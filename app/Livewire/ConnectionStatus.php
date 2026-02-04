<?php

namespace App\Livewire;

use Livewire\Component;

class ConnectionStatus extends Component
{
    public string $status = 'online';
    public bool $showOfflinePage = false;
    public bool $showSlowPage = false;

    protected $listeners = ['connectionStatusChanged'];

    public function connectionStatusChanged(string $status): void
    {
        $this->status = $status;
        $this->showOfflinePage = $status === 'offline';
        $this->showSlowPage = $status === 'slow';
    }

    public function render()
    {
        return view('livewire.connection-status');
    }
}
