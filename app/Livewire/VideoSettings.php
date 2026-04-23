<?php

namespace App\Livewire;

use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class VideoSettings extends Component
{
    public Video $video;
    public bool $commentsEnabled;
    public bool $likesEnabled;
    public bool $commentsRequireApproval;
    public bool $isLoading = false;
    public ?string $successMessage = null;

    protected $rules = [
        'commentsEnabled' => 'boolean',
        'likesEnabled' => 'boolean',
        'commentsRequireApproval' => 'boolean',
    ];

    public function mount(Video $video)
    {
        $this->video = $video;
        $this->loadSettings();
    }

    /**
     * Carica le impostazioni attuali del video
     */
    public function loadSettings()
    {
        $this->commentsEnabled = $this->video->comments_enabled ?? true;
        $this->likesEnabled = $this->video->likes_enabled ?? true;
        $this->commentsRequireApproval = $this->video->comments_require_approval ?? false;
    }

    /**
     * Aggiorna le impostazioni del video
     */
    public function updateSettings()
    {
        if (!Auth::check() || Auth::id() !== $this->video->user_id) {
            session()->flash('error', 'Non autorizzato a modificare le impostazioni di questo video');
            return;
        }

        $this->isLoading = true;

        try {
            $commentsRequireApproval = $this->commentsEnabled ? $this->commentsRequireApproval : false;

            $this->video->update([
                'comments_enabled' => $this->commentsEnabled,
                'likes_enabled' => $this->likesEnabled,
                'comments_require_approval' => $commentsRequireApproval,
            ]);

            $this->successMessage = 'Impostazioni del video aggiornate con successo';

            Log::info('Video settings updated', [
                'video_id' => $this->video->id,
                'user_id' => Auth::id(),
                'comments_enabled' => $this->commentsEnabled,
                'likes_enabled' => $this->likesEnabled,
                'comments_require_approval' => $commentsRequireApproval,
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating video settings', [
                'video_id' => $this->video->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            session()->flash('error', 'Errore durante l\'aggiornamento delle impostazioni');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Reset alle impostazioni predefinite
     */
    public function resetToDefaults()
    {
        if (!Auth::check() || Auth::id() !== $this->video->user_id) {
            session()->flash('error', 'Non autorizzato a modificare le impostazioni di questo video');
            return;
        }

        $this->commentsEnabled = true;
        $this->likesEnabled = true;
        $this->commentsRequireApproval = false;

        $this->updateSettings();
    }

    /**
     * Disabilita tutti i commenti e like
     */
    public function disableAllInteractions()
    {
        if (!Auth::check() || Auth::id() !== $this->video->user_id) {
            session()->flash('error', 'Non autorizzato a modificare le impostazioni di questo video');
            return;
        }

        $this->commentsEnabled = false;
        $this->likesEnabled = false;
        $this->commentsRequireApproval = false;

        $this->updateSettings();
    }

    /**
     * Abilita solo commenti con approvazione
     */
    public function enableCommentsWithApproval()
    {
        if (!Auth::check() || Auth::id() !== $this->video->user_id) {
            session()->flash('error', 'Non autorizzato a modificare le impostazioni di questo video');
            return;
        }

        $this->commentsEnabled = true;
        $this->likesEnabled = true;
        $this->commentsRequireApproval = true;

        $this->updateSettings();
    }

    /**
     * Abilita tutto senza approvazione
     */
    public function enableAllWithoutApproval()
    {
        if (!Auth::check() || Auth::id() !== $this->video->user_id) {
            session()->flash('error', 'Non autorizzato a modificare le impostazioni di questo video');
            return;
        }

        $this->commentsEnabled = true;
        $this->likesEnabled = true;
        $this->commentsRequireApproval = false;

        $this->updateSettings();
    }

    /**
     * Nasconde il messaggio di successo
     */
    public function hideSuccessMessage()
    {
        $this->successMessage = null;
    }

    public function render()
    {
        $canEdit = Auth::check() && Auth::id() === $this->video->user_id;

        return view('livewire.video-settings', [
            'canEdit' => $canEdit,
        ]);
    }
}