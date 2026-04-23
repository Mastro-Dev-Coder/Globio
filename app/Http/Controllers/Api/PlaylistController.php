<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Ottiene una playlist casuale di video
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function shuffle(Request $request): JsonResponse
    {
        $excludeId = $request->query('exclude');
        $limit = $request->query('limit', 10);

        // Query base per i video pubblicati
        $query = Video::where('status', 'published')
            ->where('is_reel', false)
            ->select('id', 'title', 'thumbnail_path', 'video_path');

        // Escludi il video corrente se specificato
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Ottieni video casuali
        $videos = $query->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'url' => asset('storage/' . $video->video_path),
                    'poster' => asset('storage/' . $video->thumbnail_path),
                ];
            });

        return response()->json([
            'success' => true,
            'videos' => $videos,
            'count' => $videos->count(),
        ]);
    }
}