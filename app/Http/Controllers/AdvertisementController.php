<?php

namespace App\Http\Controllers;

use App\Helpers\AdvertisementHelper;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    /**
     * Incrementa i click per una pubblicità
     */
    public function trackClick(Request $request, $advertisementId)
    {
        try {
            $advertisement = Advertisement::find($advertisementId);
            
            if (!$advertisement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pubblicità non trovata'
                ], 404);
            }
            
            AdvertisementHelper::incrementClicks($advertisementId);
            
            return response()->json([
                'success' => true,
                'message' => 'Click tracciato con successo'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel tracciamento del click'
            ], 500);
        }
    }
    
    /**
     * Ottiene le statistiche delle pubblicità (per admin)
     */
    public function getStatistics()
    {
        try {
            $advertisements = Advertisement::withCount(['views', 'clicks'])
                ->orderBy('clicks_count', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'advertisements' => $advertisements->map(function($ad) {
                    return [
                        'id' => $ad->id,
                        'name' => $ad->name,
                        'position' => $ad->position_label,
                        'type' => $ad->type_label,
                        'views' => $ad->views_count,
                        'clicks' => $ad->clicks_count,
                        'ctr' => $ad->views_count > 0 ? round(($ad->clicks_count / $ad->views_count) * 100, 2) : 0,
                        'is_active' => $ad->isCurrentlyActive()
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero delle statistiche'
            ], 500);
        }
    }
}