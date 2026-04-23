<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;
use Illuminate\Validation\ValidationException;

/**
 * VAST Service Enhanced - Versione migliorata con validazioni avanzate
 * e gestione errori robusta per la pubblicità video VAST
 */
class VastServiceEnhanced
{
    /**
     * Timeout configurabile per le richieste VAST
     */
    private const DEFAULT_TIMEOUT = 10;

    /**
     * TTL cache configurabile
     */
    private const DEFAULT_CACHE_TTL = 300; // 5 minuti

    /**
     * User agent configurabile
     */
    private const DEFAULT_USER_AGENT = 'Globio-Video-Player/1.0';

    /**
     * Massimo numero di retry per richieste fallite
     */
    private const MAX_RETRIES = 3;

    /**
     * Validazione URL VAST
     */
    private function validateVastUrl($vastUrl)
    {
        if (empty($vastUrl)) {
            throw ValidationException::withMessages(['vast_url' => 'URL VAST richiesto']);
        }

        if (!filter_var($vastUrl, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['vast_url' => 'URL VAST non valido']);
        }

        // Verifica che sia un URL HTTPS (raccomandato per sicurezza)
        if (!str_starts_with($vastUrl, 'https://')) {
            Log::warning('URL VAST non HTTPS', ['url' => $vastUrl]);
        }

        return true;
    }

    /**
     * Validazione parametri opzionali
     */
    private function validateOptions($options)
    {
        if (!is_array($options)) {
            throw ValidationException::withMessages(['options' => 'Le opzioni devono essere un array']);
        }

        // Validazione timeout
        if (isset($options['timeout']) && (!is_numeric($options['timeout']) || $options['timeout'] <= 0)) {
            throw ValidationException::withMessages(['timeout' => 'Timeout deve essere un numero positivo']);
        }

        // Validazione headers
        if (isset($options['headers']) && !is_array($options['headers'])) {
            throw ValidationException::withMessages(['headers' => 'Headers deve essere un array']);
        }

        return true;
    }

