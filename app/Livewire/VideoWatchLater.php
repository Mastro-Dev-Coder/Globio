<?php

namespace App\Livewire;

use App\Models\Video;
use App\Models\WatchLater;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VideoWatchLater extends Component
{
    public Video $video;
    public bool $isInWatchLater = false;
    public bool $isLoading = false;
    public bool $compact = false;

    protected $listeners = [
        'refreshWatchLaterStatus' => 'checkWatchLaterStatus',
        'removeFromWatchLater' => 'removeFromWatchLater',
        'updateWatchLaterStatus' => 'updateWatchLaterStatus',
        'updateVideoForWatchLater' => 'updateVideo'
    ];

    public function mount(Video $video, bool $compact = false)
    {
        $this->video = $video;
        $this->compact = $compact;
        $this->checkWatchLaterStatus();
    }

    /**
     * Verifica se il video è nella watch later list dell'utente
     */
    public function checkWatchLaterStatus()
    {
        if (Auth::check()) {
            $this->isInWatchLater = WatchLater::isInWatchLater(Auth::id(), $this->video->id);
        } else {
            $this->isInWatchLater = false;
        }
    }

    /**
     * Toggle watch later - aggiunge o rimuove il video dalla watch later list
     */
    public function toggleWatchLater()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Devi effettuare l\'accesso per salvare i video.');
            return;
        }

        $this->isLoading = true;

        try {
            $userId = Auth::id();
            $videoId = $this->video->id;

            if (WatchLater::toggleWatchLater($userId, $videoId)) {
                $this->isInWatchLater = !$this->isInWatchLater;

                // Broadcast evento per aggiornare altri componenti
                $this->dispatch('watchLaterStatusChanged', [
                    'videoId' => $videoId,
                    'isInWatchLater' => $this->isInWatchLater
                ]);

                // Notifica di successo
                $message = $this->isInWatchLater
                    ? 'Video salvato per guardarlo più tardi!'
                    : 'Video rimosso da Guarda più tardi';

                $this->dispatch('show-toast', [
                    'message' => $message,
                    'type' => 'success'
                ]);
            } else {
                $this->dispatch('show-toast', [
                    'message' => 'Errore nel salvataggio. Riprova.',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error toggling watch later', [
                'user_id' => Auth::id(),
                'video_id' => $this->video->id,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('show-toast', [
                'message' => 'Errore nel salvataggio. Riprova.',
                'type' => 'error'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Rimuove specificatamente il video dai watch later
     */
    public function removeFromWatchLater()
    {
        if (!Auth::check()) {
            return;
        }

        $this->isLoading = true;

        try {
            $userId = Auth::id();
            $videoId = $this->video->id;

            if (WatchLater::removeFromWatchLater($userId, $videoId)) {
                $this->isInWatchLater = false;

                // Broadcast evento per aggiornare altri componenti
                $this->dispatch('watchLaterStatusChanged', [
                    'videoId' => $videoId,
                    'isInWatchLater' => false
                ]);

                $this->dispatch('show-toast', [
                    'message' => 'Video rimosso da Guarda più tardi',
                    'type' => 'info'
                ]);
            } else {
                $this->dispatch('show-toast', [
                    'message' => 'Errore nel rimuovere il video',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error removing from watch later', [
                'user_id' => Auth::id(),
                'video_id' => $this->video->id,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('show-toast', [
                'message' => 'Errore nel rimuovere il video',
                'type' => 'error'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Aggiorna lo stato del watch later per un video specifico
     */
    public function updateWatchLaterStatus($videoId)
    {
        if ($this->video->id == $videoId) {
            $this->checkWatchLaterStatus();
        }
    }

    /**
     * Update video when switching between reels
     */
    public function updateVideo($videoData)
    {
        if (is_array($videoData) && isset($videoData['id'])) {
            $this->video = Video::find($videoData['id']);
            if ($this->video) {
                $this->checkWatchLaterStatus();
                $this->dispatch('watchLaterVideoUpdated', [
                    'videoId' => $this->video->id,
                    'isInWatchLater' => $this->isInWatchLater
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.video-watch-later');
    }
}
