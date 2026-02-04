<div class="flex flex-col items-center space-y-3">
    @auth
        @if ($this->likesEnabled())
            <!-- Like Button -->
            <div class="flex flex-col items-center group">
                <button wire:click="toggleLike" wire:loading.attr="disabled"
                    class="w-14 h-14 bg-black/40 backdrop-blur-md rounded-2xl flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10 hover:border-white/20 shadow-lg cursor-pointer {{ $userReaction === 'like' ? 'bg-blue-600/20 border-blue-500/50' : '' }}"
                    title="{{ $userReaction === 'like' ? 'Rimuovi like' : 'Mi piace' }}">
                    <div class="relative">
                        <i
                            class="fa-solid fa-thumbs-up text-2xl transition-colors {{ $userReaction === 'like' ? 'text-blue-500' : 'text-gray-400 group-hover:text-blue-400' }}"></i>

                        <!-- Loading Spinner -->
                        <div wire:loading wire:target="toggleLike" class="absolute -top-1 -right-1">
                            <i class="fa-solid fa-spinner fa-spin text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </button>

                <span class="text-gray-300 text-sm font-medium mt-3 text-center">
                    {{ $this->formatCount($likesCount) }}
                </span>
            </div>

            <!-- Dislike Button -->
            <div class="flex flex-col items-center group">
                <button wire:click="toggleDislike" wire:loading.attr="disabled"
                    class="w-14 h-14 bg-black/40 backdrop-blur-md rounded-2xl flex items-center justify-center hover:bg-black/60 hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10 hover:border-white/20 shadow-lg cursor-pointer {{ $userReaction === 'dislike' ? 'bg-red-600/20 border-red-500/50' : '' }}"
                    title="{{ $userReaction === 'dislike' ? 'Rimuovi dislike' : 'Non mi piace' }}">
                    <div class="relative">
                        <i
                            class="fa-solid fa-thumbs-down text-2xl transition-colors {{ $userReaction === 'dislike' ? 'text-red-500' : 'text-gray-400 group-hover:text-red-400' }}"></i>

                        <!-- Loading Spinner -->
                        <div wire:loading wire:target="toggleDislike" class="absolute -top-1 -right-1">
                            <i class="fa-solid fa-spinner fa-spin text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </button>

                <span class="text-gray-300 text-sm font-medium mt-3 text-center">
                    {{ $this->formatCount($dislikesCount) }}
                </span>
            </div>
        @else
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-gray-800/50 rounded-2xl flex items-center justify-center mb-3">
                    <i class="fa-solid fa-lock text-gray-400 text-2xl"></i>
                </div>
                <span class="text-gray-400 text-sm">
                    Like disabilitati
                </span>
            </div>
        @endif
    @else
        <!-- Login Prompt for Guests -->
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-gray-800/50 rounded-2xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-user-lock text-gray-400 text-2xl"></i>
            </div>
            <a href="{{ route('login') }}" class="text-red-400 hover:text-red-300 transition-colors text-sm font-medium">
                Accedi per interagire
            </a>
        </div>
    @endauth

    <!-- Error Display -->
    @if (session()->has('error'))
        <div class="px-3 py-1.5 bg-red-600/20 border border-red-500/30 rounded-lg">
            <p class="text-red-400 text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('videoLiked', (data) => {
                console.log('Video liked:', data);
                // Add success animation for like
                const button = document.querySelector('[wire\\:click*="toggleLike"]');
                if (button) {
                    button.classList.add('animate-pulse');
                    setTimeout(() => button.classList.remove('animate-pulse'), 600);
                    
                    // Show success toast
                    if (window.simpleReelManager) {
                        window.simpleReelManager.showToast('Mi piace aggiunto!', 'success');
                    }
                }
            });

            @this.on('videoDisliked', (data) => {
                console.log('Video disliked:', data);
                // Add success animation for dislike
                const button = document.querySelector('[wire\\:click*="toggleDislike"]');
                if (button) {
                    button.classList.add('animate-pulse');
                    setTimeout(() => button.classList.remove('animate-pulse'), 600);
                    
                    // Show success toast
                    if (window.simpleReelManager) {
                        window.simpleReelManager.showToast('Non mi piace registrato!', 'info');
                    }
                }
            });

            @this.on('videoUpdated', (data) => {
                console.log('Video updated for like/dislike:', data);
                // Update counts in sidebar if needed
                const sidebarLikes = document.getElementById('sidebar-likes');
                if (sidebarLikes && data.likesCount !== undefined) {
                    sidebarLikes.textContent = data.likesCount.toLocaleString();
                }
            });
        });
    </script>
</div>
