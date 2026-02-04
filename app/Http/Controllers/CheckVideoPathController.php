<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class CheckVideoPathController extends Controller
{
    public function check()
    {
        $video = Video::find(27);
        
        if (!$video) {
            return response()->json(['error' => 'Video not found']);
        }
        
        return response()->json([
            'video_id' => $video->id,
            'video_path' => $video->video_path,
            'video_url' => $video->video_url,
            'expected_full_path' => 'storage/videos/' . $video->video_path,
            'asset_url' => asset('storage/' . $video->video_path),
            'file_exists' => file_exists(public_path('storage/' . $video->video_path)),
        ]);
    }
}