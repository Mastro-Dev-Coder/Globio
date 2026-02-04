<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Video;
use App\Notifications\NewCommentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class VideoComments extends Component
{
    use WithPagination;

    public Video $video;
    public string $newComment = '';
    public ?int $replyingTo = null;
    public string $replyContent = '';
    public ?int $editingComment = null;
    public string $editContent = '';

    protected $rules = [
        'newComment' => 'required|min:1|max:1000',
        'replyContent' => 'required|min:1|max:1000',
        'editContent' => 'required|min:1|max:1000',
    ];

    protected $messages = [
        'newComment.required' => 'Il commento non può essere vuoto.',
        'newComment.max' => 'Il commento non può superare i 1000 caratteri.',
        'replyContent.required' => 'La risposta non può essere vuota.',
        'replyContent.max' => 'La risposta non può superare i 1000 caratteri.',
        'editContent.required' => 'Il commento non può essere vuoto.',
        'editContent.max' => 'Il commento non può superare i 1000 caratteri.',
    ];

    public function mount(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Verifica se i commenti sono abilitati (globale + per singolo video)
     */
    public function commentsEnabled(): bool
    {
        return $this->video->areCommentsEnabled();
    }

    /**
     * Verifica se i commenti richiedono approvazione per questo video
     */
    public function commentsRequireApproval(): bool
    {
        return $this->video->commentsRequireApproval();
    }

    public function addComment()
    {
        // Verifica se i commenti sono abilitati
        if (!$this->commentsEnabled()) {
            session()->flash('error', 'I commenti sono stati disabilitati per questo video');
            return;
        }

        if (!Auth::check()) {
            Log::warning('User not authenticated for comment', ['user_id' => Auth::id()]);
            $this->redirect(route('login'));
            return;
        }

        try {
            // Validazione
            $this->validate(['newComment' => 'required|min:1|max:1000']);

            // Creazione commento con status automatico
            $comment = Comment::create([
                'video_id' => $this->video->id,
                'user_id' => Auth::id(),
                'content' => trim($this->newComment),
            ]);

            // Invia notifica al proprietario del video se:
            // 1. Il commento è approvato
            // 2. Non è l'autore del video a commentare
            if ($comment->isApproved() && $this->video->user_id !== Auth::id()) {
                $this->video->user->notify(new NewCommentNotification($comment));
                Log::info('Notifica NewCommentNotification inviata', [
                    'comment_id' => $comment->id,
                    'video_id' => $this->video->id,
                    'video_owner_id' => $this->video->user_id,
                    'commenter_id' => Auth::id()
                ]);
            }

            // Resetta campo e pagina
            $this->newComment = '';
            $this->resetPage();

            // Aggiorna il modello video e dispatch evento
            $this->video->refresh();
            $this->dispatch('comment-added');

            // Messaggio di conferma basato sul requirement di approvazione
            if ($this->commentsRequireApproval()) {
                session()->flash('success', 'Commento inviato per approvazione. Sarà visibile dopo la revisione.');
            } else {
                session()->flash('success', 'Commento aggiunto con successo!');
            }

            Log::info('Comment created successfully', [
                'comment_id' => $comment->id,
                'video_id' => $this->video->id,
                'user_id' => Auth::id(),
                'status' => $comment->status,
                'requires_approval' => $this->commentsRequireApproval()
            ]);
        } catch (\Exception $e) {
            // Log dell'errore reale
            Log::error('Error creating comment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'video_id' => $this->video->id,
                'user_id' => Auth::id()
            ]);
        }
    }

    public function startReply(int $commentId)
    {
        // Verifica se i commenti sono abilitati
        if (!$this->commentsEnabled()) {
            session()->flash('error', 'I commenti sono stati disabilitati per questo video');
            return;
        }

        if (!Auth::check()) {
            Log::warning('User not authenticated for reply', ['user_id' => Auth::id()]);
            $this->redirect(route('login'));
            return;
        }

        $this->replyingTo = $commentId;
        $this->replyContent = '';
    }

    public function cancelReply()
    {
        Log::info('cancelReply called', ['replying_to' => $this->replyingTo]);
        $this->replyingTo = null;
        $this->replyContent = '';
    }

    public function addReply()
    {
        // Verifica se i commenti sono abilitati
        if (!$this->commentsEnabled()) {
            session()->flash('error', 'I commenti sono stati disabilitati per questo video');
            return;
        }

        if (!Auth::check()) {
            Log::warning('User not authenticated for reply', ['user_id' => Auth::id()]);
            $this->redirect(route('login'));
            return;
        }

        try {
            $this->validate(['replyContent' => 'required|min:1|max:1000']);

            $reply = Comment::create([
                'video_id' => $this->video->id,
                'user_id' => Auth::id(),
                'parent_id' => $this->replyingTo,
                'content' => trim($this->replyContent),
            ]);

            Log::info('Reply created', [
                'reply_id' => $reply->id,
                'parent_comment_id' => $this->replyingTo,
                'video_id' => $this->video->id,
                'user_id' => Auth::id(),
                'status' => $reply->status
            ]);

            $this->cancelReply();
            $this->dispatch('reply-added');
        } catch (\Exception $e) {
            Log::error('Error creating reply', [
                'error' => $e->getMessage(),
                'parent_comment_id' => $this->replyingTo,
                'user_id' => Auth::id()
            ]);
        }
    }

    public function startEdit(int $commentId)
    {
        if (!Auth::check()) {
            return;
        }

        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id === Auth::id()) {
            $this->editingComment = $commentId;
            $this->editContent = $comment->content;
            Log::info('Edit started', ['comment_id' => $commentId]);
        } else {
            Log::warning('Edit attempt failed - not owner', [
                'comment_id' => $commentId,
                'comment_owner' => $comment?->user_id,
                'current_user' => Auth::id()
            ]);
        }
    }

    public function cancelEdit()
    {
        $this->editingComment = null;
        $this->editContent = '';
    }

    public function updateComment()
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $this->validate(['editContent' => 'required|min:1|max:1000']);

            $comment = Comment::find($this->editingComment);
            if ($comment && $comment->user_id === Auth::id()) {
                $comment->update(['content' => trim($this->editContent)]);
                Log::info('Comment updated', ['comment_id' => $this->editingComment]);
            } else {
                Log::warning('Update failed - not owner', [
                    'comment_id' => $this->editingComment,
                    'comment_owner' => $comment?->user_id,
                    'current_user' => Auth::id()
                ]);
            }

            $this->cancelEdit();
        } catch (\Exception $e) {
            Log::error('Error updating comment', [
                'error' => $e->getMessage(),
                'comment_id' => $this->editingComment,
                'user_id' => Auth::id()
            ]);
        }
    }

    public function deleteComment(int $commentId)
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $comment = Comment::find($commentId);
            if ($comment && ($comment->user_id === Auth::id() || $this->video->user_id === Auth::id())) {
                $comment->delete();
                Log::info('Comment deleted', ['comment_id' => $commentId]);
            } else {
                Log::warning('Delete failed - insufficient permissions', [
                    'comment_id' => $commentId,
                    'comment_owner' => $comment?->user_id,
                    'video_owner' => $this->video->user_id,
                    'current_user' => Auth::id()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting comment', [
                'error' => $e->getMessage(),
                'comment_id' => $commentId,
                'user_id' => Auth::id()
            ]);
        }
    }

    public function toggleLike(int $commentId)
    {
        $comment = Comment::find($commentId);
        if (!$comment) return;

        $like = $comment->likes()->where('user_id', Auth::id())->first();

        if ($like?->reaction === 'like') {
            $like->delete();
            $comment->decrement('likes_count');
        } else {
            if ($like?->reaction === 'dislike') {
                $comment->decrement('dislikes_count');
                $like->update(['reaction' => 'like']);
            } else {
                $comment->likes()->create([
                    'user_id' => Auth::id(),
                    'reaction' => 'like'
                ]);
            }
            $comment->increment('likes_count');
        }

        $comment->refresh();
    }

    public function toggleDislike(int $commentId)
    {
        $comment = Comment::find($commentId);
        if (!$comment) return;

        $like = $comment->likes()->where('user_id', Auth::id())->first();

        if ($like?->reaction === 'dislike') {
            $like->delete();
            $comment->decrement('dislikes_count');
        } else {
            if ($like?->reaction === 'like') {
                $comment->decrement('likes_count');
                $like->update(['reaction' => 'dislike']);
            } else {
                $comment->likes()->create([
                    'user_id' => Auth::id(),
                    'reaction' => 'dislike'
                ]);
            }
            $comment->increment('dislikes_count');
        }

        $comment->refresh();
    }

    public function getUserReaction(int $commentId): ?string
    {
        if (!Auth::check()) return null;

        try {
            $like = Like::where([
                'likeable_type' => Comment::class,
                'likeable_id' => $commentId,
                'user_id' => Auth::id(),
            ])->first();

            return $like?->reaction;
        } catch (\Exception $e) {
            Log::error('Error getting user reaction', [
                'error' => $e->getMessage(),
                'comment_id' => $commentId,
                'user_id' => Auth::id()
            ]);
            return null;
        }
    }

    public function render()
    {
        try {
            // Usa il nuovo scope per ottenere solo i commenti visibili
            $comments = $this->video->visibleComments(Auth::id())
                ->whereNull('parent_id')
                ->with(['user', 'replies.user'])
                ->latest()
                ->paginate(10);

            $userReactions = [];
            if (Auth::check()) {
                $commentIds = $comments->pluck('id')->merge(
                    $comments->flatMap(fn($c) => $c->replies->pluck('id'))
                )->unique();

                $userReactions = Like::where('user_id', Auth::id())
                    ->whereIn('likeable_id', $comments->pluck('id'))
                    ->where('likeable_type', Comment::class)
                    ->get()
                    ->keyBy('likeable_id')
                    ->map(fn($like) => $like->reaction)
                    ->toArray();
            }

            return view('livewire.video-comments', [
                'comments' => $comments,
                'userReactions' => $userReactions,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in VideoComments render', [
                'error' => $e->getMessage(),
                'video_id' => $this->video->id
            ]);

            return view('livewire.video-comments', [
                'comments' => collect([]),
                'userReactions' => [],
            ]);
        }
    }
}
