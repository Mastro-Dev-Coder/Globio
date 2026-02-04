<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

class TestMiniPlayerController extends Controller
{
    public function index()
    {
        // Recupera alcuni video di esempio per il test
        $testVideos = Video::published()
            ->where('is_reel', false)
            ->limit(5)
            ->get()
            ->map(function ($video) {
                return [
                    'video_id' => $video->id,
                    'video_title' => $video->title,
                    'video_url' => $video->video_file_url,
                    'thumbnail_url' => $video->thumbnail_url,
                    'duration' => $video->duration,
                    'channel_name' => $video->user->userProfile?->channel_name ?: $video->user->name,
                    'formatted_duration' => $video->formatted_duration,
                    'views_count' => $video->views_count,
                    'current_time' => 0
                ];
            });

        return view('test-miniplayer', [
            'testVideos' => $testVideos->toArray()
        ]);
    }
}