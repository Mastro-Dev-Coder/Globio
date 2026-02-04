<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChannelAnalytics;
use App\Models\Monetization;
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

        // Andamento giornaliero
        $dailyAnalytics = $this->getDailyAnalytics($startDate, $endDate);

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

        return view('admin.analytics', compact(
            'globalMetrics',
            'dailyAnalytics',
            'topVideos',
            'trafficSources',
            'demographics',
            'period'
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

    private function getRevenueStats($startDate)
    {
        return [
            'monthly' => Monetization::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'yearly' => Monetization::whereYear('created_at', now()->year)
                ->sum('amount'),
            'pending' => Monetization::where('status', 'pending')->sum('amount'),
            'by_type' => Monetization::where('created_at', '>=', $startDate)
                ->selectRaw('type, SUM(amount) as total')
                ->groupBy('type')
                ->get()
                ->pluck('total', 'type')
                ->toArray(),
        ];
    }
}