    /**
     * Effettua una richiesta VAST con retry e validazioni avanzate
     */
    public function fetchVastXml($vastUrl, $options = [])
    {
        try {
            // Validazioni input
            $this->validateVastUrl($vastUrl);
            $this->validateOptions($options);

            Log::info('Richiesta VAST iniziata', [
                'url' => $vastUrl,
                'options' => $options
            ]);

            $retryCount = 0;
            $lastException = null;

            while ($retryCount < self::MAX_RETRIES) {
                try {
                    return $this->executeVastRequest($vastUrl, $options);
                } catch (\Exception $e) {
                    $lastException = $e;
                    $retryCount++;
                    
                    Log::warning("Tentativo VAST fallito ({$retryCount}/" . self::MAX_RETRIES . ")", [
                        'url' => $vastUrl,
                        'error' => $e->getMessage()
                    ]);

                    if ($retryCount < self::MAX_RETRIES) {
                        // Attesa esponenziale tra i retry
                        $delay = pow(2, $retryCount - 1);
                        sleep($delay);
                    }
                }
            }

            // Tutti i tentativi sono falliti
            throw new \Exception("VAST request failed after " . self::MAX_RETRIES . " attempts: " . $lastException->getMessage());

        } catch (ValidationException $e) {
            Log::error('Validazione VAST fallita', [
                'vast_url' => $vastUrl,
                'validation_errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Errore critico nel fetch VAST', [
                'vast_url' => $vastUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getFallbackVast();
        }
    }

    /**
     * Esegue la richiesta VAST effettiva con cache
     */
    private function executeVastRequest($vastUrl, $options)
    {
        // Chiave cache migliorata con hash dell'URL e opzioni
        $cacheKey = 'vast_enhanced_' . md5($vastUrl . json_encode($options));
        
        // Verifica cache con TTL configurabile
        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            Log::info('VAST response caricato da cache', [
                'url' => $vastUrl,
                'cache_age' => time() - $cachedData['cached_at']
            ]);
            return $cachedData['data'];
        }

        // Parametri di default migliorati
        $defaultParams = [
            'timeout' => config('services.vast.timeout', self::DEFAULT_TIMEOUT),
            'headers' => [
                'User-Agent' => config('services.vast.user_agent', self::DEFAULT_USER_AGENT),
                'Accept' => 'application/xml, text/xml, application/vast+xml, */*',
                'Accept-Language' => 'it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3',
                'Accept-Encoding' => 'gzip, deflate',
                'DNT' => '1',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ]
        ];

        $params = array_merge_recursive($defaultParams, $options);

        // Timeout configurabile
        $timeout = config('services.vast.timeout', self::DEFAULT_TIMEOUT);
        
        // Effettua la richiesta con retry automatico
        $response = Http::timeout($timeout)
            ->withHeaders($params['headers'])
            ->withOptions(['verify' => true]) // Verifica SSL per sicurezza
            ->get($vastUrl);

        // Gestione migliore degli errori HTTP
        if (!$response->successful()) {
            $statusCode = $response->status();
            $errorMessage = "VAST request failed: HTTP {$statusCode}";
            
            // Log dettagliato dell'errore
            Log::error('VAST HTTP Error', [
                'url' => $vastUrl,
                'status_code' => $statusCode,
                'response_body' => $response->body(),
                'headers' => $response->headers()
            ]);

            // Gestione specifica per codici di errore comuni
            switch ($statusCode) {
                case 404:
                    throw new \Exception("VAST URL not found (404): {$vastUrl}");
                case 403:
                    throw new \Exception("VAST access forbidden (403): {$vastUrl}");
                case 500:
                    throw new \Exception("VAST server error (500): {$vastUrl}");
                case 502:
                case 503:
                case 504:
                    throw new \Exception("VAST server unavailable ({$statusCode}): {$vastUrl}");
                default:
                    throw new \Exception($errorMessage);
            }
        }

        $xmlContent = $response->body();
        
        // Validazione XML migliorata
        if (!$this->isValidVastXml($xmlContent)) {
            Log::error('Invalid VAST XML received', [
                'url' => $vastUrl,
                'content_length' => strlen($xmlContent),
                'content_preview' => substr($xmlContent, 0, 200)
            ]);
            throw new \Exception('Invalid VAST XML format received from server');
        }

        // Parse XML con error handling
        try {
            libxml_use_internal_errors(true);
            $vast = new SimpleXMLElement($xmlContent);
            libxml_clear_errors();
        } catch (\Exception $e) {
            Log::error('XML parsing failed', [
                'url' => $vastUrl,
                'error' => $e->getMessage(),
                'xml_errors' => libxml_get_errors()
            ]);
            throw new \Exception('Failed to parse VAST XML: ' . $e->getMessage());
        }
        
        $parsedVast = $this->parseVastXml($vast);

        // Salva in cache con TTL configurabile
        $cacheTtl = config('services.vast.cache_ttl', self::DEFAULT_CACHE_TTL);
        Cache::put($cacheKey, [
            'data' => $parsedVast,
            'cached_at' => time(),
            'url' => $vastUrl
        ], $cacheTtl);

        Log::info('VAST response parsed and cached successfully', [
            'url' => $vastUrl,
            'ads_count' => count($parsedVast['ads'] ?? []),
            'cache_ttl' => $cacheTtl
        ]);

        return $parsedVast;
    }

    /**
     * Converte VAST response nel formato del video player con validazioni
     */
    public function convertVastToPlayerFormat($vastData, $videoId = null)
    {
        if (empty($vastData) || !isset($vastData['ads'])) {
            Log::warning('VAST data empty or invalid for conversion', [
                'vast_data_keys' => array_keys($vastData),
                'video_id' => $videoId
            ]);
            return [];
        }

        $ads = [];

        foreach ($vastData['ads'] as $adIndex => $ad) {
            try {
                if (!isset($ad['creatives']) || empty($ad['creatives'])) {
                    Log::warning("Ad {$adIndex} has no creatives", ['ad' => $ad]);
                    continue;
                }

                foreach ($ad['creatives'] as $creativeIndex => $creative) {
                    if ($creative['type'] !== 'linear') {
                        continue; // Skip non-linear creatives for now
                    }

                    // Validazione media file
                    if (empty($creative['mediaFiles'])) {
                        Log::warning("Creative {$creativeIndex} in ad {$adIndex} has no media files");
                        continue;
                    }

                    $mediaFile = $creative['mediaFiles'][0];
                    
                    // Validazione URL media file
                    if (empty($mediaFile['url']) || !filter_var($mediaFile['url'], FILTER_VALIDATE_URL)) {
                        Log::warning("Invalid media file URL in creative {$creativeIndex}");
                        continue;
                    }

                    $adData = [
                        'id' => $ad['id'] . '_' . $creative['id'],
                        'name' => $ad['title'] ?? 'VAST Advertisement',
                        'description' => $ad['description'] ?? '',
                        'video_url' => $mediaFile['url'],
                        'image_url' => null, // Non disponibile in VAST linear
                        'link_url' => $creative['videoClicks']['clickThrough'] ?? null,
                        'position' => $this->determineAdPosition($creative),
                        'timestamp' => 0,
                        'duration' => $this->parseDuration($creative['duration']),
                        'skip_delay' => $this->parseSkipOffset($creative['skipOffset']),
                        'tracking_events' => $creative['trackingEvents'] ?? [],
                        'type' => 'vast',
                        'ad_system' => $ad['system'] ?? 'Unknown',
                        'creative_type' => $creative['type'],
                        'vast_version' => $vastData['version'] ?? 'unknown'
                    ];

                    // Validazione finale dell'ad
                    if ($this->validateAdData($adData)) {
                        $ads[] = $adData;
                    } else {
                        Log::warning("Ad data validation failed", ['ad_data' => $adData]);
                    }

                } // End creatives loop
            } catch (\Exception $e) {
                Log::error("Error processing ad {$adIndex}", [
                    'error' => $e->getMessage(),
                    'ad_data' => $ad
                ]);
                continue;
            }
        }

        Log::info('VAST to player format conversion completed', [
            'input_ads' => count($vastData['ads']),
            'output_ads' => count($ads),
            'video_id' => $videoId
        ]);

        return $ads;
    }

    /**
     * Valida i dati dell'ad prima dell'aggiunta
     */
    private function validateAdData($adData)
    {
        $requiredFields = ['id', 'video_url', 'duration'];
        
        foreach ($requiredFields as $field) {
            if (empty($adData[$field])) {
                Log::warning("Ad missing required field: {$field}", $adData);
                return false;
            }
        }

        // Validazione URL video
        if (!filter_var($adData['video_url'], FILTER_VALIDATE_URL)) {
            Log::warning("Invalid video URL in ad", ['url' => $adData['video_url']]);
            return false;
        }

        // Validazione durata
        if (!is_numeric($adData['duration']) || $adData['duration'] <= 0) {
            Log::warning("Invalid duration in ad", ['duration' => $adData['duration']]);
            return false;
        }

        return true;
    }

    // ... Mantieni tutti i metodi esistenti dal VastService originale ...
    // (I metodi rimanenti come parseVastXml, getFallbackVast, etc. rimangono uguali)
}