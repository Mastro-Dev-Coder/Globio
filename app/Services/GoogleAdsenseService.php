<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleAdsenseService
{
    /**
     * Chiave cache per Google AdSense responses
     */
    private const CACHE_TTL = 300; // 5 minuti

    /**
     * User agent per le richieste Google AdSense
     */
    private const USER_AGENT = 'Globio-Video-Player/1.0';

    /**
     * Google AdSense API endpoints
     */
    private const ADX_ENDPOINT = 'https://googleads.g.doubleclick.net/pagead/ads';
    private const AD_SENSE_ENDPOINT = 'https://googleads.g.doubleclick.net/mads/gms';

    /**
     * Configurazione Google AdSense
     */
    private $config;

    public function __construct()
    {
        $this->config = [
            'ad_slot' => config('services.google_adsense.ad_slot', ''),
            'ad_client' => config('services.google_adsense.ad_client', ''),
            'ad_format' => config('services.google_adsense.ad_format', 'video'),
            'ad_theme' => config('services.google_adsense.ad_theme', 'dark'),
            'ad_language' => config('services.google_adsense.ad_language', 'it'),
            'ad_platform' => config('services.google_adsense.ad_platform', 'mobile'),
        ];
    }

    /**
     * Genera una richiesta Google AdSense per video
     */
    public function generateAdRequest($videoId, $videoDuration = null, $language = 'it', $userAgent = null)
    {
        try {
            Log::info('Generazione richiesta Google AdSense', [
                'video_id' => $videoId,
                'duration' => $videoDuration,
                'language' => $language
            ]);

            // Parametri per la richiesta AdSense
            $params = [
                'ad_slot' => $this->config['ad_slot'],
                'ad_client' => $this->config['ad_client'],
                'ad_format' => $this->config['ad_format'],
                'ad_theme' => $this->config['ad_theme'],
                'ad_language' => $language,
                'ad_platform' => $this->config['ad_platform'],
                'video_id' => $videoId,
                'video_duration' => $videoDuration,
                'timestamp' => time(),
                'random' => rand(1000000, 9999999)
            ];

            // Parametri aggiuntivi per video ads
            if ($videoDuration) {
                $params['video_content_id'] = 'video_' . $videoId;
                $params['video_content_category'] = 'entertainment';
            }

            return $params;

        } catch (\Exception $e) {
            Log::error('Errore generazione richiesta Google AdSense', [
                'video_id' => $videoId,
                'language' => $language,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackAdParams($videoId, $language);
        }
    }

    /**
     * Effettua una richiesta Google AdSense
     */
    public function fetchGoogleAd($videoId, $videoDuration = null, $language = 'it', $options = [])
    {
        try {
            // Genera parametri richiesta
            $params = $this->generateAdRequest($videoId, $videoDuration, $language);
            
            // Chiave cache
            $cacheKey = 'google_adsense_' . md5($videoId . $language . json_encode($params));
            
            // Verifica cache
            if (Cache::has($cacheKey)) {
                Log::info('Google AdSense response caricato da cache', ['video_id' => $videoId, 'language' => $language]);
                return Cache::get($cacheKey);
            }

            // Headers per la richiesta
            $headers = [
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => $language . '-' . strtoupper($language) . ',' . $language . ';q=0.8,en-US;q=0.5,en;q=0.3',
                'Accept-Encoding' => 'gzip, deflate',
                'DNT' => '1',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1'
            ];

            // Effettua la richiesta
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->withQueryParameters($params)
                ->get(self::ADX_ENDPOINT);

            if (!$response->successful()) {
                throw new \Exception("Google AdSense request failed: HTTP {$response->status()}");
            }

            $htmlContent = $response->body();
            
            // Parse del contenuto HTML per estrarre dati AdSense
            $parsedAd = $this->parseGoogleAdContent($htmlContent, $params);

            // Salva in cache
            if ($parsedAd) {
                Cache::put($cacheKey, $parsedAd, self::CACHE_TTL);
            }

            Log::info('Google AdSense response parsed successfully', [
                'video_id' => $videoId,
                'language' => $language,
                'ad_found' => !empty($parsedAd)
            ]);

            return $parsedAd ?: $this->getFallbackAd($videoId, $language);

        } catch (\Exception $e) {
            Log::error('Errore nel fetch Google AdSense', [
                'video_id' => $videoId,
                'language' => $language,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackAd($videoId, $language);
        }
    }

    /**
     * Parse del contenuto HTML Google AdSense
     */
    private function parseGoogleAdContent($htmlContent, $params)
    {
        try {
            $adData = [];

            // Cerca script con dati AdSense
            preg_match_all('/google_ad_format\s*=\s*["\']([^"\']+)["\']/i', $htmlContent, $formats);
            preg_match_all('/google_ad_width\s*=\s*["\']?(\d+)["\']?/i', $htmlContent, $widths);
            preg_match_all('/google_ad_height\s*=\s*["\']?(\d+)["\']?/i', $htmlContent, $heights);
            preg_match_all('/google_responsive_formats\s*=\s*["\']([^"\']+)["\']/i', $htmlContent, $responsive);

            // Cerca iframe AdSense
            preg_match_all('/<iframe[^>]+src=["\']([^"\']*googleads[^"\']*)["\'][^>]*>/i', $htmlContent, $iframes);

            // Cerca video URLs nei JavaScript
            preg_match_all('/video_url["\']?\s*:\s*["\']([^"\']+\.mp4[^"\']*)["\']/i', $htmlContent, $videoUrls);
            preg_match_all("/'(https:\/\/[^']*\.mp4[^']*)'/i", $htmlContent, $mp4Urls);

            // Estrae informazioni dall'iframe
            if (!empty($iframes[1])) {
                $iframeSrc = $iframes[1][0];
                $adData['iframe_url'] = $iframeSrc;
                
                // Parse dell'iframe URL per ottenere parametri
                $iframeParams = [];
                parse_str(parse_url($iframeSrc, PHP_URL_QUERY), $iframeParams);
                $adData['iframe_params'] = $iframeParams;
            }

            // Estrae video URL
            $videoUrl = null;
            if (!empty($videoUrls[1])) {
                $videoUrl = $videoUrls[1][0];
            } elseif (!empty($mp4Urls[1])) {
                $videoUrl = $mp4Urls[1][0];
            }

            if ($videoUrl) {
                $adData['video_url'] = $videoUrl;
            }

            // Dimensioni dell'ad
            if (!empty($widths[1]) && !empty($heights[1])) {
                $adData['width'] = (int) $widths[1][0];
                $adData['height'] = (int) $heights[1][0];
            }

            // Formato
            if (!empty($formats[1])) {
                $adData['format'] = $formats[1][0];
            }

            // URL di click-through
            preg_match_all('/google_click_url\s*=\s*["\']([^"\']+)["\']/i', $htmlContent, $clickUrls);
            if (!empty($clickUrls[1])) {
                $adData['click_url'] = $clickUrls[1][0];
            }

            // Titolo/descrizione
            preg_match_all('/google_ad_client\s*=\s*["\']([^"\']+)["\']/i', $htmlContent, $clients);
            if (!empty($clients[1])) {
                $adData['client'] = $clients[1][0];
            }

            return $adData;

        } catch (\Exception $e) {
            Log::warning('Errore parsing Google AdSense content', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Genera un iframe Google AdSense
     */
    public function generateAdSenseIframe($params, $width = 640, $height = 360)
    {
        try {
            $defaultParams = array_merge([
                'google_version' => '312',
                'google_safe' => 'high',
                'google_max_num_ads' => '1',
                'google_adtest' => 'on',
                'google_content_section' => 'video_content',
                'google_content_type' => 'text/html',
                'google_captcha' => '',
                'google_cust_age' => '',
                'google_cust_gender' => '',
                'google_cust_interests' => '',
                'google_language' => 'it',
                'google_encoding' => 'utf8',
                'google_country' => 'it'
            ], $params);

            $queryString = http_build_query($defaultParams);
            
            $iframe = sprintf(
                '<iframe src="%s?%s" width="%d" height="%d" frameborder="0" marginwidth="0" marginheight="0" vspace="0" hspace="0" allowtransparency="true" scrolling="no" allowfullscreen></iframe>',
                self::AD_SENSE_ENDPOINT,
                $queryString,
                $width,
                $height
            );

            return $iframe;

        } catch (\Exception $e) {
            Log::error('Errore generazione iframe Google AdSense', ['error' => $e->getMessage()]);
            return $this->getFallbackIframe($width, $height);
        }
    }

    /**
     * Parametri fallback per Google AdSense
     */
    private function getFallbackAdParams($videoId, $language = 'it')
    {
        return [
            'ad_slot' => 'default_slot',
            'ad_client' => 'ca-google_ad_client',
            'ad_format' => 'video',
            'ad_theme' => 'dark',
            'ad_language' => $language,
            'ad_platform' => 'mobile',
            'video_id' => $videoId,
            'timestamp' => time(),
            'random' => rand(1000000, 9999999),
            'fallback' => true
        ];
    }

    /**
     * Ad fallback per Google AdSense
     */
    private function getFallbackAd($videoId, $language = 'it')
    {
        return [
            'id' => 'google_adsense_fallback_' . $videoId,
            'name' => 'Google AdSense',
            'description' => 'Pubblicità Google AdSense',
            'video_url' => '',
            'image_url' => null,
            'link_url' => 'https://www.google.com/adsense/',
            'position' => 'pre_roll',
            'timestamp' => 0,
            'duration' => 15,
            'skip_delay' => 5,
            'tracking_events' => [],
            'type' => 'google_adsense',
            'language' => $language,
            'fallback' => true
        ];
    }

    /**
     * Iframe fallback
     */
    private function getFallbackIframe($width, $height)
    {
        return sprintf(
            '<div style="width: %dpx; height: %dpx; background: linear-gradient(135deg, #4285f4 0%%, #34a853 100%%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-family: Arial, sans-serif;">
                <div style="text-align: center;">
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 8px;">Google AdSense</div>
                    <div style="font-size: 12px; opacity: 0.8;">Pubblicità di esempio</div>
                </div>
            </div>',
            $width,
            $height
        );
    }

    /**
     * Converte Google AdSense response nel formato del video player esistente
     */
    public function convertGoogleAdToPlayerFormat($adData, $videoId, $language = 'it')
    {
        if (empty($adData) || $adData['fallback'] ?? false) {
            return $this->getFallbackAd($videoId, $language);
        }

        return [
            'id' => 'google_adsense_' . $videoId . '_' . time(),
            'name' => 'Google AdSense',
            'description' => 'Pubblicità Google AdSense',
            'video_url' => $adData['video_url'] ?? null,
            'image_url' => null,
            'link_url' => $adData['click_url'] ?? null,
            'position' => 'pre_roll',
            'timestamp' => 0,
            'duration' => 15,
            'skip_delay' => 5,
            'tracking_events' => [],
            'type' => 'google_adsense',
            'language' => $language,
            'iframe_url' => $adData['iframe_url'] ?? null,
            'iframe_params' => $adData['iframe_params'] ?? []
        ];
    }

    /**
     * Traccia interazione Google AdSense
     */
    public function trackGoogleAdInteraction($adData, $action, $videoId)
    {
        try {
            // Invia pixel di tracking a Google se disponibile
            if (isset($adData['click_url'])) {
                $pixelUrl = $adData['click_url'] . '&ad_event=' . $action;
                
                // Log per debug
                Log::info('Google AdSense tracking pixel', [
                    'video_id' => $videoId,
                    'action' => $action,
                    'pixel_url' => $pixelUrl
                ]);

                // Non bloccare il player se il tracking fallisce
                Http::get($pixelUrl)->throwIf(function ($response) {
                    return false; // Non considerare errori di tracking come critici
                });
            }

            return true;

        } catch (\Exception $e) {
            Log::warning('Errore tracking Google AdSense', [
                'video_id' => $videoId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}