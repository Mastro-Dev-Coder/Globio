<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportModal extends Component
{
    public $showModal = false;
    public $targetType = 'video';
    public $targetId = null;
    public $targetTitle = '';

    public $reportType = 'spam';
    public $customReason = '';
    public $description = '';

    protected $listeners = [
        'openReportModal' => 'openModal',
        'closeReportModal' => 'closeModal',
        'reportSubmitted' => 'handleReportSubmitted',
    ];

    protected $reasons = [
        'video' => [
            ['value' => 'spam', 'label' => 'Spam', 'icon' => 'fa-trash'],
            ['value' => 'harassment', 'label' => 'Molestie/Bullismo', 'icon' => 'fa-bullhorn'],
            ['value' => 'inappropriate_content', 'label' => 'Contenuto inappropriato', 'icon' => 'fa-exclamation-triangle'],
            ['value' => 'copyright', 'label' => 'Violazione copyright', 'icon' => 'fa-copyright'],
            ['value' => 'fake_information', 'label' => 'Informazioni false', 'icon' => 'fa-times-circle'],
            ['value' => 'misleading_title', 'label' => 'Titolo fuorviante', 'icon' => 'fa-tag'],
            ['value' => 'violence', 'label' => 'Violenza', 'icon' => 'fa-exclamation-circle'],
        ],
        'channel' => [
            ['value' => 'spam', 'label' => 'Spam', 'icon' => 'fa-trash'],
            ['value' => 'harassment', 'label' => 'Molestie/Bullismo', 'icon' => 'fa-bullhorn'],
            ['value' => 'inappropriate_content', 'label' => 'Contenuto inappropriato', 'icon' => 'fa-exclamation-triangle'],
            ['value' => 'impersonation', 'label' => 'Impersonificazione', 'icon' => 'fa-user-secret'],
            ['value' => 'misleading_bio', 'label' => 'Bio fuorviante', 'icon' => 'fa-info-circle'],
            ['value' => 'scam', 'label' => 'Truffa/Frode', 'icon' => 'fa-exclamation-triangle'],
        ],
        'comment' => [
            ['value' => 'spam', 'label' => 'Spam', 'icon' => 'fa-trash'],
            ['value' => 'harassment', 'label' => 'Molestie/Bullismo', 'icon' => 'fa-bullhorn'],
            ['value' => 'inappropriate_content', 'label' => 'Contenuto inappropriato', 'icon' => 'fa-exclamation-triangle'],
            ['value' => 'hate_speech', 'label' => 'Discorso d\'odio', 'icon' => 'fa-frown'],
        ],
    ];

    public function mount()
    {
        //
    }

    public function openModal($type, $id, $title = '')
    {
        $this->targetType = $type;
        $this->targetId = $id;
        $this->targetTitle = $title;
        $this->reportType = 'spam';
        $this->customReason = '';
        $this->description = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['reportType', 'customReason', 'description']);
    }

    public function getReasonsProperty()
    {
        return $this->reasons[$this->targetType] ?? $this->reasons['video'];
    }

    public function handleReportSubmitted($data)
    {
        if (isset($data['success']) && $data['success']) {
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => $data['message'] ?? 'Segnalazione inviata con successo!',
            ]);
        } else {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $data['message'] ?? 'Errore durante l\'invio della segnalazione.',
            ]);
        }
    }

    public function submitReport()
    {
        if (!Auth::check()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Devi essere autenticato per segnalare.',
            ]);
            return;
        }

        $validReasons = [
            // Video reasons
            'spam',
            'harassment',
            'inappropriate_content',
            'copyright',
            'fake_information',
            'misleading_title',
            'violence',
            // Channel reasons
            'impersonation',
            'misleading_bio',
            'scam',
            // Comment reasons
            'hate_speech',
            // Custom
            'custom'
        ];

        $this->validate([
            'reportType' => 'required|in:' . implode(',', $validReasons),
            'customReason' => 'required_if:reportType,custom|max:255',
            'description' => 'nullable|max:1000',
        ]);

        $reason = $this->reportType === 'custom' ? $this->customReason : $this->reportType;

        try {
            $report = match ($this->targetType) {
                'video' => Report::reportVideo(
                    Auth::id(),
                    $this->targetId,
                    $this->reportType,
                    $reason,
                    $this->description
                ),
                'channel' => Report::reportChannel(
                    Auth::id(),
                    $this->targetId,
                    $this->reportType,
                    $reason,
                    $this->description
                ),
                'comment' => Report::reportComment(
                    Auth::id(),
                    $this->targetId,
                    $this->reportType,
                    $reason,
                    $this->description
                ),
                default => null,
            };

            if ($report) {
                $this->dispatch('reportSubmitted', [
                    'success' => true,
                    'message' => 'Segnalazione inviata con successo!',
                ]);
                $this->closeModal();
                return;
            }

            $this->dispatch('reportSubmitted', [
                'success' => false,
                'message' => 'Errore durante l\'invio della segnalazione.',
            ]);
        } catch (\Exception $e) {
            Log::error('Report error: ' . $e->getMessage());
            $this->dispatch('reportSubmitted', [
                'success' => false,
                'message' => 'Errore durante l\'invio della segnalazione.',
            ]);
        }
    }

    public function render()
    {
        $targetReasons = $this->reasons[$this->targetType] ?? $this->reasons['video'];
        return view('livewire.report-modal', [
            'targetReasons' => $targetReasons,
        ]);
    }
}
