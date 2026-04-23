<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VideoLikeDislikeCompact extends Component
{
    public Video $video;
    public ?string $userReaction = null;
    public int $likesCount = 0;
    public int $dislikesCount = 0;

    public function mount(Video $video)
    {
        $this->video = $video;
        $this->updateCounts();
        $this->updateUserReaction();
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
        $userId = Auth::id();
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            DB::beginTransaction();
            
            $this->video->toggleLike($userId);
            
            DB::commit();
            
            $this->updateCounts();
            $this->updateUserReaction();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Errore durante il processo del like. Riprova.');
        }
    }

    public function toggleDislike()
    {
        $userId = Auth::id();
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            DB::beginTransaction();
            
            $this->video->toggleDislike($userId);
            
            DB::commit();
            
            $this->updateCounts();
            $this->updateUserReaction();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Errore durante il processo del dislike. Riprova.');
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
        return view('livewire.video-like-dislike-compact');
    }
}