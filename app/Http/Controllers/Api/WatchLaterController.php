<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WatchLater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WatchLaterController extends Controller
{
    /**
     * Toggle watch later per un video
     */
    public function toggle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:videos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Devi effettuare l\'accesso'
            ], 401);
        }

        try {
            $userId = Auth::id();
            $videoId = $request->video_id;

            $wasInWatchLater = WatchLater::isInWatchLater($userId, $videoId);
            
            if (WatchLater::toggleWatchLater($userId, $videoId)) {
                return response()->json([
                    'success' => true,
                    'action' => $wasInWatchLater ? 'removed' : 'added',
                    'message' => $wasInWatchLater ? 'Video rimosso da Guarda più tardi' : 'Video salvato per guardarlo più tardi'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore nell\'operazione'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in watch later toggle', [
                'user_id' => Auth::id(),
                'video_id' => $request->video_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore interno del server'
            ], 500);
        }
    }

    /**
     * Ottieni lo stato dei watch later per l'utente corrente
     */
    public function status()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Devi effettuare l\'accesso'
            ], 401);
        }

        try {
            $userId = Auth::id();
            
            $watchLaterIds = WatchLater::where('user_id', $userId)
                ->pluck('video_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'watch_later_ids' => $watchLaterIds
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting watch later status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel recuperare lo stato'
            ], 500);
        }
    }

    /**
     * Verifica se un video specifico è in watch later
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:videos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'is_in_watch_later' => false
            ]);
        }

        try {
            $userId = Auth::id();
            $videoId = $request->video_id;

            $isInWatchLater = WatchLater::isInWatchLater($userId, $videoId);

            return response()->json([
                'success' => true,
                'is_in_watch_later' => $isInWatchLater
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking watch later status', [
                'user_id' => Auth::id(),
                'video_id' => $request->video_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel verificare lo stato'
            ], 500);
        }
    }
}