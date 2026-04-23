<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    /**
     * Global search for videos, reels, and users
     */
    public function globalSearch(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // all, videos, reels, users
        $limit = $request->get('limit', 10);

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'videos' => [],
                'reels' => [],
                'users' => [],
                'total_results' => 0
            ]);
        }

        $userId = Auth::id();
        $searchTerm = '%' . strtolower($query) . '%';
        
        // For case-insensitive search on MySQL
        $searchTermRaw = $query;

        $results = [
            'videos' => [],
            'reels' => [],
            'users' => [],
            'total_results' => 0
        ];

        // Search videos (non-reels only) - unless type is all or reels
        if ($type === 'all' || $type === 'videos') {
            $videosQuery = Video::where('is_public', true)
                ->where('is_processed', true)
                ->where('is_reel', false)
                ->where(function ($q) use ($searchTerm, $searchTermRaw) {
                    $q->where('title', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm)
                        ->orWhere('tags', 'like', $searchTerm);
                })
                ->when($userId, function ($q) use ($userId) {
                    $q->where('user_id', '!=', $userId);
                })
                ->with(['user.profile' => function ($q) {
                    $q->select('id', 'user_id', 'channel_name', 'username', 'avatar_url');
                }])
                ->orderBy('views_count', 'desc')
                ->limit($limit);

            $results['videos'] = $videosQuery->get()->map(function ($video) {
                $profile = $video->user->profile ?? null;
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'thumbnail' => $video->thumbnail_path 
                        ? asset('storage/' . $video->thumbnail_path) 
                        : asset('images/default-thumbnail.jpg'),
                    'views' => number_format($video->views_count),
                    'duration' => $video->duration ?? null,
                    'created_at' => $video->created_at->diffForHumans(),
                    'creator' => [
                        'id' => $video->user->id ?? null,
                        'name' => $profile->channel_name ?? ($video->user->name ?? 'Creator'),
                        'username' => $profile->username ?? null,
                    ],
                    'url' => route('videos.show', $video),
                ];
            });
        }

        // Search reels - unless type is all or videos
        if ($type === 'all' || $type === 'reels') {
            $reelsQuery = Video::where('is_public', true)
                ->where('is_processed', true)
                ->where('is_reel', true)
                ->where(function ($q) use ($searchTerm, $searchTermRaw) {
                    $q->where('title', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm)
                        ->orWhere('tags', 'like', $searchTerm);
                })
                ->when($userId, function ($q) use ($userId) {
                    $q->where('user_id', '!=', $userId);
                })
                ->with(['user.profile' => function ($q) {
                    $q->select('id', 'user_id', 'channel_name', 'username', 'avatar_url');
                }])
                ->orderBy('views_count', 'desc')
                ->limit($limit);

            $results['reels'] = $reelsQuery->get()->map(function ($reel) {
                $profile = $reel->user->profile ?? null;
                return [
                    'id' => $reel->id,
                    'title' => $reel->title,
                    'description' => $reel->description,
                    'thumbnail' => $reel->thumbnail_path 
                        ? asset('storage/' . $reel->thumbnail_path) 
                        : asset('images/default-thumbnail.jpg'),
                    'views' => number_format($reel->views_count),
                    'duration' => $reel->duration ?? null,
                    'created_at' => $reel->created_at->diffForHumans(),
                    'creator' => [
                        'id' => $reel->user->id ?? null,
                        'name' => $profile->channel_name ?? ($reel->user->name ?? 'Creator'),
                        'username' => $profile->username ?? null,
                    ],
                    'url' => route('reels.show', $reel),
                ];
            });
        }

        // Search users/creators - completely rewritten with robust logic
        if ($type === 'all' || $type === 'users') {
            $userResults = collect();
            
            // Search in UserProfile table - by channel_name
            $byChannelName = UserProfile::whereNotNull('channel_name')
                ->where('channel_name', 'like', $searchTerm)
                ->when($userId, function ($q) use ($userId) {
                    $q->where('user_id', '!=', $userId);
                })
                ->with('user:id,name,email')
                ->withCount('subscribers')
                ->limit($limit)
                ->get();
            
            $userResults = $userResults->merge($byChannelName);

            // Search in UserProfile table - by username
            $byUsername = UserProfile::whereNotNull('username')
                ->where('username', 'like', $searchTerm)
                ->when($userId, function ($q) use ($userId) {
                    $q->where('user_id', '!=', $userId);
                })
                ->with('user:id,name,email')
                ->withCount('subscribers')
                ->limit($limit)
                ->get();
            
            $userResults = $userResults->merge($byUsername);

            // Search in User table - by name (users without profile)
            $byUserName = User::where('name', 'like', $searchTerm)
                ->when($userId, function ($q) use ($userId) {
                    $q->where('id', '!=', $userId);
                })
                ->whereDoesntHave('profile') // Users without profile
                ->limit($limit)
                ->get();
            
            foreach ($byUserName as $user) {
                // Create a temporary profile-like object
                $tempProfile = new \stdClass();
                $tempProfile->id = 0;
                $tempProfile->user_id = $user->id;
                $tempProfile->channel_name = $user->name;
                $tempProfile->username = strtolower(str_replace(' ', '', $user->name));
                $tempProfile->channel_description = null;
                $tempProfile->avatar_url = null;
                $tempProfile->subscribers_count = 0;
                $tempProfile->user = $user;
                $tempProfile->subscribers_count = 0;
                
                $userResults->push($tempProfile);
            }

            // Remove duplicates by user_id
            $userResults = $userResults->unique('user_id')->values()->take($limit);

            $results['users'] = $userResults->map(function ($profile) {
                // Fallback: se channel_name è null, usa il nome dell'utente
                $name = $profile->channel_name ?? null;
                if (empty($name) && isset($profile->user) && $profile->user) {
                    $name = $profile->user->name;
                }

                // Fallback username
                $username = $profile->username ?? null;
                if (empty($username) && isset($profile->user) && $profile->user) {
                    $username = strtolower(str_replace(' ', '', $profile->user->name));
                }

                // Fallback avatar
                $avatar = $profile->avatar_url ?? null;
                if (empty($avatar) && isset($profile->user) && $profile->user && isset($profile->user->profile)) {
                    $avatar = $profile->user->profile->avatar_url ?? null;
                }

                return [
                    'id' => $profile->id ?? 0,
                    'user_id' => $profile->user_id ?? ($profile->user->id ?? 0),
                    'name' => $name ?? 'Utente',
                    'username' => $username ?? 'user' . ($profile->user_id ?? 0),
                    'bio' => $profile->channel_description ?? null,
                    'avatar' => $avatar 
                        ? Storage::url($avatar) 
                        : asset('images/default-avatar.jpg'),
                    'subscribers' => number_format($profile->subscribers_count ?? 0),
                    'url' => route('channel.show', $username ?? $profile->user_id ?? 0),
                ];
            });
        }

        // Calcola il totale dei risultati
        $results['total_results'] = count($results['videos']) + count($results['reels']) + count($results['users']);

        return response()->json($results);
    }

    /**
     * Search suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $searchTerm = '%' . strtolower($query) . '%';
        $limit = 5;

        $suggestions = [];

        // Video suggestions
        $videos = Video::where('is_public', true)
            ->where('is_processed', true)
            ->where('title', 'like', $searchTerm)
            ->limit($limit)
            ->pluck('title');

        foreach ($videos as $video) {
            $suggestions[] = [
                'type' => 'video',
                'text' => $video,
                'icon' => 'video'
            ];
        }

        // User suggestions - rewritten robustly
        $userSuggestions = [];
        
        // By channel_name
        $byChannelName = UserProfile::whereNotNull('channel_name')
            ->where('channel_name', 'like', $searchTerm)
            ->limit($limit)
            ->get();
        
        foreach ($byChannelName as $profile) {
            $userSuggestions[$profile->user_id] = [
                'type' => 'user',
                'text' => $profile->channel_name,
                'icon' => 'user'
            ];
        }

        // By username
        $byUsername = UserProfile::whereNotNull('username')
            ->where('username', 'like', $searchTerm)
            ->limit($limit)
            ->get();
        
        foreach ($byUsername as $profile) {
            $userSuggestions[$profile->user_id] = [
                'type' => 'user',
                'text' => '@' . $profile->username,
                'icon' => 'user'
            ];
        }

        // By user name (users without profile)
        $byUserName = User::where('name', 'like', $searchTerm)
            ->whereDoesntHave('profile')
            ->limit($limit)
            ->get();
        
        foreach ($byUserName as $user) {
            $userSuggestions[$user->id] = [
                'type' => 'user',
                'text' => $user->name,
                'icon' => 'user'
            ];
        }

        foreach ($userSuggestions as $suggestion) {
            $suggestions[] = $suggestion;
        }

        return response()->json($suggestions);
    }

    /**
     * Advanced search with filters
     */
    public function advancedSearch(Request $request)
    {
        $query = $request->get('q', '');
        $videoType = $request->get('video_type', 'all'); // all, regular, reel
        $sortBy = $request->get('sort', 'relevance'); // relevance, views, date
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'videos' => [],
                'total' => 0
            ]);
        }

        $searchTerm = '%' . strtolower($query) . '%';
        $userId = Auth::id();

        $videoQuery = Video::where('is_public', true)
            ->where('is_processed', true)
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('tags', 'like', $searchTerm);
            })
            ->when($userId, function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId);
            });

        // Filter by video type
        if ($videoType === 'reel') {
            $videoQuery->where('is_reel', true);
        } elseif ($videoType === 'regular') {
            $videoQuery->where('is_reel', false);
        }
        // 'all' returns both

        // Sort options
        switch ($sortBy) {
            case 'views':
                $videoQuery->orderBy('views_count', 'desc');
                break;
            case 'date':
                $videoQuery->orderBy('created_at', 'desc');
                break;
            default: // relevance - by views
                $videoQuery->orderBy('views_count', 'desc');
        }

        $total = $videoQuery->count();
        $videos = $videoQuery->offset($offset)->limit($limit)
            ->with(['user.profile' => function ($q) {
                $q->select('id', 'user_id', 'channel_name', 'username', 'avatar_url');
            }])
            ->get()
            ->map(function ($video) {
                $profile = $video->user->profile ?? null;
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'thumbnail' => $video->thumbnail_path 
                        ? asset('storage/' . $video->thumbnail_path) 
                        : asset('images/default-thumbnail.jpg'),
                    'views' => number_format($video->views_count),
                    'duration' => $video->duration ?? null,
                    'is_reel' => $video->is_reel,
                    'created_at' => $video->created_at->diffForHumans(),
                    'creator' => [
                        'id' => $video->user->id ?? null,
                        'name' => $profile->channel_name ?? ($video->user->name ?? 'Creator'),
                        'username' => $profile->username ?? null,
                    ],
                    'url' => $video->is_reel 
                        ? route('reels.show', $video) 
                        : route('videos.show', $video),
                ];
            });

        return response()->json([
            'videos' => $videos,
            'total' => $total,
            'has_more' => ($offset + $limit) < $total
        ]);
    }
}
