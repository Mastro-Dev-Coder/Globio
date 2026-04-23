<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Video;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VideoReportController extends Controller
{
    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:videos,id',
            'report_type' => 'required|in:spam,harassment,copyright,inappropriate_content,fake_information,other',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $video = Video::findOrFail($request->video_id);

        // Verifica che l'utente non stia segnalando se stesso
        if ($video->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi segnalare i tuoi stessi video'
            ], 403);
        }

        // Verifica che non ci siano già segnalazioni recenti per lo stesso video
        $existingReport = Report::where('reporter_id', $user->id)
            ->where('video_id', $video->id)
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subDays(7))
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Hai già segnalato questo video di recente. Riprova più tardi.'
            ], 429);
        }

        try {
            $report = Report::reportVideo(
                $user->id,
                $video->id,
                $request->report_type,
                $request->reason,
                $request->description
            );

            return response()->json([
                'success' => true,
                'message' => 'Segnalazione inviata con successo. I nostri admin la esamineranno presto.',
                'report_id' => $report->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'invio della segnalazione. Riprova più tardi.'
            ], 500);
        }
    }

    public function copyVideoLink(Request $request)
    {
        $videoId = $request->video_id;
        $video = Video::findOrFail($videoId);
        
        return response()->json([
            'success' => true,
            'url' => route('reels.show', $video->slug ?: $video->id),
            'title' => $video->title
        ]);
    }

    public function toggleAmbientMode(Request $request)
    {
        $user = Auth::user();
        
        // Trova o crea le preferenze dell'utente
        $preference = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            ['ambient_mode' => false]
        );
        
        $preference->update([
            'ambient_mode' => $request->ambient_mode
        ]);

        return response()->json([
            'success' => true,
            'ambient_mode' => $preference->ambient_mode
        ]);
    }

    public function getUserPreferences()
    {
        $user = Auth::user();
        $preference = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            ['ambient_mode' => false]
        );

        return response()->json([
            'success' => true,
            'preferences' => [
                'ambient_mode' => $preference->ambient_mode
            ]
        ]);
    }

    public function getVideoStats(Request $request)
    {
        $videoId = $request->video_id;
        $video = Video::withCount(['likes', 'comments', 'watchHistory'])->findOrFail($videoId);
        
        // Solo il proprietario del video o un admin possono vedere le statistiche dettagliate
        if ($video->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        $stats = [
            'views' => $video->views_count,
            'likes' => $video->likes_count,
            'comments' => $video->comments_count,
            'watch_time' => $video->watchHistory()->sum('watch_duration'),
            'engagement_rate' => $video->views_count > 0 ? 
                round((($video->likes_count + $video->comments_count) / $video->views_count) * 100, 2) : 0,
            'top_countries' => [], // Da implementare con analytics
            'demographics' => [], // Da implementare con analytics
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
