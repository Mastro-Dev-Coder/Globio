<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChannelAnalytics;
use App\Models\Report;
use App\Models\Video;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period)->toDateString();
        $endDate = now()->toDateString();

        $userStats = $this->getUserStats($startDate, $endDate);
        $contentStats = $this->getContentStats($startDate, $endDate);
        $analyticsStats = $this->getAnalyticsStats($startDate, $endDate);
        $reportsStats = $this->getReportsStats($startDate, $endDate);
        $dailyTrends = $this->getDailyTrends($startDate, $endDate);
        $topCreators = $this->getTopCreators($startDate, $endDate);

        return view('admin.dashboard', compact(
            'userStats',
            'contentStats',
            'analyticsStats',
            'reportsStats',
            'dailyTrends',
            'topCreators',
            'period'
        ));
    }

    public function users(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort', 'newest');

        $query = User::with('userProfile')->withCount(['videos', 'subscribers']);

        // Ricerca
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Ordinamento
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'videos':
                $query->orderBy('videos', 'desc');
                break;
            default:
                $query->latest();
        }

        $users = $query->paginate(20);

        return view('admin.users', compact('users', 'search', 'sortBy'));
    }

    public function content(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period)->toDateString();

        $newVideos = Video::with(['user:id,name', 'userProfile:user_id,channel_name'])
            ->where('published_at', '>=', $startDate)
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $popularVideos = Video::with(['user:id,name', 'userProfile:user_id,channel_name'])
            ->where('published_at', '>=', $startDate)
            ->withCount('watchHistories')
            ->orderBy('watch_histories_count', 'desc')
            ->limit(10)
            ->get();

        $commentStats = [
            'total' => Comment::where('created_at', '>=', $startDate)->count(),
            'pending' => Comment::where('status', 'pending')->where('created_at', '>=', $startDate)->count(),
            'approved' => Comment::where('status', 'approved')->where('created_at', '>=', $startDate)->count(),
        ];

        return view('admin.content', compact('newVideos', 'popularVideos', 'commentStats', 'period'));
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period)->toDateString();
        $endDate = now()->toDateString();

        // Metriche globali
        $globalMetrics = ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                SUM(views) as total_views,
                SUM(watch_time_minutes) as total_watch_time,
                SUM(likes) as total_likes,
                SUM(comments) as total_comments,
                SUM(shares) as total_shares
            ')
            ->first();

        // Calcola metriche derivate
        $totalViews = $globalMetrics->total_views ?? 0;
        $totalWatchTime = $globalMetrics->total_watch_time ?? 0;
        $totalLikes = $globalMetrics->total_likes ?? 0;
        $totalComments = $globalMetrics->total_comments ?? 0;
        $totalShares = $globalMetrics->total_shares ?? 0;

        // Calcola tasso di engagement
        $totalInteractions = $totalLikes + $totalComments + $totalShares;
        $engagementRate = $totalViews > 0 ? round(($totalInteractions / $totalViews) * 100, 1) : 0;

        // Calcola revenue stimato (basato su stime: 4$ per 1000 views)
        $totalRevenue = $totalViews > 0 ? ($totalViews / 1000) * 4 : 0;

        // Calcola growth rates (confronto con periodo precedente)
        $prevStartDate = now()->subDays($period * 2)->toDateString();
        $prevEndDate = now()->subDays($period)->toDateString();
        
        $prevMetrics = ChannelAnalytics::whereBetween('date', [$prevStartDate, $prevEndDate])
            ->selectRaw('
                SUM(views) as total_views,
                SUM(watch_time_minutes) as total_watch_time
            ')
            ->first();
        
        $prevViews = $prevMetrics->total_views ?? 0;
        $prevWatchTime = $prevMetrics->total_watch_time ?? 0;
        
        $growthRates = [
            'views' => $prevViews > 0 ? round((($totalViews - $prevViews) / $prevViews) * 100, 1) : ($totalViews > 0 ? 100 : 0),
            'watch_time' => $prevWatchTime > 0 ? round((($totalWatchTime - $prevWatchTime) / $prevWatchTime) * 100, 1) : ($totalWatchTime > 0 ? 100 : 0),
            'engagement' => 0, // Placeholder
            'revenue' => 0, // Placeholder
        ];

        // Andamento giornaliero per grafico performance
        $performanceTrends = $this->getDailyAnalytics($startDate, $endDate);

        // Engagement breakdown
        $engagementBreakdown = [
            'likes' => $totalLikes,
            'comments' => $totalComments,
            'shares' => $totalShares,
            'saves' => 0, // Non disponibile nel modello attuale
        ];

        // Growth analysis
        $growthAnalysis = [
            'user_growth' => 0, // Placeholder
            'video_growth' => 0, // Placeholder
            'view_growth' => $growthRates['views'],
            'revenue_growth' => 0, // Placeholder
        ];

        // Top video per visualizzazioni
        $topVideos = ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
            ->with('video:id,title,user_id')
            ->selectRaw('
                video_id,
                SUM(views) as total_views,
                SUM(watch_time_minutes) as total_watch_time
            ')
            ->groupBy('video_id')
            ->orderBy('total_views', 'desc')
            ->limit(10)
            ->get();

        // Top content formattato per la vista
        $topContent = $topVideos;

        // Fonti di traffico globali
        $trafficSources = ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('traffic_source')
            ->selectRaw('
                traffic_source,
                SUM(views) as total_views,
                COUNT(DISTINCT user_id) as creator_count
            ')
            ->groupBy('traffic_source')
            ->orderBy('total_views', 'desc')
            ->get();

        // Dati demografici globali
        $demographics = [
            'countries' => ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('country')
                ->selectRaw('
                    country,
                    SUM(views) as total_views
                ')
                ->groupBy('country')
                ->orderBy('total_views', 'desc')
                ->limit(10)
                ->get(),

            'devices' => ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('device_type')
                ->selectRaw('
                    device_type,
                    SUM(views) as total_views
                ')
                ->groupBy('device_type')
                ->orderBy('total_views', 'desc')
                ->get(),
        ];

        // Peak hours (orari di punta)
        $peakHours = $this->getPeakHours($startDate, $endDate);

        // Creator analytics per tabella dettagliata
        $creatorAnalytics = $this->getCreatorAnalytics($startDate, $endDate);

        // Average watch time in minuti
        $averageWatchTime = 0;
        if ($totalViews > 0 && $totalWatchTime > 0) {
            $averageWatchTime = round($totalWatchTime / $totalViews, 2);
        }

        return view('admin.analytics', compact(
            'globalMetrics',
            'dailyAnalytics',
            'topVideos',
            'trafficSources',
            'demographics',
            'period',
            'totalViews',
            'averageWatchTime',
            'engagementRate',
            'totalRevenue',
            'growthRates',
            'performanceTrends',
            'engagementBreakdown',
            'growthAnalysis',
            'topContent',
            'peakHours',
            'creatorAnalytics'
        ));
    }

    public function reports(Request $request)
    {
        $status = $request->get('status', 'pending');
        $priority = $request->get('priority');
        $type = $request->get('type');

        $reports = Report::with(['reporter:id,name', 'reportedUser:id,name', 'video:id,title', 'admin:id,name'])
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($priority, function ($query) use ($priority) {
                $query->where('priority', $priority);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderByRaw('CASE priority 
                WHEN "urgent" THEN 1 
                WHEN "high" THEN 2 
                WHEN "medium" THEN 3 
                WHEN "low" THEN 4 
                END')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = Report::getStats(now()->subDays(30)->toDateString(), now()->toDateString());

        $admins = User::where('role', 'admin')->orderBy('name')->get();

        return view('admin.reports', compact('reports', 'stats', 'admins', 'status', 'priority', 'type'));
    }

    private function getUserStats($startDate, $endDate)
    {
        return [
            'total' => User::count(),
            'new' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active' => User::whereHas('videos', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
        ];
    }

    private function getContentStats($startDate, $endDate)
    {
        return [
            'total_videos' => Video::count(),
            'new_videos' => Video::whereBetween('published_at', [$startDate, $endDate])->count(),
            'total_comments' => Comment::count(),
            'new_comments' => Comment::whereBetween('created_at', [$startDate, $endDate])->count(),
            'pending_comments' => Comment::where('status', 'pending')->count(),
        ];
    }

    private function getAnalyticsStats($startDate, $endDate)
    {
        return ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                SUM(views) as total_views,
                SUM(watch_time_minutes) as total_watch_time,
                AVG(average_watch_duration) as avg_watch_duration
            ')
            ->first();
    }

    private function getReportsStats($startDate, $endDate)
    {
        return Report::getStats($startDate, $endDate);
    }

    private function getDailyTrends($startDate, $endDate)
    {
        $days = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dateString = $current->toDateString();

            $dayData = [
                'date' => $dateString,
                'users' => User::whereDate('created_at', $dateString)->count(),
                'videos' => Video::whereDate('published_at', $dateString)->count(),
                'views' => ChannelAnalytics::where('date', $dateString)->sum('views'),
            ];

            $days[] = $dayData;
            $current->addDay();
        }

        return $days;
    }

    private function getTopCreators($startDate, $endDate)
    {
        return User::with(['userProfile:user_id,channel_name,avatar_url'])
            ->withCount([
                'videos' => function ($query) use ($startDate) {
                    $query->where('published_at', '>=', $startDate);
                },
                'subscriptions'
            ])
            ->withSum(['videos' => function ($query) use ($startDate) {
                $query->where('published_at', '>=', $startDate);
            }], 'views_count')
            ->havingRaw('videos_count > 0')
            ->orderBy('videos_sum_views_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getUserGrowthStats($startDate)
    {
        $weeklyGrowth = [];
        $current = Carbon::parse($startDate);

        for ($i = 0; $i < 4; $i++) {
            $weekStart = $current->copy()->startOfWeek();
            $weekEnd = $current->copy()->endOfWeek();

            $count = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();

            $weeklyGrowth[] = [
                'week' => $weekStart->format('d/m'),
                'users' => $count
            ];

            $current->addWeek();
        }

        return $weeklyGrowth;
    }

    private function getDailyAnalytics($startDate, $endDate)
    {
        return ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                date,
                SUM(views) as views,
                SUM(watch_time_minutes) as watch_time,
                SUM(likes) as likes
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPeakHours($startDate, $endDate)
    {
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[$i] = [
                'views' => 0,
                'active' => false,
            ];
        }
        
        // Aggrega per ora
        $hourlyData = ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                HOUR(created_at) as hour,
                SUM(views) as views
            ')
            ->groupBy('hour')
            ->get();
        
        foreach ($hourlyData as $data) {
            $hours[$data->hour]['views'] = $data->views;
            $hours[$data->hour]['active'] = true;
        }
        
        return $hours;
    }

    private function getCreatorAnalytics($startDate, $endDate)
    {
        // Ottieni tutti i creator con le loro statistiche
        $creators = User::where('role', 'creator')->orWhere('role', 'user')->get();
        
        $creatorData = [];
        
        foreach ($creators as $creator) {
            $stats = ChannelAnalytics::whereBetween('date', [$startDate, $endDate])
                ->where('user_id', $creator->id)
                ->selectRaw('
                    COUNT(DISTINCT video_id) as video_count,
                    SUM(views) as total_views,
                    SUM(likes) as total_likes,
                    SUM(comments) as total_comments,
                    SUM(watch_time_minutes) as total_watch_time
                ')
                ->first();
            
            if ($stats && $stats->video_count > 0) {
                $totalInteractions = ($stats->total_likes ?? 0) + ($stats->total_comments ?? 0);
                $engagementRate = $stats->total_views > 0 
                    ? round(($totalInteractions / $stats->total_views) * 100, 1) 
                    : 0;
                
                // Revenue stimato
                $totalRevenue = $stats->total_views > 0 
                    ? ($stats->total_views / 1000) * 4 
                    : 0;
                
                // Growth rate (mock per ora)
                $growthRate = rand(-10, 20);
                
                $creatorData[] = (object) [
                    'id' => $creator->id,
                    'name' => $creator->userProfile->channel_name ?? $creator->name,
                    'video_count' => $stats->video_count,
                    'total_views' => $stats->total_views ?? 0,
                    'engagement_rate' => $engagementRate,
                    'total_watch_time' => $stats->total_watch_time ?? 0,
                    'total_revenue' => $totalRevenue,
                    'growth_rate' => $growthRate,
                ];
            }
        }
        
        // Ordina per views
        usort($creatorData, function($a, $b) {
            return $b->total_views - $a->total_views;
        });
        
        return collect(array_slice($creatorData, 0, 20));
    }

    private function getRevenueStats($startDate)
    {
        // Monetization model non esiste, ritorna dati vuoti/mock
        return [
            'monthly' => 0,
            'yearly' => 0,
            'pending' => 0,
            'by_type' => [],
        ];
    }
}
