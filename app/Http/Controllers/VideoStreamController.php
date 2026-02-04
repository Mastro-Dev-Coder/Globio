<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\log;

class VideoStreamController extends Controller
{
    /**
     * Stream video per precaricamento
     */
    public function stream(Request $request, $videoId)
    {
        try {
            $video = Video::findOrFail($videoId);
            
            // Verifica che il video sia pubblicato e sia un reel
            if (!$video->is_published || !$video->is_reel) {
                return response()->json(['error' => 'Video non disponibile'], 404);
            }

            $path = storage_path('app/public/' . $video->video_path);
            
            if (!file_exists($path)) {
                return response()->json(['error' => 'File video non trovato'], 404);
            }

            $fileSize = filesize($path);
            $handle = fopen($path, 'rb');
            
            if (!$handle) {
                return response()->json(['error' => 'Impossibile aprire il file'], 500);
            }

            $range = $request->header('Range');
            
            if ($range) {
                // Supporta il partial content per il precaricamento
                $positions = explode('=', $range);
                $start = intval($positions[1]);
                $end = min($start + 1024 * 1024, $fileSize - 1); // 1MB chunks per precaricamento
                
                $contentLength = $end - $start + 1;
                
                header("HTTP/1.1 206 Partial Content");
                header("Content-Range: bytes $start-$end/$fileSize");
                header("Content-Length: $contentLength");
                header("Accept-Ranges: bytes");
                
                fseek($handle, $start);
                $content = fread($handle, $contentLength);
                fclose($handle);
                
                return response($content, 206)
                    ->header('Content-Type', 'video/mp4')
                    ->header('Cache-Control', 'public, max-age=3600');
            } else {
                // Return metadata only per precaricamento veloce
                return response()->json([
                    'duration' => $video->duration,
                    'size' => $fileSize,
                    'thumbnail' => asset('storage/' . $video->thumbnail_path),
                    'ready' => true
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Video stream error', [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Errore interno del server'], 500);
        }
    }

    /**
     * Ottieni informazioni video per precaricamento
     */
    public function info($videoId)
    {
        try {
            $video = Video::findOrFail($videoId);
            
            if (!$video->is_published || !$video->is_reel) {
                return response()->json(['error' => 'Video non disponibile'], 404);
            }

            return response()->json([
                'id' => $video->id,
                'title' => $video->title,
                'duration' => $video->duration,
                'thumbnail' => asset('storage/' . $video->thumbnail_path),
                'video_url' => asset('storage/' . $video->video_path),
                'size' => file_exists(storage_path('app/public/' . $video->video_path)) 
                    ? filesize(storage_path('app/public/' . $video->video_path)) 
                    : 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Video info error', [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Errore interno del server'], 500);
        }
    }
}