<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\WatchHistory;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Playlist;
use App\Notifications\NewLikeNotification;
use App\Notifications\NewCommentNotification;
use App\Services\VideoService;
use App\Services\AutomatedPlaylistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function upload()
    {
        return view('videos.upload');
    }

    public function store(Request $request)
    {
        $maxVideoUploadMb = \App\Models\Setting::getValue('max_video_upload_mb', 500);
        $maxVideoUploadBytes = $maxVideoUploadMb * 1024 * 1024;
        $maxThumbnailSize = 5 * 1024 * 1024;

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'video_file' => "required|mimes:mp4,avi,mov,wmv,flv,webm|max:{$maxVideoUploadBytes}",
            'thumbnail' => "nullable|image|mimes:jpeg,png,jpg,webp|max:" . ($maxThumbnailSize / 1024),
            'is_public' => 'boolean',
            'is_reel' => 'boolean',
            'tags' => 'nullable|string',
            'language' => 'nullable|string|size:2',
        ]);

        try {
            $uploadData = $request->except('video_file');
            $uploadData['is_reel'] = $request->boolean('is_reel', false);

            $video = $this->videoService->uploadVideo(
                $request->file('video_file'),
                $uploadData,
                Auth::id()
            );

            if ($request->hasFile('thumbnail')) {
                if (!file_exists(public_path('thumbnails'))) {
                    mkdir(public_path('thumbnails'), 0755, true);
                }
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $video->update(['thumbnail_path' => $thumbnailPath]);
            }

            $redirectRoute = $video->is_reel ? 'reels.show' : 'videos.show';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Video caricato con successo! Verrà pubblicato quando il processamento sarà completato.',
                    'redirect' => route($redirectRoute, $video),
                    'video_id' => $video->id
                ]);
            }

            return redirect()->route($redirectRoute, $video)
                ->with('success', 'Video caricato con successo! Verrà pubblicato quando il processamento sarà completato.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore durante l\'upload: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Errore durante l\'upload: ' . $e->getMessage()]);
        }
    }

    public function show($video)
    {
        $video = Video::where('video_url', $video)->first();

        if (!$video) {
            abort(404);
        }

        if ($video->is_reel && $video->status === 'published') {
            return redirect()->route('reels.show', $video);
        }

        if (!$video->is_public || !in_array($video->status, ['published', 'rejected'])) {
            if (!Auth::check() || Auth::id() !== $video->user_id) {
                if (in_array($video->status, ['processing', 'transcoding', 'rejected'])) {
                    return view('videos.processing', compact('video'));
                }
                abort(404);
            }
        }

        if ($video->is_public && $video->status === 'published') {
            $video->incrementViews();
        }

        $video->load([
            'user.userProfile',
            'comments.user.userProfile',
            'likes' => function ($query) {
                $query->where('user_id', Auth::id());
            }
        ]);

        // Get related videos for sidebar
        $relatedVideos = Video::published()
            ->where('id', '!=', $video->id)
            ->where('is_reel', false)
            ->where(function ($query) use ($video) {
                $query->where('user_id', $video->user_id);

                if ($video->tags) {
                    foreach ($video->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->limit(10)
            ->get();

        // Get suggested playlists for sidebar
        $suggestedPlaylists = collect();
        if (Auth::check()) {
            $autoPlaylistService = new AutomatedPlaylistService();
            $suggestedPlaylists = $autoPlaylistService->getSuggestedPlaylistsForSidebar(
                Auth::id(),
                $video,
                3
            );
        }

        // Get next video from playlist if video is in a playlist
        $nextVideo = null;
        $playlistNextVideos = [];
        $currentPlaylistId = null;

        // Check if video is in any playlist
        $playlistVideo = \App\Models\PlaylistVideo::where('video_id', $video->id)->first();
        if ($playlistVideo) {
            $currentPlaylistId = $playlistVideo->playlist_id;

            // Get ALL remaining videos in the playlist
            $remainingPlaylistVideos = \App\Models\PlaylistVideo::where('playlist_id', $playlistVideo->playlist_id)
                ->where('position', '>=', $playlistVideo->position)
                ->orderBy('position', 'asc')
                ->get();

            // Build the queue of videos to play
            $playlistNextVideos = [];
            $foundCurrent = false;
            foreach ($remainingPlaylistVideos as $pv) {
                if (!$foundCurrent) {
                    // Skip current video (first in remaining)
                    if ($pv->video_id == $video->id) {
                        $foundCurrent = true;
                        continue;
                    }
                }
                $nextVideoInPlaylist = \App\Models\Video::published()->find($pv->video_id);
                if ($nextVideoInPlaylist) {
                    $playlistNextVideos[] = $nextVideoInPlaylist;
                }
            }

            // Set next video to first in queue
            if (!empty($playlistNextVideos)) {
                $nextVideo = $playlistNextVideos[0];
            }
        }

        // If no next video from playlist, use first related video for autoplay
        if (!$nextVideo && $relatedVideos->isNotEmpty()) {
            $nextVideo = $relatedVideos->first();
        }

        if (Auth::check()) {
            WatchHistory::updateOrCreate(
                ['user_id' => Auth::id(), 'video_id' => $video->id],
                [
                    'last_watched_at' => now(),
                    'total_duration' => $video->duration,
                ]
            );
        }

        $shareUrl = route('videos.show', $video);

        // Gestione stato del mini player
        $restoreMiniPlayerState = false;
        $miniPlayerVideoId = null;
        $miniPlayerStartTime = 0;
        $miniPlayerLastVideo = null;

        if (Auth::check()) {
            $miniPlayerState = session()->get('mini_player_state');

            if ($miniPlayerState && $miniPlayerState['video_id'] !== $video->id) {
                // Se c'è uno stato salvato per un altro video, prova a ripristinarlo
                $lastWatchedVideo = Video::find($miniPlayerState['video_id']);
                if ($lastWatchedVideo && $lastWatchedVideo->status === 'published' && $lastWatchedVideo->is_public) {
                    $miniPlayerLastVideo = [
                        'id' => $lastWatchedVideo->id,
                        'title' => $lastWatchedVideo->title,
                        'video_url' => $lastWatchedVideo->video_url,
                        'thumbnail_url' => $lastWatchedVideo->thumbnail_url,
                        'duration' => $lastWatchedVideo->duration,
                        'current_time' => $miniPlayerState['current_time'] ?? 0,
                        'last_watched_at' => $miniPlayerState['last_watched_at'] ?? null
                    ];
                    $restoreMiniPlayerState = true;
                }
            }

            // Controlla se l'utente ha visto questo video prima
            $watchHistory = WatchHistory::where('user_id', Auth::id())
                ->where('video_id', $video->id)
                ->first();

            if ($watchHistory && $watchHistory->watched_duration > 0) {
                $miniPlayerVideoId = $video->id;
                $miniPlayerStartTime = $watchHistory->watched_duration;
            }
        }

        return view('videos.show', compact(
            'video',
            'relatedVideos',
            'suggestedPlaylists',
            'shareUrl',
            'restoreMiniPlayerState',
            'miniPlayerVideoId',
            'miniPlayerStartTime',
            'miniPlayerLastVideo',
            'nextVideo',
            'playlistNextVideos',
            'currentPlaylistId'
        ));
    }

    /**
     * Mostra un reel in formato TikTok/YouTube Shorts
     */
    public function showReel($video)
    {
        $video = Video::where('video_url', $video)->first();

        if (!$video) {
            abort(404);
        }

        if (!$video->is_reel) {
            return redirect()->route('videos.show', $video);
        }

        if (!$video->is_public || !in_array($video->status, ['published', 'rejected'])) {
            if (!Auth::check() || Auth::id() !== $video->user_id) {
                if (in_array($video->status, ['processing', 'transcoding', 'rejected'])) {
                    return view('videos.processing', compact('video'));
                }
                abort(404);
            }
        }

        if ($video->is_public && $video->status === 'published') {
            $video->incrementViews();
        }

        $video->load([
            'user.userProfile',
            'comments.user.userProfile',
            'likes' => function ($query) {
                $query->where('user_id', Auth::id());
            }
        ]);

        if (Auth::check()) {
            WatchHistory::updateOrCreate(
                ['user_id' => Auth::id(), 'video_id' => $video->id],
                [
                    'last_watched_at' => now(),
                    'total_duration' => $video->duration,
                ]
            );
        }

        // Variabili per il miniplayer (necessarie per il layout)
        $restoreMiniPlayerState = false;
        $miniPlayerVideoId = null;
        $miniPlayerStartTime = 0;
        $miniPlayerLastVideo = null;

        if (Auth::check()) {
            $miniPlayerState = session()->get('mini_player_state');

            if ($miniPlayerState && $miniPlayerState['video_id'] !== $video->id) {
                // Se c'è uno stato salvato per un altro video, prova a ripristinarlo
                $lastWatchedVideo = Video::find($miniPlayerState['video_id']);
                if ($lastWatchedVideo && $lastWatchedVideo->status === 'published' && $lastWatchedVideo->is_public) {
                    $miniPlayerLastVideo = [
                        'id' => $lastWatchedVideo->id,
                        'title' => $lastWatchedVideo->title,
                        'video_url' => $lastWatchedVideo->video_url,
                        'thumbnail_url' => $lastWatchedVideo->thumbnail_url,
                        'duration' => $lastWatchedVideo->duration,
                        'current_time' => $miniPlayerState['current_time'] ?? 0,
                        'last_watched_at' => $miniPlayerState['last_watched_at'] ?? null
                    ];
                    $restoreMiniPlayerState = true;
                }
            }

            // Controlla se l'utente ha visto questo video prima
            $watchHistory = WatchHistory::where('user_id', Auth::id())
                ->where('video_id', $video->id)
                ->first();

            if ($watchHistory && $watchHistory->watched_duration > 0) {
                $miniPlayerVideoId = $video->id;
                $miniPlayerStartTime = $watchHistory->watched_duration;
            }
        }

        // Carica tutti i reel disponibili
        $allReels = Video::published()
            ->where('is_reel', true)
            ->with(['user.userProfile'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'title' => $v->title,
                    'description' => $v->description,
                    'thumbnail_path' => $v->thumbnail_path,
                    'video_path' => $v->video_path,
                    'video_url' => $v->video_url,
                    'duration' => $v->duration,
                    'views_count' => $v->views_count,
                    'comments_count' => $v->comments_count,
                    'created_at' => $v->created_at->toISOString(),
                    'user' => [
                        'id' => $v->user->id,
                        'name' => $v->user->name,
                        'channel_name' => $v->user->userProfile?->channel_name,
                        'avatar_url' => $v->user->userProfile?->avatar_url,
                        'subscribers' => $v->user->subscribers()->count(),
                    ],
                ];
            })->toArray();

        // Trova l'indice corrente
        $currentReelIndex = 0;
        foreach ($allReels as $index => $reel) {
            if ($reel['id'] == $video->id) {
                $currentReelIndex = $index;
                break;
            }
        }

        $totalReels = count($allReels);

        // Reel correlati (escludendo quello corrente)
        $relatedReels = array_filter($allReels, function ($r) use ($video) {
            return $r['id'] != $video->id;
        });
        $relatedReels = array_values($relatedReels);

        // Variabili per la velocità e loop
        $isLooping = false;
        $currentSpeed = 1;

        // Passa il video e le variabili del miniplayer al componente Livewire
        return view('reels.show', compact(
            'video',
            'allReels',
            'currentReelIndex',
            'totalReels',
            'relatedReels',
            'restoreMiniPlayerState',
            'miniPlayerVideoId',
            'miniPlayerStartTime',
            'miniPlayerLastVideo',
            'isLooping',
            'currentSpeed'
        ));
    }

    public function download($video)
    {
        // Cerca il video usando il video_url invece dell'ID
        $video = Video::where('video_url', $video)->first();

        if (!$video) {
            abort(404);
        }

        if (!$video->video_path || !Storage::disk('public')->exists($video->video_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($video->video_path, $video->title . '.mp4');
    }

    public function checkStatus(Video $video)
    {
        return response()->json([
            'status' => $video->status,
            'is_public' => $video->is_public,
            'video_url' => $video->video_url,
            'can_view' => $video->status === 'published' && $video->is_public,
            'thumbnail_url' => $video->thumbnail_url,
            'formatted_duration' => $video->formatted_duration,
            'updated_at' => $video->updated_at->toISOString()
        ]);
    }

    public function like(Video $video, Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Devi essere autenticato per mettere like/dislike');
        }

        if (!$this->likesEnabled()) {
            return redirect()->back()->with('error', 'I like sono stati disabilitati dall\'amministratore');
        }

        $user = Auth::user();
        $type = $request->get('type', 'like');

        $existingLike = $video->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            if ($existingLike->type === $type) {
                $existingLike->delete();
                $message = ucfirst($type) . ' rimosso';
            } else {
                $existingLike->update(['type' => $type]);
                $message = ucfirst($type) . ' aggiornato';
            }
        } else {
            $newLike = $video->likes()->create([
                'user_id' => $user->id,
                'type' => $type,
            ]);

            if ($type === 'like' && $video->user_id !== $user->id) {
                $videoOwner = $video->user;
                if ($videoOwner) {
                    $videoOwner->notify(new NewLikeNotification($newLike));
                }
            }

            $message = ucfirst($type) . ' aggiunto';
        }

        return redirect()->back()->with('success', $message);
    }

    public function toggleLike(Video $video, Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Devi essere autenticato per mettere like',
                'liked' => false
            ], 401);
        }

        try {
            $video->toggleLike(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Operazione completata con successo!',
                'liked' => true,
                'likes_count' => $video->fresh()->likes_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'operazione: ' . $e->getMessage(),
                'liked' => false
            ], 500);
        }
    }

    /**
     * Mostra i video dell'utente
     */
    public function myVideos()
    {
        $videos = Video::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('users.my-videos', compact('videos'));
    }

    /**
     * Modifica un video
     */
    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    /**
     * Aggiorna un video
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_public' => 'boolean',
            'status' => 'required|in:published,draft',
            'comments_enabled' => 'boolean',
            'likes_enabled' => 'boolean',
            'comments_require_approval' => 'boolean',
            'tags' => 'nullable|string',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public'),
            'status' => $request->status,
            'tags' => $request->tags ? explode(',', $request->tags) : null,
            'comments_enabled' => $request->boolean('comments_enabled'),
            'likes_enabled' => $request->boolean('likes_enabled'),
            'comments_require_approval' => $request->boolean('comments_require_approval'),
        ];

        // Se i commenti sono disabilitati, non richiedere l'approvazione
        if (!$request->boolean('comments_enabled')) {
            $updateData['comments_require_approval'] = false;
        }

        // Se il video viene pubblicato per la prima volta, imposta published_at
        if ($request->status === 'published' && $video->status !== 'published') {
            $updateData['published_at'] = now();
        }

        $video->update($updateData);

        $statusMessage = $request->status === 'published' ? 'Video pubblicato e aggiornato con successo!' : 'Video salvato come bozza con successo!';

        return redirect()->route('videos.show', $video)
            ->with('success', $statusMessage);
    }

    /**
     * Elimina un video
     */
    public function destroy(Video $video)
    {
        $this->videoService->deleteVideo($video);

        return redirect()->route('videos.my')
            ->with('success', 'Video eliminato con successo!');
    }

    /**
     * Aggiorna il progresso di visione
     */
    public function updateWatchProgress(Request $request, Video $video)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Devi essere autenticato'], 401);
        }

        $request->validate([
            'watched_duration' => 'required|integer|min:0',
        ]);

        WatchHistory::updateOrCreate(
            ['user_id' => Auth::id(), 'video_id' => $video->id],
            [
                'watched_duration' => $request->watched_duration,
                'total_duration' => $video->duration,
                'completed' => $request->watched_duration >= ($video->duration * 0.9),
                'last_watched_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Aggiorna il tempo di visione per il miniplayer
     */
    public function updateWatchTime(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Devi essere autenticato'], 401);
        }

        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'current_time' => 'required|numeric|min:0',
        ]);

        $video = Video::findOrFail($request->video_id);

        // Aggiorna o crea la cronologia di visione
        WatchHistory::updateOrCreate(
            ['user_id' => Auth::id(), 'video_id' => $video->id],
            [
                'watched_duration' => $request->current_time,
                'total_duration' => $video->duration,
                'completed' => $request->current_time >= ($video->duration * 0.9),
                'last_watched_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tempo di visione aggiornato'
        ]);
    }

    /**
     * Salva un nuovo commento
     */
    public function storeComment(Request $request, Video $video)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Devi essere autenticato per commentare'
                ], 401);
            }
            return redirect()->back()->with('error', 'Devi essere autenticato per commentare');
        }

        // Verifica se i commenti sono abilitati
        if (!$this->commentsEnabled()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'I commenti sono stati disabilitati dall\'amministratore'
                ], 403);
            }
            return redirect()->back()->with('error', 'I commenti sono stati disabilitati dall\'amministratore');
        }

        $request->validate([
            'content' => 'required|string|max:1000|min:1',
        ]);

        try {
            $comment = $video->comments()->create([
                'user_id' => Auth::id(),
                'content' => trim($request->input('content')),
                'likes_count' => 0,
            ]);

            // Carica le relazioni necessarie per la risposta
            $comment->load('user.userProfile');

            // Invia notifica solo se l'autore del commento non è l'autore del video
            if ($video->user_id !== Auth::id()) {
                $videoOwner = $video->user;
                if ($videoOwner) {
                    $videoOwner->notify(new NewCommentNotification($comment));
                }
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Commento aggiunto con successo!',
                    'comment' => [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at->diffForHumans(),
                        'likes_count' => $comment->likes_count,
                        'user' => [
                            'name' => $comment->user->userProfile?->channel_name ?: $comment->user->name,
                            'avatar_url' => $comment->user->userProfile?->avatar_url,
                            'initial' => strtoupper(substr($comment->user->name, 0, 1)),
                        ]
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Commento aggiunto con successo!');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore durante l\'invio del commento: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore durante l\'invio del commento. Riprova.');
        }
    }

    /**
     * Aggiunge un like a un commento
     */
    public function likeComment(Video $video, Comment $comment, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Devi essere autenticato'], 401);
        }

        // Verifica che il commento appartenga al video
        if ($comment->video_id !== $video->id) {
            return response()->json(['error' => 'Commento non valido'], 404);
        }

        // Verifica se i like sono abilitati
        if (!$this->likesEnabled()) {
            return response()->json(['error' => 'I like sono stati disabilitati dall\'amministratore'], 403);
        }

        $user = Auth::user();

        $existingLike = $comment->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $comment->likes_count = max(0, $comment->likes_count - 1);
            $comment->save();
            $liked = false;
            $message = 'Like rimosso';
        } else {
            $comment->likes()->create([
                'user_id' => $user->id,
                'type' => 'like',
            ]);
            $comment->likes_count = $comment->likes_count + 1;
            $comment->save();
            $liked = true;
            $message = 'Like aggiunto';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $comment->likes_count,
                'message' => $message
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Elimina un commento
     */
    public function deleteComment(Video $video, Comment $comment, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Devi essere autenticato'], 401);
        }

        $user = Auth::user();

        // Verifica che il commento appartenga al video
        if ($comment->video_id !== $video->id) {
            return response()->json(['error' => 'Commento non valido'], 404);
        }

        // Solo l'autore del commento o l'autore del video può eliminare
        if ($comment->user_id !== $user->id && $video->user_id !== $user->id) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        try {
            $comment->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Commento eliminato con successo'
                ]);
            }

            return redirect()->back()->with('success', 'Commento eliminato con successo');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore durante l\'eliminazione del commento'
                ], 500);
            }

            return redirect()->back()->with('error', 'Errore durante l\'eliminazione del commento');
        }
    }

    /**
     * Riprocessa un video rifiutato
     */
    public function reprocess(Video $video)
    {
        // Verifica che l'utente sia il proprietario del video
        if (Auth::id() !== $video->user_id) {
            abort(403, 'Non autorizzato');
        }

        // Verifica che il video sia rifiutato
        if ($video->status !== 'rejected') {
            return back()->with('error', 'Solo i video rifiutati possono essere riprocessati.');
        }

        // Verifica se il file originale è disponibile
        if (!$video->original_file_path) {
            return back()->with('error', 'Impossibile riprocessare questo video perché il file originale non è più disponibile. Ti consigliamo di caricare nuovamente il video.');
        }

        try {
            $this->videoService->reprocessRejectedVideo($video->id);

            return back()->with('success', 'Riprocessamento avviato! Riceverai una notifica quando sarà completato.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            // Messaggi personalizzati per errori comuni
            if (strpos($errorMessage, 'File originale non trovato') !== false) {
                $errorMessage = 'Il file originale non è più disponibile sul server. Ti consigliamo di caricare nuovamente il video.';
            } elseif (strpos($errorMessage, 'File non trovato') !== false) {
                $errorMessage = 'Il file originale non è più disponibile. Ti consigliamo di caricare nuovamente il video.';
            }

            return back()->with('error', 'Errore durante il riprocessamento: ' . $errorMessage);
        }
    }

    /**
     * Verifica se i commenti sono abilitati
     */
    public function commentsEnabled(): bool
    {
        return \App\Models\Setting::getValue('enable_comments', true);
    }

    /**
     * Verifica se i like sono abilitati
     */
    public function likesEnabled(): bool
    {
        return \App\Models\Setting::getValue('enable_likes', true);
    }

    /**
     * Verifica se l'approvazione dei video è richiesta
     */
    public function approvalRequired(): bool
    {
        return \App\Models\Setting::getValue('require_approval', false);
    }

    /**
     * Gestisce l'aggiornamento di massa dei video
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:set_public,set_private',
            'video_ids' => 'required|array',
            'video_ids.*' => 'exists:videos,id'
        ]);

        try {
            $videoIds = $request->video_ids;
            $action = $request->action;

            $userVideos = Video::whereIn('id', $videoIds)
                ->where('user_id', Auth::id())
                ->get();

            if ($userVideos->count() !== count($videoIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alcuni video non sono stati trovati o non ti appartengono.'
                ], 403);
            }

            $updateData = [];
            if ($action === 'set_public') {
                $updateData['is_public'] = true;
            } elseif ($action === 'set_private') {
                $updateData['is_public'] = false;
            }

            if (!empty($updateData)) {
                Video::whereIn('id', $videoIds)
                    ->where('user_id', Auth::id())
                    ->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Operazione completata con successo.',
                'updated_count' => $userVideos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'operazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gestisce l'eliminazione di massa dei video
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'video_ids' => 'required|array',
            'video_ids.*' => 'exists:videos,id'
        ]);

        try {
            $videoIds = $request->video_ids;

            // Verifica che tutti i video appartengano all'utente corrente
            $userVideos = Video::whereIn('id', $videoIds)
                ->where('user_id', Auth::id())
                ->get();

            if ($userVideos->count() !== count($videoIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alcuni video non sono stati trovati o non ti appartengono.'
                ], 403);
            }

            // Elimina tutti i video
            foreach ($userVideos as $video) {
                $this->videoService->deleteVideo($video);
            }

            return response()->json([
                'success' => true,
                'message' => 'Video eliminati con successo.',
                'deleted_count' => $userVideos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle privacy di un singolo video
     */
    public function togglePrivacy(Video $video)
    {
        // Verifica che il video appartenga all'utente corrente
        if ($video->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato.'
            ], 403);
        }

        try {
            $video->update([
                'is_public' => !$video->is_public
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visibilità video aggiornata.',
                'is_public' => $video->is_public
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottiene il reel precedente in ordine di creazione
     */
    public function getPreviousReel(Video $currentReel)
    {
        return Video::published()
            ->where('is_reel', true)
            ->where('id', '<', $currentReel->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Ottiene il reel successivo in ordine di creazione
     */
    public function getNextReel(Video $currentReel)
    {
        return Video::published()
            ->where('is_reel', true)
            ->where('id', '>', $currentReel->id)
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * API endpoint per ottenere reel precedente/successivo via AJAX
     */
    public function getAdjacentReel(Request $request, Video $video)
    {
        $direction = $request->get('direction', 'next');

        $adjacentReel = $direction === 'previous'
            ? $this->getPreviousReel($video)
            : $this->getNextReel($video);

        if (!$adjacentReel) {
            return response()->json([
                'success' => false,
                'message' => 'Nessun reel ' . ($direction === 'previous' ? 'precedente' : 'successivo') . ' trovato'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'reel' => [
                'id' => $adjacentReel->id,
                'title' => $adjacentReel->title,
                'thumbnail_url' => $adjacentReel->thumbnail_path ? asset('storage/' . $adjacentReel->thumbnail_path) : null,
                'url' => route('reels.show', $adjacentReel)
            ]
        ]);
    }

    /**
     * API endpoint per ottenere un reel casuale (per navigazione scroll)
     */
    public function getRandomReel(Request $request, Video $currentVideo)
    {
        // Gestisci l'array di exclude_ids in diversi formati
        $excludeIds = [];

        if ($request->has('exclude_ids')) {
            $excludeIdsParam = $request->get('exclude_ids');

            // Se è una stringa JSON, decodificala
            if (is_string($excludeIdsParam)) {
                try {
                    $decoded = json_decode($excludeIdsParam, true);
                    if (is_array($decoded)) {
                        $excludeIds = $decoded;
                    } else {
                        // Prova a trattarlo come lista separata da virgole
                        $excludeIds = explode(',', $excludeIdsParam);
                    }
                } catch (\Exception $e) {
                    // Se il JSON non è valido, prova con virgola
                    $excludeIds = explode(',', $excludeIdsParam);
                }
            } elseif (is_array($excludeIdsParam)) {
                $excludeIds = $excludeIdsParam;
            }
        }

        // Converte tutti gli ID in interi e rimuovi duplicati e valori null
        $excludeIds = array_unique(array_filter(array_map('intval', $excludeIds)));

        // Aggiungi sempre l'ID del video corrente alla lista di esclusione
        $excludeIds[] = $currentVideo->id;

        try {
            $query = Video::published()->where('is_reel', true);

            // Se ci sono ID da escludere, applica il filtro
            if (!empty($excludeIds)) {
                $query->whereNotIn('id', $excludeIds);
            }

            $randomReel = $query->inRandomOrder()->first();

            if (!$randomReel) {
                // Se non ci sono altri reel disponibili, prova a prendere qualsiasi reel
                // (incluso quello corrente) ma escludendo sempre l'ID corrente se possibile
                $fallbackQuery = Video::published()->where('is_reel', true);
                if (!empty($excludeIds)) {
                    $fallbackQuery->whereNotIn('id', $excludeIds);
                }
                $randomReel = $fallbackQuery->inRandomOrder()->first();

                // Se ancora non c'è nulla, prendi qualsiasi reel
                if (!$randomReel) {
                    $randomReel = Video::published()
                        ->where('is_reel', true)
                        ->inRandomOrder()
                        ->first();
                }
            }

            if (!$randomReel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nessun reel disponibile nel sistema'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'reel' => [
                    'id' => $randomReel->id,
                    'title' => $randomReel->title,
                    'thumbnail_url' => $randomReel->thumbnail_path ? asset('storage/' . $randomReel->thumbnail_path) : null,
                    'url' => route('reels.show', $randomReel)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il recupero del reel casuale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostra la pagina principale dei reels
     * Carica tutti i reels disponibili e reindirizza al primo reel disponibile
     */
    public function reelsIndex(Request $request)
    {
        try {
            // Prende il primo reel disponibile (più recente)
            $firstReel = Video::published()
                ->where('is_reel', true)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$firstReel) {
                // Se non ci sono reels disponibili, mostra la home
                return redirect()->route('home')
                    ->with('info', 'Nessun reel disponibile al momento.');
            }

            // Reindirizza immediatamente al primo reel disponibile
            return redirect()->route('reels.show', $firstReel);
        } catch (\Exception $e) {
            Log::error('Errore nel caricamento della pagina reels', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('home')
                ->with('error', 'Errore nel caricamento dei reels.');
        }
    }

    /**
     * API endpoint per ottenere tutti i reels pubblicati
     */
    public function getAllReels(Request $request)
    {
        try {
            $reels = Video::published()
                ->where('is_reel', true)
                ->with(['user.userProfile'])
                ->orderBy('created_at', 'desc')
                ->get(['id', 'title', 'video_path', 'thumbnail_path', 'created_at', 'user_id', 'views_count', 'likes_count', 'comments_count', 'description', 'video_url']);

            $reelsData = $reels->map(function ($reel) {
                return [
                    'id' => $reel->id,
                    'title' => $reel->title,
                    'description' => $reel->description,
                    'url' => route('reels.show', $reel),
                    'video_path' => $reel->video_path ? asset('storage/' . $reel->video_path) : null,
                    'thumbnail_path' => $reel->thumbnail_path ? asset('storage/' . $reel->thumbnail_path) : null,
                    'created_at' => $reel->created_at->toISOString(),
                    'views_count' => $reel->views_count,
                    'likes_count' => $reel->likes_count,
                    'comments_count' => $reel->comments_count,
                    'user' => [
                        'name' => $reel->user->name,
                        'email' => $reel->user->email,
                        'channel_name' => $reel->user->userProfile?->channel_name,
                        'avatar_url' => $reel->user->userProfile?->avatar_url ? asset('storage/' . $reel->user->userProfile->avatar_url) : null,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'reels' => $reelsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei reels: ' . $e->getMessage()
            ], 500);
        }
    }
}
