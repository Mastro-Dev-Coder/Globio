<div class="comments-section">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-white">
            {{ $comments->total() }} {{ __('ui.comments') }}
        </h3>
    </div>

    @if ($this->commentsEnabled())
        <!-- Add Comment Form -->
        @auth
            <div class="mb-8">
                <div class="flex gap-4">
                    @if (Auth::user()->userProfile->avatar_url)
                        <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}"
                            alt="{{ Auth::user()->userProfile->channel_name }}"
                            class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    @else
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-lg font-medium">
                                {{ strtoupper(substr(Auth::user()->userProfile->channel_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div class="flex-1">
                        <textarea wire:model="newComment" placeholder="{{ __('ui.add_comment') }}" rows="2"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none transition-all"></textarea>
                        @error('newComment')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <div class="flex justify-end gap-2 mt-3">
                            <button wire:click="$set('newComment', '')"
                                class="px-4 py-2 text-white/70 hover:text-white transition-colors cursor-pointer">
                                {{ __('ui.cancel') }}
                            </button>
                            <button wire:click="addComment" wire:loading.attr="disabled"
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-full font-medium transition-all disabled:opacity-50 cursor-pointer">
                                <span wire:loading.remove wire:target="addComment">{{ __('ui.comment') }}
                                    <i class="fas fa-paper-plane"></i>
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
            <div class="mb-8 p-4 bg-white/5 rounded-xl border border-white/10 text-center">
                <p class="text-white/70">
                    <a href="{{ route('login') }}" class="text-red-500 hover:text-red-400">{{ __('ui.login') }}</a>
                    {{ __('ui.sign_in_to_comment') }}
                    {{ __('ui.comment') }}
                </p>
            </div>
        @endauth

        <!-- Comments List -->
        <div class="space-y-6">
            @forelse($comments as $comment)
                <div id="comment-{{ $comment->id }}" class="comment-item" wire:key="comment-{{ $comment->id }}">
                    <div class="flex gap-4">
                        @if ($comment->user->userProfile->avatar_url)
                            <img src="{{ asset('storage/' . $comment->user->userProfile->avatar_url) }}"
                                alt="{{ $comment->user->userProfile->channel_name }}"
                                class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-lg font-medium">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <!-- Comment Header -->
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('channel.show', $comment->user->userProfile->channel_name) }}"
                                    class="font-medium text-white hover:text-red-400 transition-colors">
                                    {{ $comment->user->name }}
                                </a>
                                <span class="text-white/50 text-sm">{{ $comment->created_at->diffForHumans() }}</span>
                                @if ($comment->created_at != $comment->updated_at)
                                    <span class="text-white/40 text-xs">({{ __('ui.edited') }})</span>
                                @endif
                            </div>

                            <!-- Comment Content / Edit Form -->
                            @if ($editingComment === $comment->id)
                                <div class="mt-2">
                                    <textarea wire:model="editContent" rows="2"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-500 resize-none"></textarea>
                                    @error('editContent')
                                        <span class="text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button wire:click="cancelEdit"
                                            class="px-4 py-1.5 text-white/70 hover:text-white text-sm">
                                            {{ __('ui.cancel') }}
                                        </button>
                                        <button wire:click="updateComment"
                                            class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-sm">
                                            {{ __('ui.save') }}
                                        </button>
                                    </div>
                                </div>
                            @else
                                <p class="text-white/90 whitespace-pre-wrap break-words">{{ $comment->content }}</p>
                            @endif

                            <!-- Comment Actions -->
                            <div class="flex items-center gap-4 mt-3">
                                <!-- Like Button -->
                                <button wire:click="toggleLike({{ $comment->id }})"
                                    class="flex items-center gap-1.5 text-sm transition-colors {{ ($userReactions[$comment->id] ?? null) === 'like' ? 'text-blue-500' : 'text-white/60 hover:text-white' }}">
                                    <i class="fa-solid fa-thumbs-up"></i>
                                    <span>{{ $comment->likes_count > 0 ? $comment->likes_count : '' }}</span>
                                </button>

                                <!-- Dislike Button -->
                                <button wire:click="toggleDislike({{ $comment->id }})"
                                    class="flex items-center gap-1.5 text-sm transition-colors {{ ($userReactions[$comment->id] ?? null) === 'dislike' ? 'text-red-500' : 'text-white/60 hover:text-white' }}">
                                    <i class="fa-solid fa-thumbs-down"></i>
                                    <span>{{ $comment->dislikes_count > 0 ? $comment->dislikes_count : '' }}</span>
                                </button>

                                <!-- Reply Button -->
                                @auth
                                    <button wire:click="startReply({{ $comment->id }})"
                                        class="text-sm text-white/60 hover:text-white transition-colors">
                                        {{ __('ui.reply') }}
                                    </button>
                                @endauth

                                <!-- Edit/Delete (Owner only) -->
                                @auth
                                    @if (auth()->id() === $comment->user_id)
                                        <button wire:click="startEdit({{ $comment->id }})"
                                            class="text-sm text-white/60 hover:text-white transition-colors">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                    @endif
                                    @if (auth()->id() === $comment->user_id || auth()->id() === $video->user_id)
                                        <button wire:click="deleteComment({{ $comment->id }})"
                                            wire:confirm="Sei sicuro di voler eliminare questo commento?"
                                            class="text-sm text-white/60 hover:text-red-500 transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                    @if (auth()->id() !== $comment->user_id)
                                        <button
                                            onclick="openReportModal('comment', {{ $comment->id }}, '{{ str_replace(["'", '"'], '', Str::limit($comment->content, 50)) }}')"
                                            class="text-sm text-white/60 hover:text-yellow-500 transition-colors"
                                            title="Segnala questo commento">
                                            <i class="fas fa-flag"></i>
                                        </button>
                                    @endif
                                @endauth
                            </div>

                            <!-- Reply Form -->
                            @if ($replyingTo === $comment->id)
                                <div class="mt-4 flex gap-3">
                                    @if (Auth::user()->userProfile->avatar_url)
                                        <img src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}"
                                            alt="{{ Auth::user()->userProfile->channel_name }}"
                                            class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    @else
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-lg font-medium">
                                                {{ strtoupper(substr(Auth::user()->userProfile->channel_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <textarea wire:model="replyContent" placeholder="Aggiungi una risposta..." rows="2"
                                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/50 focus:outline-none focus:border-red-500 resize-none text-sm"></textarea>
                                        @error('replyContent')
                                            <span class="text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                        <div class="flex justify-end gap-2 mt-2">
                                            <button wire:click="cancelReply"
                                                class="px-3 py-1.5 text-white/70 hover:text-white text-sm">
                                                {{ __('ui.cancel') }}
                                            </button>
                                            <button wire:click="addReply"
                                                class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-sm">
                                                {{ __('ui.reply') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Replies -->
                            @if ($comment->replies->count() > 0)
                                <div class="mt-4 space-y-4 pl-4 border-l-2 border-white/10">
                                    @foreach ($comment->replies as $reply)
                                        <div class="flex gap-3" wire:key="reply-{{ $reply->id }}">
                                            @if ($comment->user->userProfile->avatar_url)
                                                <img src="{{ asset('storage/' . $comment->user->userProfile->avatar_url) }}"
                                                    alt="{{ $comment->user->userProfile->channel_name }}"
                                                    class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                            @else
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-lg font-medium">
                                                        {{ strtoupper(substr($comment->user->userProfile->channel_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <a href="{{ route('channel.show', $reply->user->userProfile->channel_name) }}"
                                                        class="font-medium text-white text-sm hover:text-red-400">
                                                        {{ $reply->user->name }}
                                                    </a>
                                                    <span
                                                        class="text-white/50 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>

                                                @if ($editingComment === $reply->id)
                                                    <div class="mt-2">
                                                        <textarea wire:model="editContent" rows="2"
                                                            class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-500 resize-none"></textarea>
                                                        <div class="flex justify-end gap-2 mt-2">
                                                            <button wire:click="cancelEdit"
                                                                class="px-3 py-1 text-white/70 hover:text-white text-xs">
                                                                {{ __('ui.cancel') }}
                                                            </button>
                                                            <button wire:click="updateComment"
                                                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs">
                                                                {{ __('ui.save') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-white/90 text-sm">
                                                        {{ $reply->content }}</p>
                                                @endif

                                                <!-- Reply Actions -->
                                                <div class="flex items-center gap-3 mt-2">
                                                    <button wire:click="toggleLike({{ $reply->id }})"
                                                        class="flex items-center gap-1 text-xs transition-colors {{ ($userReactions[$reply->id] ?? null) === 'like' ? 'text-blue-500' : 'text-white/60 hover:text-white' }}">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                        <span>{{ $reply->likes_count > 0 ? $reply->likes_count : '' }}</span>
                                                    </button>
                                                    <button wire:click="toggleDislike({{ $reply->id }})"
                                                        class="flex items-center gap-1 text-xs transition-colors {{ ($userReactions[$reply->id] ?? null) === 'dislike' ? 'text-red-500' : 'text-white/60 hover:text-white' }}">
                                                        <i class="fa-solid fa-thumbs-down"></i>
                                                        <span>{{ $reply->dislikes_count > 0 ? $reply->dislikes_count : '' }}</span>
                                                    </button>
                                                    @auth
                                                        @if (auth()->id() === $reply->user_id)
                                                            <button wire:click="startEdit({{ $reply->id }})"
                                                                class="text-xs text-white/60 hover:text-white">
                                                                <i class="fa-solid fa-pen"></i>
                                                            </button>
                                                        @endif
                                                        @if (auth()->id() === $reply->user_id || auth()->id() === $video->user_id)
                                                            <button wire:click="deleteComment({{ $reply->id }})"
                                                                wire:confirm="Sei sicuro di voler eliminare questa risposta?"
                                                                class="text-xs text-white/60 hover:text-red-500">
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
                    <i class="fa-regular fa-comments text-4xl text-white/30 mb-4"></i>
                    <p class="text-white/50">{{ __('ui.no_comments_yet') }}</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="text-center py-12">
            <i class="fa-solid fa-comment-slash text-4xl text-white/30 mb-4"></i>
            <p class="text-white/50">{{ __('ui.comments_disabled') }}</p>
        </div>
    @endif
    <!-- Pagination -->
    @if ($comments->hasPages())
        <div class="mt-8">
            {{ $comments->links() }}
        </div>
    @endif
</div>
