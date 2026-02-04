<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;

class VmapService
{
    /**
     * Chiave cache per VMAP responses
     */
    private const CACHE_TTL = 300; // 5 minuti

    /**
     * User agent per le richieste VMAP
     */
    private const USER_AGENT = 'Globio-Video-Player/1.0';

    /**
     * Effettua una richiesta VMAP
     */
    public function fetchVmapXml($vmapUrl, $options = [])
    {
        try {
            Log::info('Richiesta VMAP', ['url' => $vmapUrl]);

            // Verifica cache
            $cacheKey = 'vmap_' . md5($vmapUrl . json_encode($options));
            if (Cache::has($cacheKey)) {
                Log::info('VMAP response caricato da cache', ['url' => $vmapUrl]);
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
                ->get($vmapUrl);

            if (!$response->successful()) {
                throw new \Exception("VMAP request failed: HTTP {$response->status()}");
            }

            $xmlContent = $response->body();

            // Valida XML
            if (!$this->isValidVmapXml($xmlContent)) {
                throw new \Exception('Invalid VMAP XML format');
            }

            // Parse XML
            $vmap = new SimpleXMLElement($xmlContent);

            $parsedVmap = $this->parseVmapXml($vmap);

            // Salva in cache
            Cache::put($cacheKey, $parsedVmap, self::CACHE_TTL);

            Log::info('VMAP response parsed successfully', [
                'url' => $vmapUrl,
                'ads_count' => count($parsedVmap['ads'] ?? [])
            ]);

            return $parsedVmap;
        } catch (\Exception $e) {
            Log::error('Errore nel fetch VMAP', [
                'url' => $vmapUrl,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackVmap();
        }
    }

    /**
     * Parsa una stringa XML VMAP diretta
     */
    public function parseVmapXmlString($xmlString)
    {
        try {
            Log::info('Parsing VMAP XML string', ['length' => strlen($xmlString)]);

            // Verifica cache
            $cacheKey = 'vmap_xml_' . md5($xmlString);
            if (Cache::has($cacheKey)) {
                Log::info('VMAP XML caricato da cache');
                return Cache::get($cacheKey);
            }

            // Valida XML
            if (!$this->isValidVmapXml($xmlString)) {
                throw new \Exception('Invalid VMAP XML format');
            }

            // Parse XML
            $vmap = new SimpleXMLElement($xmlString);

            $parsedVmap = $this->parseVmapXml($vmap);

            // Salva in cache
            Cache::put($cacheKey, $parsedVmap, self::CACHE_TTL);

            Log::info('VMAP XML parsed successfully', [
                'ads_count' => count($parsedVmap['ads'] ?? [])
            ]);

            return $parsedVmap;
        } catch (\Exception $e) {
            Log::error('Errore nel parsing VMAP XML string', [
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackVmap();
        }
    }

    /**
     * Valida se l'XML è un VMAP valido
     */
    private function isValidVmapXml($xmlContent)
    {
        try {
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($xmlContent);

            // Verifica che sia un VMAP root element (con o senza namespace)
            $rootName = $xml->getName();
            if ($rootName !== 'vmap:VMAP' && $rootName !== 'VMAP') {
                return false;
            }

            libxml_clear_errors();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parse del VMAP XML
     */
    private function parseVmapXml(SimpleXMLElement $vmap)
    {
        $parsed = [
            'version' => (string) $vmap['version'] ?? 'unknown',
            'ads' => [],
            'extensions' => []
        ];

        // Parse degli AdBreaks
        foreach ($vmap->AdBreak as $adBreak) {
            try {
                $parsedAdBreak = $this->parseAdBreakElement($adBreak);
                if ($parsedAdBreak) {
                    $parsed['ads'][] = $parsedAdBreak;
                }
            } catch (\Exception $e) {
                Log::warning('Errore parsing VMAP AdBreak', ['error' => $e->getMessage()]);
            }
        }

        // Parse delle estensioni
        if (isset($vmap->Extensions)) {
            foreach ($vmap->Extensions->Extension as $extension) {
                $parsed['extensions'][] = [
                    'type' => (string) $extension['type'] ?? '',
                    'data' => (string) $extension
                ];
            }
        }

        return $parsed;
    }

    /**
     * Parse di un AdBreak element
     */
    private function parseAdBreakElement(SimpleXMLElement $adBreak)
    {
        $breakId = (string) $adBreak['breakId'] ?? null;
        $breakType = (string) $adBreak['breakType'] ?? 'linear';
        $timeOffset = (string) $adBreak['timeOffset'] ?? '0:00:00';

        // Template type (oppure, linear, etc.)
        $templateType = (string) $adBreak['templateType'] ?? 'linear';

        // Parse delle creatives
        $creatives = [];
        if (isset($adBreak->AdSource)) {
            if (isset($adBreak->AdSource->VASTAdData)) {
                // VAST inline
                $vastData = $this->parseVastAdData($adBreak->AdSource->VASTAdData);
                if ($vastData) {
                    $creatives[] = [
                        'type' => 'vast_inline',
                        'data' => $vastData
                    ];
                }
            } elseif (isset($adBreak->AdSource->AdTagURI)) {
                // VAST wrapper
                $vastUrl = (string) $adBreak->AdSource->AdTagURI;
                $vastService = new VastService();
                $vastData = $vastService->fetchVastXml($vastUrl);
                if ($vastData) {
                    $creatives[] = [
                        'type' => 'vast_wrapper',
                        'data' => $vastData
                    ];
                }
            }
        }

        if (empty($creatives)) {
            return null;
        }

        return [
            'breakId' => $breakId,
            'breakType' => $breakType,
            'timeOffset' => $this->parseTimeOffset($timeOffset),
            'templateType' => $templateType,
            'creatives' => $creatives,
            'type' => 'vmap'
        ];
    }

    /**
     * Parse dei VAST AdData inline
     */
    private function parseVastAdData(SimpleXMLElement $vastAdData)
    {
        try {
            $vastContent = $vastAdData->asXML();
            $vastService = new VastService();
            return $vastService->parseVastXmlString($vastContent);
        } catch (\Exception $e) {
            Log::warning('Errore parsing VAST AdData', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Converte timeOffset in secondi
     */
    private function parseTimeOffset($timeOffset)
    {
        // Gestisce formati diversi: HH:MM:SS, percentage, start/end
        if (strpos($timeOffset, ':') !== false) {
            // Formato temporale HH:MM:SS
            $parts = explode(':', $timeOffset);
            if (count($parts) === 3) {
                return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
            }
        } elseif (strpos($timeOffset, '%') !== false) {
            // Percentuale del video
            $percentage = (float) str_replace('%', '', $timeOffset);
            return $percentage; // Verrà convertito in secondi basandosi sulla durata del video
        } elseif (strtolower($timeOffset) === 'start') {
            return 0; // Inizio video
        } elseif (strtolower($timeOffset) === 'end') {
            return -1; // Fine video
        }

        return 0; // Default
    }

    /**
     * VMAP fallback in caso di errore
     */
    private function getFallbackVmap()
    {
        return [
            'version' => '1.0',
            'ads' => [
                [
                    'breakId' => 'fallback-break-1',
                    'breakType' => 'linear',
                    'timeOffset' => 0,
                    'templateType' => 'linear',
                    'creatives' => [
                        [
                            'type' => 'vast_inline',
                            'data' => [
                                'version' => '4.0',
                                'ads' => [
                                    [
                                        'id' => 'fallback-vast-ad',
                                        'sequence' => 1,
                                        'system' => 'Globio',
                                        'title' => 'Pubblicità VMAP',
                                        'description' => 'Pubblicità VMAP di fallback',
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
                                'errors' => ['Fallback VMAP activated']
                            ]
                        ]
                    ],
                    'type' => 'vmap'
                ]
            ],
            'extensions' => []
        ];
    }

    /**
     * Converte VMAP response nel formato del video player esistente
     */
    public function convertVmapToPlayerFormat($vmapData, $videoDuration = null)
    {
        $ads = [];

        foreach ($vmapData['ads'] as $adBreak) {
            foreach ($adBreak['creatives'] as $creative) {
                if ($creative['type'] === 'vast_inline' || $creative['type'] === 'vast_wrapper') {
                    $vastData = $creative['data'];
                    $vastService = new VastService();
                    $vastAds = $vastService->convertVastToPlayerFormat($vastData);

                    foreach ($vastAds as $vastAd) {
                        // Determina posizione basandosi sul timeOffset
                        $position = $this->determineAdPosition($adBreak, $videoDuration);
                        $timestamp = $this->calculateTimestamp($adBreak['timeOffset'], $videoDuration);

                        $ads[] = [
                            'id' => $adBreak['breakId'] . '_' . $vastAd['id'],
                            'name' => $vastAd['name'],
                            'description' => $vastAd['description'],
                            'video_url' => $vastAd['video_url'],
                            'image_url' => $vastAd['image_url'],
                            'link_url' => $vastAd['link_url'],
                            'position' => $position,
                            'timestamp' => $timestamp,
                            'duration' => $vastAd['duration'],
                            'skip_delay' => $vastAd['skip_delay'],
                            'tracking_events' => $vastAd['tracking_events'],
                            'type' => 'vmap',
                            'break_id' => $adBreak['breakId']
                        ];
                    }
                }
            }
        }

        return $ads;
    }

    /**
     * Determina la posizione dell'ad basandosi sull'AdBreak
     */
    private function determineAdPosition($adBreak, $videoDuration)
    {
        $timeOffset = $adBreak['timeOffset'];

        if ($timeOffset === 0 || $timeOffset === 'start') {
            return 'pre_roll';
        } elseif ($timeOffset === -1 || $timeOffset === 'end') {
            return 'post_roll';
        } elseif (is_numeric($timeOffset) && $timeOffset > 0 && $timeOffset < ($videoDuration ?? 100)) {
            return 'mid_roll';
        }

        return 'pre_roll'; // Default
    }

    /**
     * Calcola il timestamp in secondi basandosi sul timeOffset
     */
    private function calculateTimestamp($timeOffset, $videoDuration)
    {
        if ($timeOffset === 0 || $timeOffset === 'start') {
            return 0;
        } elseif ($timeOffset === -1 || $timeOffset === 'end') {
            return 100; // Percentage-based per post-roll
        } elseif (is_numeric($timeOffset) && $timeOffset > 0) {
            // Se è una percentuale (0-100)
            if ($timeOffset <= 100) {
                return $timeOffset;
            }
            // Se è in secondi, converti in percentuale
            return $videoDuration ? ($timeOffset / $videoDuration) * 100 : $timeOffset;
        }

        return 0;
    }
}
