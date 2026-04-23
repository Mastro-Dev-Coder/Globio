<?php

namespace App\Helpers;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdvertisementHelper
{
    /**
     * Ottiene le pubblicità attive per una posizione specifica
     */
    public static function getActiveAdvertisements($position, $limit = 1)
    {
        return Cache::remember(
            "advertisements_{$position}_{$limit}",
            300, // Cache per 5 minuti
            function () use ($position, $limit) {
                return Advertisement::currentlyActive()
                    ->byPosition($position)
                    ->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
            }
        );
    }

    /**
     * Incrementa le visualizzazioni di una pubblicità
     */
    public static function incrementViews($advertisementId)
    {
        try {
            Advertisement::where('id', $advertisementId)->increment('views');
            
            // Rimuovi dalla cache per aggiornare i dati
            Cache::forget("advertisements_{$advertisementId}");
        } catch (\Exception $e) {
            Log::error('Errore nell\'incremento delle visualizzazioni: ' . $e->getMessage());
        }
    }

    /**
     * Incrementa i click di una pubblicità
     */
    public static function incrementClicks($advertisementId)
    {
        try {
            Advertisement::where('id', $advertisementId)->increment('clicks');
            
            // Rimuovi dalla cache per aggiornare i dati
            Cache::forget("advertisements_{$advertisementId}");
        } catch (\Exception $e) {
            Log::error('Errore nell\'incremento dei click: ' . $e->getMessage());
        }
    }

    /**
     * Renderizza una pubblicità HTML
     */
    public static function renderAdvertisement($advertisement)
    {
        if (!$advertisement) {
            return '';
        }

        if (!$advertisement->isCurrentlyActive()) {
            return '';
        }

        // Incrementa le visualizzazioni
        self::incrementViews($advertisement->id);

        $html = '';

        switch ($advertisement->type) {
            case 'banner':
                $html = self::renderBanner($advertisement);
                break;
            case 'adsense':
                $html = self::renderAdSense($advertisement);
                break;
            case 'video':
                $html = self::renderVideo($advertisement);
                break;
        }

        return $html;
    }

    /**
     * Renderizza un banner
     */
    private static function renderBanner($advertisement)
    {
        if (!$advertisement) {
            return '';
        }

        $linkStart = $advertisement->link_url ? '<a href="' . e($advertisement->link_url) . '" target="_blank" rel="noopener">' : '';
        $linkEnd = $advertisement->link_url ? '</a>' : '';

        $content = '';
        if ($advertisement->image_url) {
            $imageUrl = asset('storage/' . $advertisement->image_url);
            $alt = e($advertisement->content ?: $advertisement->name ?: '');
            $content = "<img src=\"{$imageUrl}\" alt=\"{$alt}\" class=\"w-full h-auto\">";
        }

        return "
        <div class=\"advertisement-banner advertisement-{$advertisement->position}\" data-advertisement-id=\"{$advertisement->id}\">
            {$linkStart}
                {$content}
            {$linkEnd}
        </div>";
    }

    /**
     * Renderizza una pubblicità AdSense
     */
    private static function renderAdSense($advertisement)
    {
        if (!$advertisement || !$advertisement->code) {
            return '';
        }

        return "
        <div class=\"advertisement-adsense advertisement-{$advertisement->position}\" data-advertisement-id=\"{$advertisement->id}\">
            {$advertisement->code}
        </div>";
    }

    /**
     * Renderizza una pubblicità video
     */
    private static function renderVideo($advertisement)
    {
        if (!$advertisement || !$advertisement->code) {
            return '';
        }

        return "
        <div class=\"advertisement-video advertisement-{$advertisement->position}\" data-advertisement-id=\"{$advertisement->id}\">
            {$advertisement->code}
        </div>";
    }

    /**
     * Genera il JavaScript per tracciare i click
     */
    public static function generateClickTrackingScript()
    {
        return "
        <script>
        document.addEventListener('click', function(e) {
            const adElement = e.target.closest('[data-advertisement-id]');
            if (adElement) {
                const adId = adElement.getAttribute('data-advertisement-id');
                
                // Traccia il click via AJAX
                fetch('/api/advertisement/' + adId + '/click', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                }).catch(error => console.log('Click tracking error:', error));
            }
        });
        </script>";
    }

    /**
     * Pulisce la cache delle pubblicità
     */
    public static function clearCache()
    {
        try {
            // Pulisce cache usando pattern matching (compatibile con file cache)
            $prefix = config('cache.prefix');
            $files = glob(storage_path('framework/cache/data/*'));
            foreach ($files as $file) {
                if (strpos(basename($file), 'advertisements_') === 0) {
                    @unlink($file);
                }
            }
        } catch (\Exception $e) {
            Log::error('Errore nella pulizia cache pubblicità: ' . $e->getMessage());
        }
    }
}