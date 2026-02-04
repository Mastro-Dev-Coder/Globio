<div class="flex flex-col h-full">
    <!-- Comments Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-700/50">
        <div class="flex items-center gap-3">
            <div
                class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                <i class="fas fa-comments text-white text-sm"></i>
            </div>
            <h3 class="text-white font-semibold text-lg">Commenti</h3>
            <span class="px-2 py-1 bg-gray-800/80 text-gray-300 text-xs rounded-full">
                {{ $comments->total() }}
            </span>
        </div>
    </div>

    @if ($this->commentsEnabled())
        <!-- Add Comment Form -->
        @auth
            <div class="p-6 border-b border-gray-700/50">
                <div class="flex gap-3">
                    @if (Auth::user()->userProfile?->avatar_url)
                        <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}"
                            alt="{{ Auth::user()->userProfile?->channel_name }}"
                            class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    @else
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-lg font-medium">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div class="flex-1">
                        <textarea wire:model="newComment" placeholder="Aggiungi un commento..." rows="2"
                            class="w-full bg-gray-800/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"></textarea>
                        @error('newComment')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <div class="flex justify-end gap-2 mt-3">
                            <button wire:click="$set('newComment', '')"
                                class="px-4 py-2 text-gray-400 hover:text-white transition-colors cursor-pointer">
                                Annulla
                            </button>
                            <button wire:click="addComment" wire:loading.attr="disabled"
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-all disabled:opacity-50 cursor-pointer">
                                <span wire:loading.remove wire:target="addComment">Commenta
                                    <i class="fas fa-paper-plane ml-1"></i>
                                </span>
                                <span wire:loading wire:target="addComment">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="p-6 border-b border-gray-700/50">
                <div class="text-center p-4 bg-gray-800/30 rounded-xl">
                    <p class="text-gray-400 mb-3">Accedi per lasciare un commento</p>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl transition-all duration-200">
                        <i class="fas fa-sign-in-alt text-sm"></i>
                        Accedi
                    </a>
                </div>
            </div>
        @endauth

        <!-- Comments List -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            @forelse($comments as $comment)
                <div id="comment-{{ $comment->id }}" class="comment-item" wire:key="comment-{{ $comment->id }}">
                    <div class="flex gap-3">
                        @if ($comment->user->userProfile?->avatar_url)
                            <img src="{{ asset('storage/' . $comment->user->userProfile->avatar_url) }}"
                                alt="{{ $comment->user->userProfile?->channel_name }}"
                                class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <!-- Comment Header -->
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-white font-medium text-sm">
                                    {{ $comment->user->userProfile?->channel_name ?: $comment->user->name }}
                                </span>
                                <span class="text-gray-400 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                                @if ($comment->created_at != $comment->updated_at)
                                    <span class="text-gray-400 text-xs">(modificato)</span>
                                @endif
                            </div>

                            <!-- Comment Content / Edit Form -->
                            @if ($editingComment === $comment->id)
                                <div class="mt-2">
                                    <textarea wire:model="editContent" rows="2"
                                        class="w-full bg-gray-800/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                    @error('editContent')
                                        <span class="text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button wire:click="cancelEdit"
                                            class="px-4 py-1.5 text-gray-400 hover:text-white text-sm">
                                            Annulla
                                        </button>
                                        <button wire:click="updateComment"
                                            class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-sm">
                                            Salva
                                        </button>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-200 whitespace-pre-wrap break-words">{{ $comment->content }}</p>
                            @endif

                            <!-- Comment Actions -->
                            <div class="flex items-center gap-4 mt-3">
                                <!-- Like Button -->
                                <button wire:click="toggleLike({{ $comment->id }})"
                                    class="flex items-center gap-1.5 text-sm transition-colors {{ ($userReactions[$comment->id] ?? null) === 'like' ? 'text-red-500' : 'text-gray-400 hover:text-red-400' }}">
                                    <i class="fa-solid fa-thumbs-up"></i>
                                    @if ($comment->likes_count > 0)
                                        <span>{{ $comment->likes_count }}</span>
                                    @endif
                                </button>

                                <!-- Dislike Button -->
                                <button wire:click="toggleDislike({{ $comment->id }})"
                                    class="flex items-center gap-1.5 text-sm transition-colors {{ ($userReactions[$comment->id] ?? null) === 'dislike' ? 'text-blue-500' : 'text-gray-400 hover:text-blue-400' }}">
                                    <i class="fa-solid fa-thumbs-down"></i>
                                    @if ($comment->dislikes_count > 0)
                                        <span>{{ $comment->dislikes_count }}</span>
                                    @endif
                                </button>

                                <!-- Reply Button -->
                                @auth
                                    <button wire:click="startReply({{ $comment->id }})"
                                        class="text-sm text-gray-400 hover:text-white transition-colors">
                                        Rispondi
                                    </button>
                                @endauth

                                <!-- Edit/Delete (Owner only) -->
                                @auth
                                    @if (auth()->id() === $comment->user_id)
                                        <button wire:click="startEdit({{ $comment->id }})"
                                            class="text-sm text-gray-400 hover:text-white transition-colors">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                    @endif
                                    @if (auth()->id() === $comment->user_id || auth()->id() === $video->user_id)
                                        <button wire:click="deleteComment({{ $comment->id }})"
                                            wire:confirm="Sei sicuro di voler eliminare questo commento?"
                                            class="text-sm text-gray-400 hover:text-red-500 transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                @endauth
                            </div>

                            <!-- Reply Form -->
                            @if ($replyingTo === $comment->id)
                                <div class="mt-4 flex gap-3">
                                    @if (Auth::user()->userProfile?->avatar_url)
                                        <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}"
                                            alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-medium">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <textarea wire:model="replyContent" placeholder="Aggiungi una risposta..." rows="2"
                                            class="w-full bg-gray-800/50 border border-gray-600/50 rounded-xl px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none text-sm"></textarea>
                                        @error('replyContent')
                                            <span class="text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                        <div class="flex justify-end gap-2 mt-2">
                                            <button wire:click="cancelReply"
                                                class="px-3 py-1.5 text-gray-400 hover:text-white text-sm">
                                                Annulla
                                            </button>
                                            <button wire:click="addReply"
                                                class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-sm">
                                                Rispondi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Replies -->
                            @if ($comment->replies->count() > 0)
                                <div class="mt-4 space-y-4 pl-4 border-l-2 border-gray-700/50">
                                    @foreach ($comment->replies as $reply)
                                        <div class="flex gap-3" wire:key="reply-{{ $reply->id }}">
                                            @if ($reply->user->userProfile?->avatar_url)
                                                <img src="{{ asset('storage/' . $reply->user->userProfile->avatar_url) }}"
                                                    alt="{{ $reply->user->name }}"
                                                    class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-xs font-medium">
                                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-white text-sm font-medium">
                                                        {{ $reply->user->userProfile?->channel_name ?: $reply->user->name }}
                                                    </span>
                                                    <span
                                                        class="text-gray-400 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>

                                                @if ($editingComment === $reply->id)
                                                    <div class="mt-2">
                                                        <textarea wire:model="editContent" rows="2"
                                                            class="w-full bg-gray-800/50 border border-gray-600/50 rounded-xl px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none text-sm"></textarea>
                                                        <div class="flex justify-end gap-2 mt-2">
                                                            <button wire:click="cancelEdit"
                                                                class="px-3 py-1 text-gray-400 hover:text-white text-xs">
                                                                Annulla
                                                            </button>
                                                            <button wire:click="updateComment"
                                                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs">
                                                                Salva
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-gray-200 text-sm whitespace-pre-wrap break-words">
                                                        {{ $reply->content }}</p>
                                                @endif

                                                <!-- Reply Actions -->
                                                <div class="flex items-center gap-3 mt-2">
                                                    <button wire:click="toggleLike({{ $reply->id }})"
                                                        class="flex items-center gap-1 text-xs transition-colors {{ ($userReactions[$reply->id] ?? null) === 'like' ? 'text-red-500' : 'text-gray-400 hover:text-red-400' }}">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                        @if ($reply->likes_count > 0)
                                                            <span>{{ $reply->likes_count }}</span>
                                                        @endif
                                                    </button>
                                                    <button wire:click="toggleDislike({{ $reply->id }})"
                                                        class="flex items-center gap-1 text-xs transition-colors {{ ($userReactions[$reply->id] ?? null) === 'dislike' ? 'text-blue-500' : 'text-gray-400 hover:text-blue-400' }}">
                                                        <i class="fa-solid fa-thumbs-down"></i>
                                                        @if ($reply->dislikes_count > 0)
                                                            <span>{{ $reply->dislikes_count }}</span>
                                                        @endif
                                                    </button>
                                                    @auth
                                                        @if (auth()->id() === $reply->user_id)
                                                            <button wire:click="startEdit({{ $reply->id }})"
                                                                class="text-xs text-gray-400 hover:text-white">
                                                                <i class="fa-solid fa-pen"></i>
                                                            </button>
                                                        @endif
                                                        @if (auth()->id() === $reply->user_id || auth()->id() === $video->user_id)
                                                            <button wire:click="deleteComment({{ $reply->id }})"
                                                                wire:confirm="Sei sicuro di voler eliminare questa risposta?"
                                                                class="text-xs text-gray-400 hover:text-red-500">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comment text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-400 font-medium">Nessun commento ancora</p>
                    <p class="text-gray-500 text-sm mt-1">Sii il primo a commentare!</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="flex-1 flex items-center justify-center p-6">
            <div class="text-center">
                <i class="fas fa-comment-slash text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-400">I commenti sono disabilitati per questo video.</p>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if ($comments->hasPages())
        <div class="p-6 border-t border-gray-700/50">
            {{ $comments->links() }}
        </div>
    @endif

    <!-- Error Display -->
    @if (session()->has('error'))
        <div class="mx-6 mb-4 px-4 py-2 bg-red-600/20 border border-red-500/30 rounded-lg">
            <p class="text-red-400 text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mx-6 mb-4 px-4 py-2 bg-green-600/20 border border-green-500/30 rounded-lg">
            <p class="text-green-400 text-sm">{{ session('success') }}</p>
        </div>
    @endif
</div>
