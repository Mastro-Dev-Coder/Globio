<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;

class VastService
{
    /**
     * Chiave cache per VAST responses
     */
    private const CACHE_TTL = 300; // 5 minuti

    /**
     * User agent per le richieste VAST
     */
    private const USER_AGENT = 'Globio-Video-Player/1.0';

    /**
     * Effettua una richiesta VAST
     */
    public function fetchVastXml($vastUrl, $options = [])
    {
        try {
            Log::info('Richiesta VAST', ['url' => $vastUrl]);

            // Verifica cache
            $cacheKey = 'vast_' . md5($vastUrl . json_encode($options));
            if (Cache::has($cacheKey)) {
                Log::info('VAST response caricato da cache', ['url' => $vastUrl]);
                return Cache::get($cacheKey);
            }

            // Parametri di default
            $defaultParams = [
                'timeout' => 10,
                'headers' => [
                    'User-Agent' => self::USER_AGENT,
                    'Accept' => 'application/xml, text/xml, */*',
                ]
            ];

            $params = array_merge_recursive($defaultParams, $options);

            // Effettua la richiesta
            $response = Http::timeout($params['timeout'])
                ->withHeaders($params['headers'])
                ->get($vastUrl);

            if (!$response->successful()) {
                throw new \Exception("VAST request failed: HTTP {$response->status()}");
            }

            $xmlContent = $response->body();
            
            // Valida XML
            if (!$this->isValidVastXml($xmlContent)) {
                throw new \Exception('Invalid VAST XML format');
            }

            // Parse XML
            $vast = new SimpleXMLElement($xmlContent);
            
            $parsedVast = $this->parseVastXml($vast);

            // Salva in cache
            Cache::put($cacheKey, $parsedVast, self::CACHE_TTL);

            Log::info('VAST response parsed successfully', [
                'url' => $vastUrl,
                'ads_count' => count($parsedVast['ads'] ?? [])
            ]);

            return $parsedVast;

        } catch (\Exception $e) {
            Log::error('Errore nel fetch VAST', [
                'url' => $vastUrl,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackVast();
        }
    }

    /**
     * Parse di un VAST XML da stringa (per uso interno)
     */
    public function parseVastXmlString($xmlContent)
    {
        try {
            if (!$this->isValidVastXml($xmlContent)) {
                throw new \Exception('Invalid VAST XML format');
            }

            $vast = new SimpleXMLElement($xmlContent);
            return $this->parseVastXml($vast);

        } catch (\Exception $e) {
            Log::error('Errore nel parsing VAST XML string', ['error' => $e->getMessage()]);
            return $this->getFallbackVast();
        }
    }

    /**
     * Valida se l'XML è un VAST valido
     */
    private function isValidVastXml($xmlContent)
    {
        try {
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($xmlContent);
            
            // Verifica che sia un VAST root element
            if ($xml->getName() !== 'VAST') {
                return false;
            }

            // Verifica che ci sia almeno un Ad
            if (!isset($xml->Ad)) {
                return false;
            }

            libxml_clear_errors();
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parse del VAST XML
     */
    private function parseVastXml(SimpleXMLElement $vast)
    {
        $parsed = [
            'version' => (string) $vast['version'] ?? 'unknown',
            'ads' => [],
            'errors' => []
        ];

        foreach ($vast->Ad as $ad) {
            try {
                $parsedAd = $this->parseAdElement($ad);
                if ($parsedAd) {
                    $parsed['ads'][] = $parsedAd;
                }
            } catch (\Exception $e) {
                $parsed['errors'][] = 'Ad parsing error: ' . $e->getMessage();
                Log::warning('Errore parsing VAST Ad', ['error' => $e->getMessage()]);
            }
        }

        return $parsed;
    }

    /**
     * Parse di un singolo Ad element
     */
    private function parseAdElement(SimpleXMLElement $ad)
    {
        $adId = (string) $ad['id'] ?? null;
        $sequence = (string) $ad['sequence'] ?? '1';

        $adSystem = (string) $ad->AdSystem ?? 'Unknown';
        $adTitle = (string) $ad->AdTitle ?? 'Advertisement';
        $description = (string) $ad->Description ?? '';

        // Parse delle creative
        $creatives = [];
        if (isset($ad->InLine)) {
            foreach ($ad->InLine->Creatives->Creative as $creative) {
                $parsedCreative = $this->parseCreativeElement($creative);
                if ($parsedCreative) {
                    $creatives[] = $parsedCreative;
                }
            }
        } elseif (isset($ad->Wrapper)) {
            // Gestisce wrapper VAST (redirect)
            $wrapperData = $this->parseWrapperElement($ad->Wrapper);
            if ($wrapperData) {
                return $wrapperData; // Restituisce i dati del wrapper direttamente
            }
        }

        if (empty($creatives)) {
            return null;
        }

        return [
            'id' => $adId,
            'sequence' => (int) $sequence,
            'system' => $adSystem,
            'title' => $adTitle,
            'description' => $description,
            'creatives' => $creatives,
            'type' => 'vast'
        ];
    }

    /**
     * Parse di un elemento Creative
     */
    private function parseCreativeElement(SimpleXMLElement $creative)
    {
        $creativeId = (string) $creative['id'] ?? null;
        $adId = (string) $creative['adId'] ?? null;
        $sequence = (string) $creative['sequence'] ?? '1';

        // Linear creatives (video ads)
        if (isset($creative->Linear)) {
            return $this->parseLinearCreative($creative->Linear, $creativeId, $adId, $sequence);
        }

        // NonLinear creatives (overlay ads)
        if (isset($creative->NonLinear)) {
            return $this->parseNonLinearCreative($creative->NonLinear, $creativeId, $adId, $sequence);
        }

        return null;
    }

    /**
     * Parse di un Linear Creative
     */
    private function parseLinearCreative(SimpleXMLElement $linear, $creativeId, $adId, $sequence)
    {
        $duration = (string) $linear->Duration ?? '0:00:00';
        $skipOffset = (string) $linear->skipoffset ?? null;

        // Media files
        $mediaFiles = [];
        foreach ($linear->MediaFiles->MediaFile as $mediaFile) {
            $mediaFiles[] = [
                'url' => (string) $mediaFile,
                'type' => (string) $mediaFile['type'] ?? '',
                'bitrate' => (string) $mediaFile['bitrate'] ?? '',
                'width' => (string) $mediaFile['width'] ?? '',
                'height' => (string) $mediaFile['height'] ?? '',
                'delivery' => (string) $mediaFile['delivery'] ?? 'progressive'
            ];
        }

        // Tracking events
        $trackingEvents = [];
        if (isset($linear->TrackingEvents)) {
            foreach ($linear->TrackingEvents->Tracking as $tracking) {
                $event = (string) $tracking['event'];
                $url = (string) $tracking;
                if (!isset($trackingEvents[$event])) {
                    $trackingEvents[$event] = [];
                }
                $trackingEvents[$event][] = $url;
            }
        }

        // Video clicks
        $videoClicks = [];
        if (isset($linear->VideoClicks)) {
            if (isset($linear->VideoClicks->ClickThrough)) {
                $videoClicks['clickThrough'] = (string) $linear->VideoClicks->ClickThrough;
            }
            if (isset($linear->VideoClicks->ClickTracking)) {
                $videoClicks['clickTracking'] = (string) $linear->VideoClicks->ClickTracking;
            }
        }

        // Icons
        $icons = [];
        if (isset($linear->Icons)) {
            foreach ($linear->Icons->Icon as $icon) {
                $icons[] = [
                    'url' => (string) $icon,
                    'width' => (string) $icon['width'] ?? '',
                    'height' => (string) $icon['height'] ?? '',
                    'xPosition' => (string) $icon['xPosition'] ?? '',
                    'yPosition' => (string) $icon['yPosition'] ?? '',
                    'duration' => (string) $icon['duration'] ?? '',
                ];
            }
        }

        return [
            'id' => $creativeId,
            'adId' => $adId,
            'sequence' => (int) $sequence,
            'type' => 'linear',
            'duration' => $duration,
            'skipOffset' => $skipOffset,
            'mediaFiles' => $mediaFiles,
            'trackingEvents' => $trackingEvents,
            'videoClicks' => $videoClicks,
            'icons' => $icons
        ];
    }

    /**
     * Parse di un NonLinear Creative
     */
    private function parseNonLinearCreative(SimpleXMLElement $nonLinear, $creativeId, $adId, $sequence)
    {
        // Static creative data
        $staticResource = null;
        if (isset($nonLinear->StaticResource)) {
            $staticResource = [
                'url' => (string) $nonLinear->StaticResource,
                'type' => (string) $nonLinear->StaticResource['creativeType'] ?? ''
            ];
        }

        // HTML creative
        $htmlResource = null;
        if (isset($nonLinear->HTMLResource)) {
            $htmlResource = [
                'content' => (string) $nonLinear->HTMLResource
            ];
        }

        return [
            'id' => $creativeId,
            'adId' => $adId,
            'sequence' => (int) $sequence,
            'type' => 'nonLinear',
            'width' => (string) $nonLinear['width'] ?? '',
            'height' => (string) $nonLinear['height'] ?? '',
            'staticResource' => $staticResource,
            'htmlResource' => $htmlResource
        ];
    }

    /**
     * Parse di un Wrapper element
     */
    private function parseWrapperElement(SimpleXMLElement $wrapper)
    {
        $vastAdTagUrl = (string) $wrapper->VASTAdTagURI;
        if (empty($vastAdTagUrl)) {
            return null;
        }

        // Effettua richiesta ricorsiva al VAST wrapper
        return $this->fetchVastXml($vastAdTagUrl);
    }

    /**
     * VAST fallback in caso di errore
     */
    private function getFallbackVast()
    {
        return [
            'version' => '4.0',
            'ads' => [
                [
                    'id' => 'fallback-ad',
                    'sequence' => 1,
                    'system' => 'Globio',
                    'title' => 'Pubblicità',
                    'description' => 'Pubblicità non disponibile',
                    'creatives' => [
                        [
                            'id' => 'fallback-linear',
                            'sequence' => 1,
                            'type' => 'linear',
                            'duration' => '0:00:15',
                            'mediaFiles' => [
                                [
                                    'url' => '',
                                    'type' => 'video/mp4',
                                    'delivery' => 'progressive'
                                ]
                            ],
                            'trackingEvents' => [],
                            'videoClicks' => [],
                            'icons' => []
                        ]
                    ],
                    'type' => 'vast'
                ]
            ],
            'errors' => ['Fallback VAST activated']
        ];
    }

    /**
     * Converte VAST response nel formato del video player esistente
     */
    public function convertVastToPlayerFormat($vastData, $customClickthroughUrl = null, $customText = null, $defaultSkipDelay = 5)
    {
        $ads = [];

        Log::info('convertVastToPlayerFormat called with:', [
            'custom_clickthrough_url' => $customClickthroughUrl,
            'custom_text' => $customText,
            'default_skip_delay' => $defaultSkipDelay,
        ]);

        foreach ($vastData['ads'] as $ad) {
            foreach ($ad['creatives'] as $creative) {
                if ($creative['type'] === 'linear') {
                    // Prende il primo media file disponibile
                    $mediaFile = !empty($creative['mediaFiles']) ? $creative['mediaFiles'][0] : null;
                    
                    if ($mediaFile) {
                        // Usa il clickthrough URL personalizzato se fornito, altrimenti usa quello del VAST
                        $linkUrl = $customClickthroughUrl ?? ($creative['videoClicks']['clickThrough'] ?? null);
                        
                        // Usa il testo personalizzato se fornito, altrimenti usa la descrizione del VAST
                        $description = $customText ?? $ad['description'];
                        
                        // Usa il skip delay personalizzato se fornito, altrimenti usa quello del VAST
                        $skipDelay = $this->parseSkipOffset($creative['skipOffset'], $defaultSkipDelay);
                        
                        $ads[] = [
                            'id' => $ad['id'] . '_' . $creative['id'],
                            'name' => $ad['title'],
                            'description' => $description,
                            'video_url' => $mediaFile['url'],
                            'image_url' => null, // Non disponibile in VAST linear
                            'link_url' => $linkUrl,
                            'position' => $this->determineAdPosition($creative),
                            'timestamp' => 0,
                            'duration' => $this->parseDuration($creative['duration']),
                            'skip_delay' => $skipDelay,
                            'tracking_events' => $creative['trackingEvents'],
                            'type' => 'vast'
                        ];
                    }
                }
            }
        }

        Log::info('convertVastToPlayerFormat returning ads:', [
            'count' => count($ads),
            'ads' => $ads,
        ]);

        return $ads;
    }

    /**
     * Determina la posizione dell'ad basandosi sui dati del creative
     */
    private function determineAdPosition($creative)
    {
        // Per ora, tutte le ads VAST vengono trattate come pre-roll
        // In futuro si può analizzare il sequence number o altri metadati
        return 'pre_roll';
    }

    /**
     * Converte duration VAST in secondi
     */
    private function parseDuration($duration)
    {
        // Formato VAST: HH:MM:SS
        $parts = explode(':', $duration);
        if (count($parts) === 3) {
            return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
        }
        return 15; // Default duration
    }

    /**
     * Converte skip offset in secondi
     */
    private function parseSkipOffset($skipOffset, $defaultSkipDelay = 5)
    {
        if (!$skipOffset) {
            return $defaultSkipDelay; // Usa il default fornito
        }

        // Se è una percentuale
        if (strpos($skipOffset, '%') !== false) {
            $percentage = (float) str_replace('%', '', $skipOffset);
            return max($defaultSkipDelay, (int) ($percentage * 0.15)); // Converte in secondi approssimativi
        }

        // Se è in formato HH:MM:SS
        $parts = explode(':', $skipOffset);
        if (count($parts) === 3) {
            return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
        }

        return $defaultSkipDelay; // Usa il default fornito
    }
}