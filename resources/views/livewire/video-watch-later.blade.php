<div>
    @auth
        @if ($compact)
            {{-- Modalità compatta per componenti video e reel --}}
            <button wire:click="toggleWatchLater" wire:loading.attr="disabled"
                            class="watch-later-button w-9 h-9 bg-gray-900/70 text-white rounded-xl flex items-center justify-center hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl cursor-pointer disabled:opacity-50 {{ $isInWatchLater ? 'saved' : '' }}"
                            title="{{ $isInWatchLater ? 'Rimuovi da Guarda più tardi' : 'Aggiungi a Guarda più tardi' }}"
                            id="watch-later-btn-{{ $video->id }}">
            
                            <div wire:loading.remove wire:target="toggleWatchLater">
                                <i class="fas watch-later-icon {{ $isInWatchLater ? 'fa-check-double' : 'fa-clock' }} text-sm"
                                    style="color: {{ $isInWatchLater ? '#22c55e' : 'inherit' }}"></i>
                            </div>
            
                            <div wire:loading wire:target="toggleWatchLater" class="w-4 h-4 flex items-center justify-center">
                                <div class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </button>
        @else
            {{-- Modalità normale per le pagine video --}}
            <button wire:click="toggleWatchLater" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group border border-gray-600/50 hover:border-gray-500 backdrop-blur-sm cursor-pointer disabled:opacity-50"
                title="{{ $isInWatchLater ? 'Rimuovi da Guarda più tardi' : 'Salva per guardarlo più tardi' }}">

                <div wire:loading.remove wire:target="toggleWatchLater">
                    <i class="fas {{ $isInWatchLater ? 'fa-check-double' : 'fa-clock' }} text-gray-300 group-hover:text-white transition-colors duration-200"
                        style="color: {{ $isInWatchLater ? '#ef4444' : 'inherit' }}"></i>
                </div>

                <div wire:loading wire:target="toggleWatchLater" class="w-5 h-5 flex items-center justify-center">
                    <div class="w-4 h-4 border-2 border-gray-400 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </button>
        @endif
    @else
        @if ($compact)
            {{-- Modalità compatta per utenti non autenticati --}}
            <button onclick="showToast('Devi effettuare l\'accesso per salvare i video.', 'error')"
                class="w-9 h-9 bg-gray-900/70 text-white rounded-xl flex items-center justify-center hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg backdrop-blur-sm border border-white/20 hover:scale-105 hover:shadow-xl cursor-pointer">
                <i class="fas fa-clock text-sm"></i>
            </button>
        @else
            {{-- Modalità normale per utenti non autenticati --}}
            <button onclick="showToast('Devi effettuare l\'accesso per salvare i video.', 'error')"
                class="flex items-center gap-2 px-4 py-2.5 bg-gray-800/50 hover:bg-gray-700/50 rounded-full transition-all duration-200 group border border-gray-600/50 hover:border-gray-500 backdrop-blur-sm cursor-pointer">
                <i class="fas fa-clock text-gray-300 group-hover:text-white"></i>
            </button>
        @endif
    @endauth
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-toast', (event) => {
            if (window.showToast) {
                window.showToast(event.message, event.type);
            } else {
                // Fallback se showToast non è definita
                console.log(`Toast: ${event.type} - ${event.message}`);
                alert(event.message);
            }
        });

        // Listener per gli aggiornamenti dello stato watch later
        Livewire.on('watchLaterStatusChanged', (event) => {
            // Aggiorna lo stato del componente se il video ID corrisponde
            // Usiamo un selector più generico per trovare il componente
            const videoId = @this.video.id;
            if (event.videoId === videoId) {
                @this.isInWatchLater = event.isInWatchLater;
            }
        });
    });
</script>
