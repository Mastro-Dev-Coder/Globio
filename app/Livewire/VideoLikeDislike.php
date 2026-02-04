<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\Like;
use App\Notifications\NewLikeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VideoLikeDislike extends Component
{
    public Video $video;
    public ?string $userReaction = null;
    public int $likesCount = 0;
    public int $dislikesCount = 0;
    public bool $isLoading = false;

    public function mount(Video $video)
    {
        $this->video = $video;
        $this->updateCounts();
        $this->updateUserReaction();
    }

    /**
     * Verifica se i like sono abilitati (globale + per singolo video)
     */
    public function likesEnabled(): bool
    {
        return $this->video->areLikesEnabled();
    }

    public function updateCounts()
    {
        $this->likesCount = $this->video->likes()->where('reaction', 'like')->count();
        $this->dislikesCount = $this->video->likes()->where('reaction', 'dislike')->count();
    }

    public function updateUserReaction()
    {
        if (Auth::check()) {
            $this->userReaction = $this->video->getUserReaction(Auth::id());
        }
    }

    public function toggleLike()
    {
        // Verifica se i like sono abilitati
        if (!$this->likesEnabled()) {
            session()->flash('error', 'I like sono stati disabilitati per questo video');
            return;
        }

        $userId = Auth::id();
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->isLoading = true;
        
        try {
            DB::beginTransaction();
            
            $existingLike = $this->video->likes()->where('user_id', $userId)->first();
            $wasLiked = $existingLike && $existingLike->reaction === 'like';
            
            $this->video->toggleLike($userId);
            
            // Se l'utente non aveva già messo like, invia la notifica al proprietario del video
            if (!$wasLiked && !$existingLike || ($existingLike && $existingLike->reaction !== 'like')) {
                $like = $this->video->likes()->where('user_id', $userId)->first();
                if ($like && $this->video->user_id !== $userId) {
                    $this->video->user->notify(new NewLikeNotification($like));
                }
            }
            
            DB::commit();
            
            $this->updateCounts();
            $this->updateUserReaction();
            
            $this->dispatch('videoLiked', [
                'videoId' => $this->video->id,
                'likesCount' => $this->likesCount,
                'dislikesCount' => $this->dislikesCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();           
            session()->flash('error', 'Errore durante il processo del like. Riprova.');
            Log::error('Errore toggleLike', [
                'user_id' => $userId,
                'video_id' => $this->video->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function toggleDislike()
    {
        // Verifica se i like sono abilitati
        if (!$this->likesEnabled()) {
            session()->flash('error', 'I like sono stati disabilitati per questo video');
            return;
        }

        $userId = Auth::id();
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->isLoading = true;
        
        try {
            DB::beginTransaction();
            
            $existingLike = $this->video->likes()->where('user_id', $userId)->first();
            $wasDisliked = $existingLike && $existingLike->reaction === 'dislike';
            
            $this->video->toggleDislike($userId);
            
            // Se l'utente non aveva già messo dislike, invia la notifica al proprietario del video
            if (!$wasDisliked && !$existingLike || ($existingLike && $existingLike->reaction !== 'dislike')) {
                $like = $this->video->likes()->where('user_id', $userId)->first();
                if ($like && $this->video->user_id !== $userId) {
                    $this->video->user->notify(new NewLikeNotification($like));
                }
            }
            
            DB::commit();
            
            $this->updateCounts();
            $this->updateUserReaction();
                        
            $this->dispatch('videoDisliked', [
                'videoId' => $this->video->id,
                'likesCount' => $this->likesCount,
                'dislikesCount' => $this->dislikesCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();           
            session()->flash('error', 'Errore durante il processo del dislike. Riprova.');
            Log::error('Errore toggleDislike', [
                'user_id' => $userId,
                'video_id' => $this->video->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function formatCount(int $count): string
    {
        if ($count < 1000) {
            return (string) $count;
        } elseif ($count < 1000000) {
            return number_format($count / 1000, 1) . 'K';
        } else {
            return number_format($count / 1000000, 1) . 'M';
        }
    }

    public function render()
    {
        return view('livewire.video-like-dislike');
    }
}