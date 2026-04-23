<div class="video-like-dislike-compact flex items-center gap-1">
    @auth
        <!-- Like Button -->
        <button 
            wire:click="toggleLike" 
            wire:loading.attr="disabled"
            class="flex items-center gap-1 px-2 py-1 hover:bg-gray-700/50 rounded transition-all duration-200 {{ $userReaction === 'like' ? 'text-blue-400' : 'text-gray-400 hover:text-white' }}"
            title="{{ $userReaction === 'like' ? 'Rimuovi like' : 'Mi piace' }}"
        >
            <i class="fa-solid fa-thumbs-up text-xs"></i>
            <span class="text-xs font-medium">{{ $this->formatCount($likesCount) }}</span>
            
            <div wire:loading wire:target="toggleLike" class="hidden">
                <i class="fa-solid fa-spinner fa-spin text-xs"></i>
            </div>
        </button>

        <!-- Dislike Button -->
        <button 
            wire:click="toggleDislike" 
            wire:loading.attr="disabled"
            class="flex items-center gap-1 px-2 py-1 hover:bg-gray-700/50 rounded transition-all duration-200 {{ $userReaction === 'dislike' ? 'text-red-400' : 'text-gray-400 hover:text-white' }}"
            title="{{ $userReaction === 'dislike' ? 'Rimuovi dislike' : 'Non mi piace' }}"
        >
            <i class="fa-solid fa-thumbs-down text-xs"></i>
            <span class="text-xs font-medium">{{ $this->formatCount($dislikesCount) }}</span>
            
            <div wire:loading wire:target="toggleDislike" class="hidden">
                <i class="fa-solid fa-spinner fa-spin text-xs"></i>
            </div>
        </button>
    @else
        <!-- Display only counts for guests -->
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <div class="flex items-center gap-1">
                <i class="fa-solid fa-thumbs-up text-xs"></i>
                <span>{{ $this->formatCount($likesCount) }}</span>
            </div>
            @if($dislikesCount > 0)
                <div class="flex items-center gap-1">
                    <i class="fa-solid fa-thumbs-down text-xs"></i>
                    <span>{{ $this->formatCount($dislikesCount) }}</span>
                </div>
            @endif
        </div>
    @endauth
</div>