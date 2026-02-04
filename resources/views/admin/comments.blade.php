<x-admin-layout>
    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <i class="fas fa-comments text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Commenti Totali</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $comments->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <i class="fas fa-calendar-day text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Oggi</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Comment::whereDate('created_at', today())->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <i class="fas fa-calendar-week text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Questa Settimana</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Comment::where('created_at', '>=', now()->startOfWeek())->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Questo Mese</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Comment::whereMonth('created_at', now()->month)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('admin.comments') }}" class="flex items-center space-x-4">
                <input type="text" name="search" placeholder="Cerca commenti..." value="{{ $search ?? '' }}"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Cerca
                </button>
            </form>
        </div>

        <!-- Comments Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lista Commenti</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Commento
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Autore
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Video
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Data
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($comments as $comment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            @if ($comment->user->userprofile->avatar_url)
                                                <img src="{{ asset('storage/' . $comment->user->userprofile->avatar_url) }}"
                                                    class="w-10 h-10 rounded-full" alt="{{ $comment->user->name }}">
                                            @else
                                                <span class="text-white text-sm font-medium">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900 dark:text-white line-clamp-3">
                                                {{ $comment->content }}
                                            </p>
                                            <div class="mt-2 flex items-center space-x-4">
                                                @if ($comment->parent_id)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        <i class="fas fa-reply mr-1"></i>
                                                        Risposta
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $comment->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $comment->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded overflow-hidden flex-shrink-0">
                                            @if ($comment->video->thumbnail_path)
                                                <img src="{{ asset('storage/' . $comment->video->thumbnail_path) }}"
                                                    alt="{{ $comment->video->title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-video text-gray-400 text-sm"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1">
                                                {{ Str::limit($comment->video->title, 30) }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ number_format($comment->video->views_count) }} visualizzazioni
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col">
                                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $comment->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if ($comment->video && $comment->video->status === 'published')
                                            <a href="{{ route('videos.show', $comment->video) }}" target="_blank"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                                title="Visualizza video">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.users.show', $comment->user) }}"
                                            class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300"
                                            title="Profilo utente">
                                            <i class="fas fa-user"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.comments.delete', $comment) }}"
                                            class="inline"
                                            onsubmit="return confirm('Sei sicuro di voler eliminare questo commento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                title="Elimina commento">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-comments text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Nessun commento trovato</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($comments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
