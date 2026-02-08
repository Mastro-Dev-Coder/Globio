<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\User;
use App\Models\Video;
use App\Services\AutomatedPlaylistService;
use App\Services\VideoRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Mostra la homepage con i video
     */
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');

        if ($category === 'reels') {
            $videos = Video::with(['user', 'user.userProfile'])
                ->published()
                ->where('is_reel', true)
                ->inRandomOrder()
                ->paginate(24);
        } else {
            if (Auth::check()) {
                $recommendationService = new VideoRecommendationService();

                $filters = [];
                if ($category !== 'all') {
                    if ($category === 'videos') {
                        $filters['is_reel'] = false;
                    } else {
                        $filters['tags'] = $category;
                    }
                }

                if ($request->has('duration')) {
                    $filters['duration'] = $request->duration;
                }

                $videos = $recommendationService->getRecommendationsFromInterests(Auth::id(), 24, $filters);

                $videos = new \Illuminate\Pagination\LengthAwarePaginator(
                    $videos,
                    $videos->count(),
                    24,
                    1,
                    ['path' => $request->url(), 'pageName' => 'page']
                );
            } else {
                $query = Video::with(['user', 'user.userProfile'])
                    ->published()
                    ->where('is_reel', false)
                    ->inRandomOrder();

                if ($category !== 'all') {
                    if ($category === 'videos') {
                        $query->where('is_reel', false);
                    } else {
                        $query->where('tags', 'like', '%' . $category . '%');
                    }
                }

                if ($request->has('duration')) {
                    switch ($request->duration) {
                        case 'short':
                            $query->where('duration', '<', 300);
                            break;
                        case 'medium':
                            $query->whereBetween('duration', [300, 1200]);
                            break;
                        case 'long':
                            $query->where('duration', '>', 1200);
                            break;
                    }
                }

                $videos = $query->paginate(24);
            }
        }

        $trendingVideos = Video::with(['user', 'user.userProfile'])
            ->published()
            ->where('is_reel', false)
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        $suggestedVideos = Video::with(['user', 'user.userProfile'])
            ->published()
            ->where('is_reel', false)
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Recupera le playlist automatiche dell'utente (personalizzate)
        $autoPlaylists = collect();
        if (Auth::check()) {
            $autoPlaylistService = new AutomatedPlaylistService();
            $autoPlaylists = $autoPlaylistService->getUserAutoPlaylists(Auth::id());

            // Se non ci sono playlist automatiche, generane qualcune
            if ($autoPlaylists->isEmpty()) {
                $autoPlaylists = $autoPlaylistService->generateAutomaticPlaylists(Auth::id(), 5);
            }
        }

        // Recupera playlist consigliate (esistenti)
        $playlistRecommendations = collect();
        if (Auth::check()) {
            $recommendationService = new VideoRecommendationService();
            $playlistRecommendations = $recommendationService->getPlaylistRecommendations(Auth::id(), 2);
        } else {
            // Per utenti non autenticati, mostra playlist popolari
            $playlistRecommendations = Playlist::public()
                ->with(['user', 'videos'])
                ->whereHas('videos', function ($query) {
                    $query->published();
                })
                ->orderBy('views_count', 'desc')
                ->limit(2)
                ->get();
        }

        // Recupera le playlist dell'utente loggato (le sue playlist più viste)
        $userPlaylists = collect();
        if (Auth::check()) {
            $userPlaylists = Playlist::where('user_id', Auth::id())
                ->where('is_automatic', false) // Solo playlist manuali
                ->with('videos')
                ->withCount('videos')
                ->orderBy('views_count', 'desc')
                ->limit(4)
                ->get();
        }

        // Integra le playlist con i video nella griglia principale
        $integratedContent = collect();
        $videoCount = 0;
        $playlistCount = 0;
        $userPlaylistCount = 0;
        $userPlaylistsArray = $userPlaylists->toArray();

        foreach ($videos as $index => $video) {
            $integratedContent->push([
                'type' => 'video',
                'content' => $video
            ]);
            $videoCount++;

            // Inserisce una playlist consigliata ogni 6 video
            if ($videoCount % 6 == 0 && $playlistCount < $playlistRecommendations->count()) {
                $integratedContent->push([
                    'type' => 'playlist',
                    'content' => $playlistRecommendations[$playlistCount],
                    'source' => 'recommended'
                ]);
                $playlistCount++;
            }

            // Inserisce una playlist dell'utente ogni 8 video (se l'utente ha playlist)
            if (Auth::check() && $videoCount % 8 == 0 && $userPlaylistCount < count($userPlaylistsArray)) {
                $integratedContent->push([
                    'type' => 'playlist',
                    'content' => $userPlaylists[$userPlaylistCount],
                    'source' => 'user'
                ]);
                $userPlaylistCount++;
            }
        }

        // Aggiunge playlist rimanenti alla fine se ci sono spazi
        while ($playlistCount < $playlistRecommendations->count()) {
            $integratedContent->push([
                'type' => 'playlist',
                'content' => $playlistRecommendations[$playlistCount],
                'source' => 'recommended'
            ]);
            $playlistCount++;
        }

        return view('home', compact('integratedContent', 'trendingVideos', 'suggestedVideos', 'videos', 'userPlaylists', 'autoPlaylists'));
    }

    /**
     * Carica più video per scroll infinito (AJAX)
     */
    public function loadMore(Request $request)
    {
        $page = $request->get('page', 1);
        $category = $request->get('category', 'all');
        $sort = $request->get('sort', 'latest');

        if ($category === 'reels') {
            $query = Video::with(['user', 'user.userProfile'])
                ->published()
                ->where('is_reel', true)
                ->inRandomOrder();

            $totalVideos = (clone $query)->count();
            $perPage = 12;
            $loadedVideos = ($page - 1) * $perPage;

            if ($loadedVideos >= $totalVideos) {
                return response()->json([
                    'success' => true,
                    'html' => '',
                    'has_more' => false,
                    'total_count' => $totalVideos,
                    'loaded_count' => $loadedVideos
                ]);
            }

            $videos = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            if (Auth::check()) {
                $recommendationService = new VideoRecommendationService();

                $filters = [];
                if ($category !== 'all') {
                    if ($category === 'videos') {
                        $filters['is_reel'] = false;
                    } else {
                        $filters['tags'] = $category;
                    }
                }

                if ($request->has('duration')) {
                    $filters['duration'] = $request->duration;
                }

                $perPage = 12;
                $offset = ($page - 1) * $perPage;

                $allVideos = $recommendationService->getRecommendationsFromInterests(Auth::id(), 1000, $filters);
                $totalVideos = $allVideos->count();
                $loadedVideos = $offset;

                if ($loadedVideos >= $totalVideos) {
                    return response()->json([
                        'success' => true,
                        'html' => '',
                        'has_more' => false,
                        'total_count' => $totalVideos,
                        'loaded_count' => $loadedVideos
                    ]);
                }

                $videos = $allVideos->slice($offset, $perPage);
                $hasMore = ($offset + $perPage) < $totalVideos;
            } else {
                $query = Video::with(['user', 'user.userProfile'])
                    ->published()
                    ->where('is_reel', false);

                if ($category && $category !== 'all') {
                    if ($category === 'videos') {
                        $query->where('is_reel', false);
                    } else {
                        $query->where('tags', 'like', '%' . $category . '%');
                    }
                }

                if ($sort) {
                    switch ($sort) {
                        case 'popular':
                            $query->orderBy('views_count', 'desc');
                            break;
                        case 'trending':
                            $query->where('published_at', '>=', now()->subDays(7))
                                ->orderBy('views_count', 'desc');
                            break;
                        default:
                            $query->inRandomOrder();
                    }
                }

                $totalVideos = (clone $query)->count();
                $perPage = 12;
                $loadedVideos = ($page - 1) * $perPage;

                if ($loadedVideos >= $totalVideos) {
                    return response()->json([
                        'success' => true,
                        'html' => '',
                        'has_more' => false,
                        'total_count' => $totalVideos,
                        'loaded_count' => $loadedVideos
                    ]);
                }

                $videos = $query->paginate($perPage, ['*'], 'page', $page);
                $hasMore = $videos->hasMorePages();
            }
        }

        $html = '';
        if (isset($videos) && $videos) {
            foreach ($videos as $video) {
                if ($video->is_reel) {
                    $html .= view('components.reel', compact('video'))->render();
                } else {
                    $html .= view('components.video', compact('video'))->render();
                }
            }
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'has_more' => $hasMore ?? false,
            'total_count' => $totalVideos ?? 0,
            'loaded_count' => ($loadedVideos ?? 0) + ($videos ? $videos->count() : 0)
        ]);
    }

    /**
     * Filtra video (AJAX)
     */
    public function filter(Request $request)
    {
        $category = $request->get('category', 'all');
        $sort = $request->get('sort', 'latest');

        if ($category === 'reels') {
            $query = Video::with(['user', 'user.userProfile'])
                ->published()
                ->where('is_reel', true)
                ->inRandomOrder();

            $totalVideos = (clone $query)->count();

            if ($totalVideos === 0) {
                return response()->json([
                    'success' => true,
                    'html' => '<div class="col-span-full text-center py-16"><div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-video text-2xl text-gray-400"></i></div><h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun video disponibile</h3><p class="text-gray-600 dark:text-gray-400 text-sm">Torna presto per scoprire nuovi contenuti</p></div>',
                    'has_more' => false,
                    'total_count' => 0,
                    'loaded_count' => 0
                ]);
            }

            $videos = $query->paginate(12);
        } else {
            if (Auth::check()) {
                $recommendationService = new VideoRecommendationService();

                $filters = [];
                if ($category !== 'all') {
                    if ($category === 'videos') {
                        $filters['is_reel'] = false;
                    } else {
                        $filters['tags'] = $category;
                    }
                }

                if ($request->has('duration')) {
                    $filters['duration'] = $request->duration;
                }

                $videos = $recommendationService->getRecommendationsFromInterests(Auth::id(), 12, $filters);
                $totalVideos = $videos->count();

                if ($totalVideos === 0) {
                    return response()->json([
                        'success' => true,
                        'html' => '<div class="col-span-full text-center py-16"><div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-video text-2xl text-gray-400"></i></div><h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun video disponibile</h3><p class="text-gray-600 dark:text-gray-400 text-sm">Torna presto per scoprire nuovi contenuti</p></div>',
                        'has_more' => false,
                        'total_count' => 0,
                        'loaded_count' => 0
                    ]);
                }

                $videos = new \Illuminate\Pagination\LengthAwarePaginator(
                    $videos,
                    $totalVideos,
                    12,
                    1,
                    ['path' => $request->url(), 'pageName' => 'page']
                );
            } else {
                $query = Video::with(['user', 'user.userProfile'])
                    ->published()
                    ->where('is_reel', false);

                if ($category && $category !== 'all') {
                    if ($category === 'videos') {
                        $query->where('is_reel', false);
                    } else {
                        $query->where('tags', 'like', '%' . $category . '%');
                    }
                }

                if ($sort) {
                    switch ($sort) {
                        case 'popular':
                            $query->orderBy('views_count', 'desc');
                            break;
                        case 'trending':
                            $query->where('published_at', '>=', now()->subDays(7))
                                ->orderBy('views_count', 'desc');
                            break;
                        default:
                            $query->inRandomOrder();
                    }
                }

                $totalVideos = (clone $query)->count();

                if ($totalVideos === 0) {
                    return response()->json([
                        'success' => true,
                        'html' => '<div class="col-span-full text-center py-16"><div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-video text-2xl text-gray-400"></i></div><h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun video disponibile</h3><p class="text-gray-600 dark:text-gray-400 text-sm">Torna presto per scoprire nuovi contenuti</p></div>',
                        'has_more' => false,
                        'total_count' => 0,
                        'loaded_count' => 0
                    ]);
                }

                $videos = $query->paginate(12);
            }
        }

        $html = '';
        foreach ($videos as $video) {
            if ($video->is_reel) {
                $html .= view('components.reel', compact('video'))->render();
            } else {
                $html .= view('components.video', compact('video'))->render();
            }
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'has_more' => $videos->hasMorePages(),
            'total_count' => $totalVideos,
            'loaded_count' => $videos->count()
        ]);
    }

    /**
     * Mostra la pagina di esplorazione
     */
    public function explore(Request $request)
    {
        $sortBy = $request->get('sort', 'latest');
        $category = $request->get('category');

        $query = Video::with(['user', 'user.userProfile'])
            ->published()
            ->where('is_reel', false);

        if ($category) {
            $query->where('tags', 'like', '%' . $category . '%');
        }

        switch ($sortBy) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'trending':
                $query->where('published_at', '>=', now()->subDays(7))
                    ->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            default:
                $query->orderBy('published_at', 'desc');
        }

        $videos = $query->paginate(30);

        $popularCategories = Video::published()
            ->where('is_reel', false)
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->groupBy(function ($tag) {
                return strtolower($tag);
            })
            ->map->count()
            ->sortDesc()
            ->take(10);

        return view('explore', compact('videos', 'popularCategories', 'sortBy', 'category'));
    }

    /**
     * Ricerca video
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $sortBy = $request->get('sort', 'relevance');

        if (!$query) {
            return redirect()->route('home');
        }

        $videoQuery = Video::with(['user', 'user.userProfile'])
            ->published()
            ->where('is_reel', false)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%')
                    ->orWhereJsonContains('tags', $query);
            });

        $reelQuery = Video::with(['user', 'user.userProfile'])
            ->published()
            ->where('is_reel', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%')
                    ->orWhereJsonContains('tags', $query);
            });

        $applySort = function ($queryBuilder) use ($sortBy) {
            switch ($sortBy) {
                case 'upload_date':
                    $queryBuilder->orderBy('published_at', 'desc');
                    break;
                case 'view_count':
                    $queryBuilder->orderBy('views_count', 'desc');
                    break;
                case 'rating':
                    $queryBuilder->orderBy('likes_count', 'desc');
                    break;
                default:
                    $queryBuilder->orderBy('views_count', 'desc')
                        ->orderBy('published_at', 'desc');
            }
        };

        $applySort($videoQuery);
        $applySort($reelQuery);

        $videoResults = $videoQuery->paginate(20, ['*'], 'videos_page');
        $reelResults = $reelQuery->paginate(12, ['*'], 'reels_page');

        $userQuery = User::with('userProfile')
            ->when(Auth::id(), function ($q) {
                $q->where('id', '!=', Auth::id());
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhereHas('userProfile', function ($profileQuery) use ($query) {
                        $profileQuery->where('channel_name', 'like', '%' . $query . '%')
                            ->orWhere('username', 'like', '%' . $query . '%');
                    });
            })
            ->orderBy('name', 'asc');

        $userResults = $userQuery->paginate(12, ['*'], 'users_page');

        return view('search', compact('videoResults', 'reelResults', 'userResults', 'query', 'sortBy'));
    }

    /**
     * Mostra i video più visti (trending) - Solo video normali
     */
    public function trending()
    {
        $videos = Video::with(['user', 'user.userProfile'])
            ->published()
            ->where('is_reel', false)
            ->where('published_at', '>=', now()->subDays(7))
            ->orderBy('views_count', 'desc')
            ->paginate(24);

        return view('trending', compact('videos'));
    }

    /**
     * Mostra i video pubblicati più di recente - Solo video normali
     */
    public function latest()
    {
        $videos = Video::with(['user', 'user.userProfile'])
            ->published()
            ->where('is_reel', false)
            ->orderBy('published_at', 'desc')
            ->paginate(24);

        return view('latest', compact('videos'));
    }

    public function setLanguage($lang)
    {
        Session::put('locale', $lang);

        $cookie = Cookie::make('locale', $lang, 525600);
        return redirect()->back()->withCookie($cookie);
    }
}
