<div class="video-like-dislike flex items-center gap-3">
    @if ($video->likes_enabled == 1)
        <!-- Like Button -->
        <button wire:click="toggleLike" wire:loading.attr="disabled"
            class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group {{ $userReaction === 'like' ? 'bg-blue-600/20 border-blue-500/30' : 'border-gray-600/50 hover:border-gray-500' }} border backdrop-blur-sm cursor-pointer"
            title="{{ $userReaction === 'like' ? 'Rimuovi like' : 'Mi piace' }}">
            <div class="flex items-center gap-1.5">
                <i
                    class="fa-solid fa-thumbs-up text-lg transition-colors {{ $userReaction === 'like' ? 'text-blue-500' : 'text-gray-400 group-hover:text-white' }}"></i>

                <!-- Loading Spinner -->
                <div wire:loading wire:target="toggleLike" class="hidden">
                    <i class="fa-solid fa-spinner fa-spin text-gray-400"></i>
                </div>

                <!-- Count -->
                <span
                    class="font-medium text-sm transition-colors {{ $userReaction === 'like' ? 'text-blue-400' : 'text-gray-300 group-hover:text-white' }}">
                    {{ $this->formatCount($likesCount) }}
                </span>
            </div>
        </button>

        <!-- Divider -->
        <div class="w-px h-8 bg-gray-700/50"></div>

        <!-- Dislike Button -->
        <button wire:click="toggleDislike" wire:loading.attr="disabled"
            class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group {{ $userReaction === 'dislike' ? 'bg-red-600/20 border-red-500/30' : 'border-gray-600/50 hover:border-gray-500' }} border backdrop-blur-sm cursor-pointer"
            title="{{ $userReaction === 'dislike' ? 'Rimuovi dislike' : 'Non mi piace' }}">
            <div class="flex items-center gap-1.5">
                <i
                    class="fa-solid fa-thumbs-down text-lg transition-colors {{ $userReaction === 'dislike' ? 'text-red-500' : 'text-gray-400 group-hover:text-white' }}"></i>

                <!-- Loading Spinner -->
                <div wire:loading wire:target="toggleDislike" class="hidden">
                    <i class="fa-solid fa-spinner fa-spin text-gray-400"></i>
                </div>

                <!-- Count -->
                <span
                    class="font-medium text-sm transition-colors {{ $userReaction === 'dislike' ? 'text-red-400' : 'text-gray-300 group-hover:text-white' }}">
                    {{ $this->formatCount($dislikesCount) }}
                </span>
            </div>
        </button>

        <!-- Login Prompt for Guests -->
        @guest
            <div class="ml-4 flex items-center gap-2 text-sm text-gray-400">
                <i class="fa-solid fa-user-lock"></i>
                <a href="{{ route('login') }}" class="text-red-400 hover:text-blue-300 transition-colors">
                    Accedi per interagire
                </a>
            </div>
        @endguest

        <!-- Error Display -->
        @if (session()->has('error'))
            <div class="ml-4 px-3 py-1.5 bg-red-600/20 border border-red-500/30 rounded-lg">
                <p class="text-red-400 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <script>
            document.addEventListener('livewire:initialized', () => {
                @this.on('videoLiked', (data) => {
                    // Add success animation for like
                    const button = document.querySelector('[wire\\:click*="toggleLike"]');
                    if (button) {
                        button.classList.add('animate-pulse');
                        setTimeout(() => button.classList.remove('animate-pulse'), 600);
                    }
                });

                @this.on('videoDisliked', (data) => {
                    // Add success animation for dislike
                    const button = document.querySelector('[wire\\:click*="toggleDislike"]');
                    if (button) {
                        button.classList.add('animate-pulse');
                        setTimeout(() => button.classList.remove('animate-pulse'), 600);
                    }
                });
            });
        </script>
    @endif
</div>
