<div class="video-subscribe flex items-center gap-3">
    @guest
        <!-- Guest User -->
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <i class="fas fa-user-lock"></i>
            <a href="{{ route('login') }}" class="text-red-400 hover:text-red-300 transition-colors">
                Accedi per iscriverti
            </a>
        </div>
    @else
        @if ($isOwner)
            <!-- Owner Actions -->
            <div class="flex items-center gap-2">
                <a href="{{ route('videos.edit', $video) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group border border-gray-600/50 hover:border-gray-500">
                    <i class="fas fa-edit text-gray-400 group-hover:text-white transition-colors"></i>
                    <span class="text-gray-300 group-hover:text-white text-sm font-medium">Modifica</span>
                </a>

                <a href="{{ route('channel.edit', $video->user->userProfile?->channel_name) }}?tab=analytics"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group border border-gray-600/50 hover:border-gray-500">
                    <i class="fas fa-chart-bar text-gray-400 group-hover:text-white transition-colors"></i>
                    <span class="text-gray-300 group-hover:text-white text-sm font-medium">Analitica</span>
                </a>
            </div>
        @else
            <!-- Subscribe Button -->
            <button wire:click="toggleSubscription" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-6 py-2.5 rounded-full font-medium transition-all duration-200 group
                    {{ $isSubscribed
                        ? 'bg-gray-700/50 hover:bg-gray-600/50 border border-gray-600/50 hover:border-gray-500'
                        : 'bg-red-600 hover:bg-red-700 border border-red-500 hover:border-red-400' }} backdrop-blur-sm">

                <div class="flex items-center gap-1.5">
                    <i
                        class="fa-solid {{ $isSubscribed ? 'fa-check' : 'fa-bell' }} 
                        {{ $isSubscribed ? 'text-gray-300' : 'text-white' }} transition-colors"></i>

                    <!-- Loading Spinner -->
                    <div wire:loading wire:target="toggleSubscription" class="hidden">
                        <i class="fa-solid fa-spinner fa-spin text-white"></i>
                    </div>

                    <!-- Text -->
                    <span
                        class="text-sm font-medium transition-colors
                        {{ $isSubscribed ? 'text-gray-300' : 'text-white' }}">
                        {{ $isSubscribed ? 'Iscritto' : 'Iscriviti' }}
                    </span>
                </div>
            </button>
        @endif
    @endguest

    <!-- Success Animation (Optional) -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('subscribed', (data) => {
                // Add success animation for subscription
                const button = document.querySelector('[wire\\:click*="toggleSubscription"]');
                if (button) {
                    button.classList.add('animate-pulse');
                    setTimeout(() => button.classList.remove('animate-pulse'), 600);
                }
            });

            @this.on('unsubscribed', (data) => {
                // Add success animation for unsubscription
                const button = document.querySelector('[wire\\:click*="toggleSubscription"]');
                if (button) {
                    button.classList.add('animate-pulse');
                    setTimeout(() => button.classList.remove('animate-pulse'), 600);
                }
            });
        });
    </script>
</div>
