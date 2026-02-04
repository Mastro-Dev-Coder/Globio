<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Setting;
use App\Models\Video;
use App\Services\GoogleAdsenseService;
use App\Services\VastService;
use App\Services\VmapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoAdsController extends Controller
{
    private $vastService;
    private $vmapService;
    private $googleAdsenseService;

    public function __construct()
    {
        $this->vastService = new VastService();
        $this->vmapService = new VmapService();
        $this->googleAdsenseService = new GoogleAdsenseService();
    }

    /**
     * Ottiene le pubblicità per un video specifico (esteso con VAST, VMAP, Google AdSense)
     */
    public function getVideoAds(Request $request, $videoId)
    {
        try {
            $video = Video::findOrFail($videoId);
            $language = $request->get('lang', 'it');

            $adSettings = $this->getAdSettings();

            $ads = [];

            $traditionalAds = $this->getTraditionalAds($video, $adSettings, $language);
            $ads = array_merge($ads, $traditionalAds);

            if ($adSettings['vast_enabled'] ?? false) {
                $vastAds = $this->getVastAds($video, $adSettings, $language);
                $ads = array_merge($ads, $vastAds);
            }

            if ($adSettings['vmap_enabled'] ?? false) {
                $vmapAds = $this->getVmapAds($video, $adSettings, $language);
                $ads = array_merge($ads, $vmapAds);
            }

            if ($adSettings['google_adsense_enabled'] ?? false) {
                $googleAds = $this->getGoogleAdSenseAds($video, $adSettings, $language);
                $ads = array_merge($ads, $googleAds);
            }

            return response()->json([
                'success' => true,
                'ads' => $ads,
                'settings' => $adSettings,
                'language' => $language
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle pubblicità: ' . $e->getMessage(),
                'video_id' => $videoId,
                'ads' => []
            ], 500);
        }
    }

    /**
     * Ottiene ads VAST per il video
     */
    public function getVastAds($video, $adSettings, $language = 'it')
    {
        try {
            $vastAds = [];
            $vastSourceType = Setting::getValue('ads_vast_source_type', 'url');
            
            // Recupera le impostazioni personalizzate per VAST
            $customClickthroughUrl = Setting::getValue('ads_vast_clickthrough_url', '');
            $customText = Setting::getValue('ads_vast_custom_text', '');
            $defaultSkipDelay = Setting::getValue('ads_skip_delay', 5);

            if ($vastSourceType === 'xml') {
                // Usa XML diretto
                $vastXml = Setting::getValue('ads_vast_xml', '');
                if (!empty($vastXml)) {
                    try {
                        $vastData = $this->vastService->parseVastXmlString($vastXml);
                        $vastPlayerAds = $this->vastService->convertVastToPlayerFormat(
                            $vastData,
                            $customClickthroughUrl ?: null,
                            $customText ?: null,
                            $defaultSkipDelay
                        );
                        $vastAds = array_merge($vastAds, $vastPlayerAds);
                    } catch (\Exception $e) {
                    }
                }
            } else {
                // Usa URL VAST
                $vastUrls = $this->getVastUrls();

                foreach ($vastUrls as $vastUrl) {
                    try {
                        $vastData = $this->vastService->fetchVastXml($vastUrl);
                        $vastPlayerAds = $this->vastService->convertVastToPlayerFormat(
                            $vastData,
                            $customClickthroughUrl ?: null,
                            $customText ?: null,
                            $defaultSkipDelay
                        );
                        $vastAds = array_merge($vastAds, $vastPlayerAds);
                    } catch (\Exception $e) {
                    }
                }
            }

            return $vastAds;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Ottiene ads VMAP per il video
     */
    public function getVmapAds($video, $adSettings, $language = 'it')
    {
        try {
            $vmapAds = [];
            $vmapSourceType = Setting::getValue('ads_vmap_source_type', 'url');

            if ($vmapSourceType === 'xml') {
                // Usa XML diretto
                $vmapXml = Setting::getValue('ads_vmap_xml', '');
                if (!empty($vmapXml)) {
                    try {
                        $vmapData = $this->vmapService->parseVmapXmlString($vmapXml);
                        $vmapPlayerAds = $this->vmapService->convertVmapToPlayerFormat($vmapData, $video->duration);
                        $vmapAds = array_merge($vmapAds, $vmapPlayerAds);
                    } catch (\Exception $e) {
                    }
                }
            } else {
                // Usa URL VMAP
                $vmapUrls = $this->getVmapUrls();

                foreach ($vmapUrls as $vmapUrl) {
                    try {
                        $vmapData = $this->vmapService->fetchVmapXml($vmapUrl);
                        $vmapPlayerAds = $this->vmapService->convertVmapToPlayerFormat($vmapData, $video->duration);
                        $vmapAds = array_merge($vmapAds, $vmapPlayerAds);
                    } catch (\Exception $e) {
                    }
                }
            }

            return $vmapAds;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Ottiene Google AdSense ads per il video
     */
    public function getGoogleAdSenseAds($video, $adSettings, $language = 'it')
    {
        try {
            $googleAds = [];

            $googleAdData = $this->googleAdsenseService->fetchGoogleAd(
                $video->id,
                $video->duration,
                $language
            );

            if ($googleAdData) {
                $googlePlayerAd = $this->googleAdsenseService->convertGoogleAdToPlayerFormat(
                    $googleAdData,
                    $video->id,
                    $language
                );
                $googleAds[] = $googlePlayerAd;
            }

            return $googleAds;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Ads tradizionali dal database (esistente)
     */
    private function getTraditionalAds($video, $adSettings, $language = 'it')
    {
        $ads = [];

        if ($adSettings['pre_roll_enabled']) {
            $preRollQuery = Advertisement::where('type', 'video')
                ->where('position', 'video_overlay')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->where('content', 'pre_roll')
                ->byLanguage($language);

            $preRollAds = $preRollQuery
                ->inRandomOrder()
                ->limit(1)
                ->get()
                ->map(function ($ad) {
                    return [
                        'id' => $ad->id,
                        'name' => $ad->name,
                        'description' => $ad->content,
                        'video_url' => $ad->code,
                        'image_url' => $ad->image_url,
                        'link_url' => $ad->link_url,
                        'position' => 'pre_roll',
                        'timestamp' => 0,
                        'duration' => 15,
                        'type' => 'traditional'
                    ];
                });

            $ads = array_merge($ads, $preRollAds->toArray());
        }

        if ($adSettings['mid_roll_enabled'] && $video->duration) {
            $midRollPositions = $adSettings['mid_roll_positions'];
            $maxAds = min($adSettings['max_ads_per_video'], count($midRollPositions));

            for ($i = 0; $i < $maxAds; $i++) {
                $position = $midRollPositions[$i];
                $timestamp = ($position / 100) * $video->duration;

                $midRollAd = Advertisement::where('type', 'video')
                    ->where('position', 'video_overlay')
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                            ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    })
                    ->where('content', 'mid_roll')
                    ->byLanguage($language)
                    ->inRandomOrder()
                    ->first();

                if ($midRollAd) {
                    $ads[] = [
                        'id' => $midRollAd->id,
                        'name' => $midRollAd->name,
                        'description' => $midRollAd->content,
                        'video_url' => $midRollAd->code,
                        'image_url' => $midRollAd->image_url,
                        'link_url' => $midRollAd->link_url,
                        'position' => 'mid_roll',
                        'timestamp' => $position,
                        'duration' => 10,
                        'type' => 'traditional'
                    ];
                }
            }
        }

        if ($adSettings['post_roll_enabled']) {
            $postRollQuery = Advertisement::where('type', 'video')
                ->where('position', 'video_overlay')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->where('content', 'post_roll')
                ->byLanguage($language);

            $postRollAds = $postRollQuery
                ->inRandomOrder()
                ->limit(1)
                ->get()
                ->map(function ($ad) use ($video) {
                    return [
                        'id' => $ad->id,
                        'name' => $ad->name,
                        'description' => $ad->content,
                        'video_url' => $ad->code,
                        'image_url' => $ad->image_url,
                        'link_url' => $ad->link_url,
                        'position' => 'post_roll',
                        'timestamp' => 100,
                        'duration' => 15,
                        'type' => 'traditional'
                    ];
                });

            $ads = array_merge($ads, $postRollAds->toArray());
        }

        return $ads;
    }

    /**
     * Traccia le interazioni con le ads (esteso)
     */
    public function trackAdInteraction(Request $request)
    {
        try {
            $request->validate([
                'advertisement_id' => 'required|exists:advertisements,id',
                'action' => 'required|in:view,click,skip,close,complete',
                'video_id' => 'required|exists:videos,id',
                'ad_type' => 'nullable|in:traditional,vast,vmap,google_adsense'
            ]);

            $advertisement = Advertisement::find($request->advertisement_id);
            $adType = $request->ad_type ?? 'traditional';

            if ($adType === 'traditional' && $advertisement) {
                switch ($request->action) {
                    case 'view':
                        $advertisement->incrementViews();
                        break;
                    case 'click':
                    case 'complete':
                        $advertisement->incrementClicks();
                        break;
                }
            }

            if ($adType === 'google_adsense') {
                $this->googleAdsenseService->trackGoogleAdInteraction(
                    ['click_url' => $advertisement->link_url ?? ''],
                    $request->action,
                    $request->video_id
                );
            }

            DB::table('ad_interactions')->insert([
                'advertisement_id' => $request->advertisement_id,
                'video_id' => $request->video_id,
                'action' => $request->action,
                'ad_type' => $adType,
                'user_id' => auth()->guard()->check() ? auth()->guard()->id() : null,
                'created_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Interazione tracciata con successo'
            ]);
        } catch (\Exception $e) {
            Log::error('Errore nel tracciamento delle ads: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Errore nel tracciamento'
            ], 500);
        }
    }

    /**
     * Endpoint per testare VAST ads
     */
    public function testVastAds(Request $request)
    {
        try {
            $vastUrl = $request->get('vast_url', 'https://pubads.g.doubleclick.net/gampad/ads?sz=640x480&iu=/124319096/external/single_ad_samples&ciu_szs=300x250&impl=s&gdfp_req=1&env=vp&output=vast&unviewed_position_start=1&cust_params=deployment%3Ddevsite%26sample_ct%3Dlinear&correlator=');

            $vastData = $this->vastService->fetchVastXml($vastUrl);
            $playerAds = $this->vastService->convertVastToPlayerFormat($vastData);

            return response()->json([
                'success' => true,
                'vast_data' => $vastData,
                'player_ads' => $playerAds,
                'vast_url' => $vastUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore test VAST: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint per testare VMAP ads
     */
    public function testVmapAds(Request $request)
    {
        try {
            $vmapUrl = $request->get('vmap_url');

            if (!$vmapUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL VMAP richiesto'
                ], 400);
            }

            $vmapData = $this->vmapService->fetchVmapXml($vmapUrl);
            $playerAds = $this->vmapService->convertVmapToPlayerFormat($vmapData);

            return response()->json([
                'success' => true,
                'vmap_data' => $vmapData,
                'player_ads' => $playerAds,
                'vmap_url' => $vmapUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore test VMAP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint per testare Google AdSense
     */
    public function testGoogleAdSense(Request $request)
    {
        try {
            $videoId = $request->get('video_id', 'test_video');
            $videoDuration = $request->get('video_duration', 300);

            $googleAdData = $this->googleAdsenseService->fetchGoogleAd($videoId, $videoDuration);
            $playerAd = $this->googleAdsenseService->convertGoogleAdToPlayerFormat($googleAdData, $videoId);

            return response()->json([
                'success' => true,
                'google_ad_data' => $googleAdData,
                'player_ad' => $playerAd,
                'video_id' => $videoId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore test Google AdSense: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottiene le impostazioni delle ads (esteso)
     */
    private function getAdSettings()
    {
        return [
            'pre_roll_enabled' => Setting::getBooleanValue('ads_pre_roll_enabled', true),
            'mid_roll_enabled' => Setting::getBooleanValue('ads_mid_roll_enabled', true),
            'post_roll_enabled' => Setting::getBooleanValue('ads_post_roll_enabled', true),
            'skip_delay' => (int) Setting::getValue('ads_skip_delay', 5),
            'mid_roll_positions' => explode(',', Setting::getValue('ads_mid_roll_positions', '25,50,75')),
            'max_ads_per_video' => (int) Setting::getValue('ads_max_per_video', 3),
            'frequency_cap' => (int) Setting::getValue('ads_frequency_cap', 1),

            'vast_enabled' => Setting::getBooleanValue('ads_vast_enabled', false),
            'vmap_enabled' => Setting::getBooleanValue('ads_vmap_enabled', false),
            'google_adsense_enabled' => Setting::getBooleanValue('ads_google_adsense_enabled', false),
        ];
    }

    /**
     * Ottiene gli URL VAST configurati
     */
    private function getVastUrls()
    {
        $vastUrls = Setting::getValue('ads_vast_urls', '');
        return array_filter(explode("\n", $vastUrls));
    }

    /**
     * Ottiene gli URL VMAP configurati
     */
    private function getVmapUrls()
    {
        $vmapUrls = Setting::getValue('ads_vmap_urls', '');
        return array_filter(explode("\n", $vmapUrls));
    }

    /**
     * Endpoint per tracciare le analytics delle ads
     */
    public function trackAdAnalytics(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'video_id' => 'required|exists:videos,id',
                'session_id' => 'required|string',
                'timestamp' => 'required|integer',
                'ad_id' => 'nullable|string',
                'ad_type' => 'nullable|string',
                'position' => 'nullable|string',
                'duration' => 'nullable|integer',
                'load_time' => 'nullable|integer'
            ]);

            DB::table('ad_analytics')->insert([
                'event_type' => $request->type,
                'video_id' => $request->video_id,
                'session_id' => $request->session_id,
                'timestamp' => $request->timestamp,
                'ad_id' => $request->ad_id,
                'ad_type' => $request->ad_type,
                'position' => $request->position,
                'duration' => $request->duration,
                'load_time' => $request->load_time,
                'user_id' => auth()->guard()->check() ? auth()->guard()->id() : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            Log::info('Ad analytics tracked', [
                'type' => $request->type,
                'video_id' => $request->video_id,
                'session_id' => $request->session_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Analytics tracciate con successo'
            ]);
        } catch (\Exception $e) {
            Log::error('Errore tracciamento analytics ads', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel tracciamento analytics'
            ], 500);
        }
    }

    /**
     * Ottiene le statistiche delle ads per un video
     */
    public function getAdStatistics($videoId)
    {
        try {
            $video = Video::findOrFail($videoId);

            $stats = DB::table('ad_analytics')
                ->where('video_id', $videoId)
                ->selectRaw('
                    event_type,
                    COUNT(*) as count,
                    AVG(load_time) as avg_load_time
                ')
                ->groupBy('event_type')
                ->get();

            $summary = [
                'total_events' => DB::table('ad_analytics')->where('video_id', $videoId)->count(),
                'unique_sessions' => DB::table('ad_analytics')->where('video_id', $videoId)->distinct('session_id')->count('session_id'),
                'event_breakdown' => $stats
            ];

            return response()->json([
                'success' => true,
                'statistics' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Errore recupero statistiche ads', [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero statistiche'
            ], 500);
        }
    }
}
