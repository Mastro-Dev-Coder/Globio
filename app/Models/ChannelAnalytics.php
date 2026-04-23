<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelAnalytics extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
        'date',
        'views',
        'likes',
        'comments',
        'shares',
        'watch_time_minutes',
        'average_watch_duration',
        'click_through_rate',
        'traffic_source',
        'country',
        'device_type',
        'referrer',
    ];

    protected $casts = [
        'date' => 'date',
        'views' => 'integer',
        'likes' => 'integer',
        'comments' => 'integer',
        'shares' => 'integer',
        'watch_time_minutes' => 'decimal:2',
        'average_watch_duration' => 'decimal:2',
        'click_through_rate' => 'decimal:3',
    ];

    // Relazioni
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    // Scope per data
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Scope per utente
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope per video
    public function scopeForVideo($query, $videoId)
    {
        return $query->where('video_id', $videoId);
    }

    // Scope per fonte di traffico
    public function scopeForTrafficSource($query, $trafficSource)
    {
        return $query->where('traffic_source', $trafficSource);
    }

    // Scope per paese
    public function scopeForCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Scope per dispositivo
    public function scopeForDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    // Metodi di utilità
    public static function recordView($userId, $videoId, $data = [])
    {
        $today = now()->toDateString();
        
        $analytics = self::firstOrNew([
            'user_id' => $userId,
            'video_id' => $videoId,
            'date' => $today,
        ]);

        $analytics->increment('views');
        
        if (isset($data['watch_duration'])) {
            $analytics->increment('watch_time_minutes', $data['watch_duration'] / 60);
        }
        
        if (isset($data['traffic_source'])) {
            $analytics->traffic_source = $data['traffic_source'];
        }
        
        if (isset($data['country'])) {
            $analytics->country = $data['country'];
        }
        
        if (isset($data['device_type'])) {
            $analytics->device_type = $data['device_type'];
        }
        
        if (isset($data['referrer'])) {
            $analytics->referrer = $data['referrer'];
        }

        $analytics->save();

        return $analytics;
    }

    public static function recordLike($userId, $videoId)
    {
        $today = now()->toDateString();
        
        $analytics = self::firstOrNew([
            'user_id' => $userId,
            'video_id' => $videoId,
            'date' => $today,
        ]);

        $analytics->increment('likes');
        $analytics->save();

        return $analytics;
    }

    public static function recordComment($userId, $videoId)
    {
        $today = now()->toDateString();
        
        $analytics = self::firstOrNew([
            'user_id' => $userId,
            'video_id' => $videoId,
            'date' => $today,
        ]);

        $analytics->increment('comments');
        $analytics->save();

        return $analytics;
    }

    public static function recordShare($userId, $videoId)
    {
        $today = now()->toDateString();
        
        $analytics = self::firstOrNew([
            'user_id' => $userId,
            'video_id' => $videoId,
            'date' => $today,
        ]);

        $analytics->increment('shares');
        $analytics->save();

        // Invia notifica al creator del video
        try {
            $video = Video::find($videoId);
            if ($video && $video->user_id !== $userId) {
                $sharingUser = User::find($userId);
                if ($sharingUser) {
                    $video->user->notify(new \App\Notifications\NewVideoShareNotification($video, $sharingUser));
                    
                    \Illuminate\Support\Facades\Log::info('Notifica NewVideoShareNotification inviata', [
                        'sharer_id' => $userId,
                        'video_id' => $videoId,
                        'video_owner_id' => $video->user_id
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Errore invio notifica condivisione video', [
                'user_id' => $userId,
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
        }

        return $analytics;
    }

    // Statistiche aggregate
    public static function getChannelStats($userId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subDays(30)->toDateString();
        $endDate = $endDate ?? now()->toDateString();

        $analyticsStats = self::where('user_id', $userId)
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

        // Fallback: usa i dati aggregati dai video se la tabella analytics è vuota
        if (!$analyticsStats || ($analyticsStats->total_views ?? 0) == 0) {
            $videoStats = Video::where('user_id', $userId)
                ->published()
                ->selectRaw('
                    SUM(views_count) as total_views,
                    SUM(likes_count) as total_likes
                ')
                ->first();

            // Calcola commenti totali dai video
            $commentsCount = Comment::whereHas('video', function ($query) use ($userId) {
                    $query->where('user_id', $userId)->published();
                })
                ->where('status', 'approved')
                ->count();

            // Stima delle ore di visione (5 minuti per visualizzazione in media)
            $estimatedWatchTimeMinutes = ($videoStats->total_views ?? 0) * 5;

            return (object) [
                'total_views' => $videoStats->total_views ?? 0,
                'total_likes' => $videoStats->total_likes ?? 0,
                'total_comments' => $commentsCount,
                'total_shares' => 0,
                'total_watch_time' => $estimatedWatchTimeMinutes,
                'avg_watch_duration' => 5
            ];
        }

        return $analyticsStats;
    }

    public static function getVideoStats($videoId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subDays(30)->toDateString();
        $endDate = $endDate ?? now()->toDateString();

        return self::where('video_id', $videoId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                date,
                SUM(views) as daily_views,
                SUM(likes) as daily_likes,
                SUM(comments) as daily_comments,
                SUM(shares) as daily_shares,
                SUM(watch_time_minutes) as daily_watch_time
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public static function getTopVideos($userId, $limit = 10, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subDays(30)->toDateString();
        $endDate = $endDate ?? now()->toDateString();

        $topVideos = self::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('video')
            ->selectRaw('
                video_id,
                SUM(views) as total_views,
                SUM(likes) as total_likes,
                SUM(comments) as total_comments,
                SUM(watch_time_minutes) as total_watch_time
            ')
            ->groupBy('video_id')
            ->orderBy('total_views', 'desc')
            ->limit($limit)
            ->get();

        // Fallback: usa i dati dai video se non ci sono dati analytics
        if ($topVideos->isEmpty()) {
            return Video::where('user_id', $userId)
                ->published()
                ->orderBy('views_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($video) {
                    return (object) [
                        'video_id' => $video->id,
                        'title' => $video->title,
                        'views_count' => $video->views_count,
                        'likes_count' => $video->likes_count,
                        'total_views' => $video->views_count,
                        'total_likes' => $video->likes_count,
                        'total_comments' => 0,
                        'total_watch_time' => 0,
                        'video' => $video
                    ];
                });
        }

        return $topVideos;
    }

    public static function getTrafficSources($userId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subDays(30)->toDateString();
        $endDate = $endDate ?? now()->toDateString();

        return self::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('traffic_source')
            ->selectRaw('
                traffic_source,
                SUM(views) as views,
                COUNT(DISTINCT video_id) as video_count
            ')
            ->groupBy('traffic_source')
            ->orderBy('views', 'desc')
            ->get();
    }

    public static function getDemographics($userId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subDays(30)->toDateString();
        $endDate = $endDate ?? now()->toDateString();

        return [
            'countries' => self::where('user_id', $userId)
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
            
            'devices' => self::where('user_id', $userId)
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
    }
}