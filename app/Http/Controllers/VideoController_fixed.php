<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\WatchHistory;
use App\Models\Comment;
use App\Models\Like;
use App\Notifications\NewLikeNotification;
use App\Notifications\NewCommentNotification;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Gestisce l'upload del video
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'video_file' => 'required|mimes:mp4,avi,mov,wmv,flv,webm|max:1000000000',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_public' => 'boolean',
            'tags' => 'nullable|string',
            'language' => 'nullable|string|size:2',
        ]);

        try {
            $video = $this->videoService->uploadVideo(
                $request->file('video_file'),
                $request->title,
                $request->description,
                $request->user(),
                $request->boolean('is_public'),
                $request->file('thumbnail'),
                $request->tags ? explode(',', $request->tags) : null,
                $request->language
            );

            return redirect()->route('videos.show', $video)
                ->with('success', 'Video caricato con successo! Il processing è in corso.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore durante il caricamento del video: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostra i video dell'utente corrente
     */
    public function myVideos()
    {
        $videos = Video::where('user_id', Auth::id())
            ->with('user.userProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('videos.my-videos', compact('videos'));
    }

    /**
     * Mostra la pagina di edit video
     */
    public function edit(Video $video)
    {
        $this->authorize('update', $video);

        return view('videos.edit', compact('video'));
    }

    /**
     * Aggiorna un video
     */
    public function update(Request $request, Video $video)
    {
        $this->authorize('update', $video);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_public' => 'boolean',
            'status' => 'required|in:published,draft',
            'tags' => 'nullable|string',
            'comments_enabled' => 'boolean',
            'likes_enabled' => 'boolean',
            'comments_require_approval' => 'boolean',
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
        if (!$updateData['comments_enabled']) {
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
        $this->authorize('delete', $video);

        try {
            // Elimina i file dal storage
            if ($video->video_path) {
                Storage::disk('public')->delete($video->video_path);
            }
            if ($video->original_file_path) {
                Storage::disk('public')->delete($video->original_file_path);
            }
            if ($video->thumbnail_path) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }

            // Elimina il video dal database
            $video->delete();

            return redirect()->route('videos.my-videos')
                ->with('success', 'Video eliminato con successo!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore durante l\'eliminazione del video: ' . $e->getMessage());
        }
    }

    /**
     * Mostra un video specifico
     */
    public function show(Video $video)
    {
        if ($video->status !== 'published' || !$video->is_public) {
            if (!Auth::check() || Auth::id() !== $video->user_id) {
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

        $relatedVideos = Video::published()
            ->where('id', '!=', $video->id)
            ->where(function ($query) use ($video) {
                $query->where('user_id', $video->user_id);

                if ($video->tags) {
                    foreach ($video->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->limit(6)
            ->get();

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

        return view('videos.show', compact('video', 'relatedVideos', 'shareUrl'));
    }

    /**
     * Scarica un video
     */
    public function download(Video $video)
    {
        if (!$video->video_path || !Storage::disk('public')->exists($video->video_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($video->video_path);
    }

    /**
     * Verifica lo status del video (per AJAX)
     */
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

    /**
     * Aggiunge un like al video
     */
    public function like(Video $video, Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Devi essere autenticato per mettere like/dislike');
        }

        // Verifica se i like sono abilitati
        if (!$this->likesEnabled($video)) {
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

            // Invia notifica
            if ($video->user_id !== $user->id) {
                $video->user->notify(new NewLikeNotification($newLike));
            }

            $message = ucfirst($type) . ' aggiunto';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'likes_count' => $video->fresh()->likes_count,
                'user_like_type' => $video->likes()->where('user_id', $user->id)->value('type')
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Verifica se i like sono abilitati per questo video
     */
    public function likesEnabled(Video $video): bool
    {
        return $video->likes_enabled ?? true;
    }

    /**
     * Verifica se i commenti sono abilitati per questo video
     */
    public function commentsEnabled(Video $video): bool
    {
        return $video->comments_enabled ?? true;
    }

    /**
     * Verifica se è richiesta l'approvazione per i commenti
     */
    public function approvalRequired(Video $video): bool
    {
        return $video->comments_require_approval ?? false;
    }

    /**
     * Aggiunge un commento al video
     */
    public function storeComment(Request $request, Video $video)
    {
        if (!$this->commentsEnabled($video)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'I commenti sono stati disabilitati'], 403);
            }
            return redirect()->back()->with('error', 'I commenti sono stati disabilitati');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Devi essere autenticato per commentare'], 401);
            }
            return redirect()->back()->with('error', 'Devi essere autenticato per commentare');
        }

        $comment = new Comment([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'status' => $this->approvalRequired($video) ? 'pending' : 'approved'
        ]);

        $video->comments()->save($comment);

        if (!$this->approvalRequired($video) && $video->user_id !== Auth::id()) {
            $video->user->notify(new NewCommentNotification($comment));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $this->approvalRequired($video) ? 
                    'Commento inviato e in attesa di approvazione' : 
                    'Commento aggiunto con successo',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'name' => Auth::user()->name,
                        'avatar' => Auth::user()->userProfile?->avatar_url
                    ],
                    'created_at' => $comment->created_at->diffForHumans(),
                    'status' => $comment->status
                ]
            ]);
        }

        $message = $this->approvalRequired($video) ? 
            'Commento inviato e in attesa di approvazione' : 
            'Commento aggiunto con successo';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Aggiunge un like a un commento
     */
    public function likeComment(Video $video, Comment $comment, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Devi essere autenticato'], 401);
        }

        if (!$this->likesEnabled($video)) {
            return response()->json(['error' => 'I like sono stati disabilitati'], 403);
        }

        $type = $request->get('type', 'like');
        $user = Auth::user();

        $existingLike = $comment->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            if ($existingLike->type === $type) {
                $existingLike->delete();
                $message = 'Like rimosso';
            } else {
                $existingLike->update(['type' => $type]);
                $message = 'Like aggiornato';
            }
        } else {
            $comment->likes()->create([
                'user_id' => $user->id,
                'type' => $type,
            ]);
            $message = 'Like aggiunto';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'likes_count' => $comment->fresh()->likes_count,
            'user_like_type' => $comment->likes()->where('user_id', $user->id)->value('type')
        ]);
    }

    /**
     * Elimina un commento
     */
    public function deleteComment(Video $video, Comment $comment, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Devi essere autenticato'], 401);
        }

        if ($comment->user_id !== Auth::id() && Auth::id() !== $video->user_id) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commento eliminato con successo'
        ]);
    }

    /**
     * Riprocessa un video
     */
    public function reprocess(Video $video)
    {
        $this->authorize('update', $video);

        try {
            $video->update([
                'status' => 'processing',
                'video_url' => null,
                'duration' => null,
                'thumbnail_url' => null
            ]);

            CompleteVideoJob::dispatch($video);

            return redirect()->back()->with('success', 'Riprocessamento avviato con successo!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore durante il riprocessamento: ' . $e->getMessage());
        }
    }
}