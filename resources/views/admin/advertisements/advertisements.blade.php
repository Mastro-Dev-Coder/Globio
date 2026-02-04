<x-admin-layout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestione Pubblicità</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Gestisci banner, AdSense e pubblicità video</p>
            </div>
            <a href="{{ route('admin.advertisements.create') }}"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus w-4 h-4 mr-2"></i>
                Nuova Pubblicità
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-bullhorn text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Totale</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-play-circle text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Attive</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-image text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Banner</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['banner'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fab fa-google text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">AdSense</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['adsense'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-video text-2xl text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Video</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['video'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ricerca</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nome pubblicità..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                    <select name="type"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Tutti i tipi</option>
                        <option value="banner" {{ request('type') === 'banner' ? 'selected' : '' }}>Banner</option>
                        <option value="adsense" {{ request('type') === 'adsense' ? 'selected' : '' }}>AdSense</option>
                        <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Posizione</label>
                    <select name="position"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Tutte le posizioni</option>
                        <option value="footer" {{ request('position') === 'footer' ? 'selected' : '' }}>Footer</option>
                        <option value="between_videos" {{ request('position') === 'between_videos' ? 'selected' : '' }}>Tra i Video</option>
                        <option value="home_video" {{ request('position') === 'home_video' ? 'selected' : '' }}>Video Home</option>
                        <option value="video_overlay" {{ request('position') === 'video_overlay' ? 'selected' : '' }}>Overlay Video</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stato</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Tutti</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Attive</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inattive</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-search w-4 h-4 mr-2"></i>
                        Filtra
                    </button>
                </div>
            </form>
        </div>

        <!-- Advertisements Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pubblicità
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tipo & Posizione
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statistiche
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stato
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($advertisements as $advertisement)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($advertisement->image_url)
                                            <img src="{{ asset('storage/' . $advertisement->image_url) }}"
                                                alt="{{ $advertisement->name }}"
                                                class="w-12 h-8 object-cover rounded border">
                                        @else
                                            <div class="w-12 h-8 bg-gray-200 dark:bg-gray-600 rounded border flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $advertisement->name }}
                                            </div>
                                            @if($advertisement->link_url)
                                                <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                    <a href="{{ $advertisement->link_url }}" target="_blank"
                                                        class="hover:text-red-600">
                                                        {{ $advertisement->link_url }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $advertisement->type === 'banner' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : '' }}
                                            {{ $advertisement->type === 'adsense' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                            {{ $advertisement->type === 'video' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                            {{ $advertisement->type_label }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $advertisement->position_label }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div class="space-y-1">
                                        <div class="flex items-center">
                                            <i class="fas fa-eye w-3 h-3 mr-1"></i>
                                            {{ number_format($advertisement->views) }}
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-mouse-pointer w-3 h-3 mr-1"></i>
                                            {{ number_format($advertisement->clicks) }}
                                        </div>
                                        @if($advertisement->views > 0)
                                            <div class="text-xs">
                                                CTR: {{ number_format(($advertisement->clicks / $advertisement->views) * 100, 2) }}%
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($advertisement->isCurrentlyActive())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            <i class="fas fa-circle w-2 h-2 mr-1"></i>
                                            Attiva
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                            <i class="fas fa-circle w-2 h-2 mr-1"></i>
                                            Inattiva
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div class="space-y-1">
                                        @if($advertisement->start_date)
                                            <div>
                                                <span class="font-medium">Inizio:</span><br>
                                                {{ $advertisement->start_date->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                        @if($advertisement->end_date)
                                            <div>
                                                <span class="font-medium">Fine:</span><br>
                                                {{ $advertisement->end_date->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.advertisements.edit', $advertisement) }}"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Modifica">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.advertisements.stats', $advertisement) }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                            title="Statistiche">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.advertisements.toggle-status', $advertisement) }}"
                                            class="inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit"
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                title="{{ $advertisement->is_active ? 'Disattiva' : 'Attiva' }}">
                                                <i class="fas {{ $advertisement->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.advertisements.delete', $advertisement) }}"
                                            class="inline"
                                            onsubmit="return confirm('Sei sicuro di voler eliminare questa pubblicità?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                title="Elimina">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-bullhorn text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">Nessuna pubblicità trovata</p>
                                        <p class="text-sm">Crea la tua prima pubblicità per iniziare</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($advertisements->hasPages())
                <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $advertisements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>