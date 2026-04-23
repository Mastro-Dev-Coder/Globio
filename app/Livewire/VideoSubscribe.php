<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\User;
use App\Models\Subscription;
use App\Notifications\NewSubscriberNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VideoSubscribe extends Component
{
    public Video $video;
    public User $channelOwner;
    public bool $isSubscribed = false;
    public bool $isOwner = false;

    public function mount(Video $video)
    {
        $this->video = $video;
        $this->channelOwner = $video->user;
        $this->isOwner = Auth::check() && Auth::id() === $this->channelOwner->id;
        
        if (Auth::check() && !$this->isOwner) {
            $this->isSubscribed = Subscription::where('subscriber_id', Auth::id())
                ->where('channel_id', $this->channelOwner->id)
                ->exists();
        }
    }

    public function toggleSubscription()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->isOwner) {
            return;
        }

        if ($this->isSubscribed) {
            // Annulla iscrizione
            Subscription::where('subscriber_id', Auth::id())
                ->where('channel_id', $this->channelOwner->id)
                ->delete();
            $this->isSubscribed = false;
            
            Log::info('Subscription cancelled', [
                'subscriber_id' => Auth::id(),
                'channel_owner_id' => $this->channelOwner->id
            ]);
        } else {
            // Iscriviti
            $subscription = Subscription::create([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->channelOwner->id,
            ]);
            $this->isSubscribed = true;
            
            // Invia notifica al proprietario del canale
            $this->channelOwner->notify(new NewSubscriberNotification(Auth::user()));
            
            Log::info('Notifica NewSubscriberNotification inviata', [
                'subscriber_id' => Auth::id(),
                'channel_owner_id' => $this->channelOwner->id,
                'subscription_id' => $subscription->id
            ]);
        }
    }

    public function render()
    {
        return view('livewire.video-subscribe');
    }
}