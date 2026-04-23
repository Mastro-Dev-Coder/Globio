<?php

namespace App\Http\Controllers;

use App\Models\ChannelAnalytics;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdvancedAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard analytics principale del channel studio
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', '30'); // giorni
        $startDate = now()->subDays($period)->toDateString();
        $endDate = now()->toDateString();

        // Statistiche generali del canale
        $channelStats = ChannelAnalytics::getChannelStats($user->id, $startDate, $endDate);
        
        // Trend giornaliero
        $dailyStats = $this->getDailyStats($user->id, $startDate, $endDate);
        
        // Video più performanti
        $topVideos = ChannelAnalytics::getTopVideos($user->id, 5, $startDate, $endDate);
        
        // Fonti di traffico
        $trafficSources = ChannelAnalytics::getTrafficSources($user->id, $startDate, $endDate);
        
        // Dati demografici
        $demographics = ChannelAnalytics::getDemographics($user->id, $startDate, $endDate);

        return view('users.analytics', compact(
            'channelStats', 
            'dailyStats', 
            'topVideos', 
            'trafficSources', 
            'demographics',
            'period'
        ));
    }

    /**
     * Analytics dettagliate per video specifico
     */
    public function videoAnalytics(Request $request, $videoId)
    {
        $user = Auth::user();
        $video = Video::where('id', $videoId)->where('user_id', $user->id)->firstOrFail();
        
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period)->toDateString();
        $endDate = now()->toDateString();

        // Statistiche del video
        $videoStats = ChannelAnalytics::getVideoStats($videoId, $startDate, $endDate);
        
        // Statistiche aggregate del video
        $aggregatedStats = ChannelAnalytics::where('video_id', $videoId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                SUM(views) as total_views,
                SUM(likes) as total_likes,
                SUM(comments) as total_comments,
                SUM(shares) as total_shares,
                SUM(watch_time_minutes) as total_watch_time,
                AVG(average_watch_duration) as avg_watch_duration
            ')
            ->first();

        // Fonte di traffico per questo video
        $trafficSources = ChannelAnalytics::where('video_id', $videoId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('traffic_source')
            ->selectRaw('
                traffic_source,
                SUM(views) as views
            ')
            ->groupBy('traffic_source')
            ->orderBy('views', 'desc')
            ->get();

        // Dati demografici per questo video
        $demographics = [
            'countries' => ChannelAnalytics::where('video_id', $videoId)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('country')
                ->selectRaw('
                    country,
                    SUM(views) as views
                ')
                ->groupBy('country')
                ->orderBy('views', 'desc')
                ->limit(10)
                ->get(),
            
            'devices' => ChannelAnalytics::where('video_id', $videoId)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('device_type')
                ->selectRaw('
                    device_type,
                    SUM(views) as views
                ')
                ->groupBy('device_type')
                ->orderBy('views', 'desc')
                ->get(),
        ];

        return view('users.video-analytics', compact(
            'video',
            'videoStats',
            'aggregatedStats',
            'trafficSources',
            'demographics',
            'period'
        ));
    }

    /**
     * Confronta performance tra periodi
     */
    public function compare(Request $request)
    {
        $user = Auth::user();
        $currentPeriod = $request->get('current_period', 30);
        $comparePeriod = $request->get('compare_period', 30);

        $currentStart = now()->subDays($currentPeriod)->toDateString();
        $currentEnd = now()->toDateString();
        
        $compareStart = now()->subDays($currentPeriod + $comparePeriod)->toDateString();
        $compareEnd = now()->subDays($currentPeriod)->toDateString();

        // Statistiche periodo corrente
        $currentStats = ChannelAnalytics::getChannelStats($user->id, $currentStart, $currentEnd);
        
        // Statistiche periodo di confronto
        $compareStats = ChannelAnalytics::getChannelStats($user->id, $compareStart, $compareEnd);

        // Calcola le percentuali di cambiamento
        $changes = [
            'views' => $this->calculatePercentageChange($compareStats->total_views ?? 0, $currentStats->total_views ?? 0),
            'likes' => $this->calculatePercentageChange($compareStats->total_likes ?? 0, $currentStats->total_likes ?? 0),
            'comments' => $this->calculatePercentageChange($compareStats->total_comments ?? 0, $currentStats->total_comments ?? 0),
            'watch_time' => $this->calculatePercentageChange($compareStats->total_watch_time ?? 0, $currentStats->total_watch_time ?? 0),
        ];

        return view('users.analytics-compare', compact(
            'currentStats',
            'compareStats',
            'changes',
            'currentPeriod',
            'comparePeriod'
        ));
    }

    /**
     * Esporta dati analytics
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'csv');
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period)->toDateString();
        $endDate = now()->toDateString();

        $data = ChannelAnalytics::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('video:id,title')
            ->orderBy('date', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        return response()->json($data);
    }

    /**
     * Ottiene statistiche giornaliere per grafico
     */
    private function getDailyStats($userId, $startDate, $endDate)
    {
        $days = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dateString = $current->toDateString();
            
            $stats = ChannelAnalytics::where('user_id', $userId)
                ->where('date', $dateString)
                ->selectRaw('
                    SUM(views) as views,
                    SUM(likes) as likes,
                    SUM(comments) as comments,
                    SUM(shares) as shares
                ')
                ->first();

            $days[] = [
                'date' => $dateString,
                'views' => $stats->views ?? 0,
                'likes' => $stats->likes ?? 0,
                'comments' => $stats->comments ?? 0,
                'shares' => $stats->shares ?? 0,
            ];

            $current->addDay();
        }

        return $days;
    }

    /**
     * Calcola la percentuale di cambiamento
     */
    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }

    /**
     * Esporta dati in formato CSV
     */
    private function exportToCsv($data)
    {
        $filename = 'analytics_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'Data',
                'Video',
                'Visualizzazioni',
                'Mi piace',
                'Commenti',
                'Condivisioni',
                'Tempo di visione (min)',
                'Durata media visione',
                'Fonte traffico',
                'Paese',
                'Dispositivo'
            ]);

            // Dati
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->date,
                    $row->video->title ?? 'N/A',
                    $row->views,
                    $row->likes,
                    $row->comments,
                    $row->shares,
                    $row->watch_time_minutes,
                    $row->average_watch_duration,
                    $row->traffic_source ?? 'N/A',
                    $row->country ?? 'N/A',
                    $row->device_type ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}