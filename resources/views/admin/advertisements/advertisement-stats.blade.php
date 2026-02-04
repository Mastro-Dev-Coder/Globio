<x-admin-layout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Statistiche Pubblicità</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Analytics dettagliate per "{{ $advertisement->name }}"
                </p>
            </div>
            <a href="{{ route('admin.advertisements') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left w-4 h-4 mr-2"></i>
                Torna alla lista
            </a>
        </div>

        <!-- Advertisement Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($advertisement->image_url)
                        <img src="{{ asset('storage/' . $advertisement->image_url) }}"
                            alt="{{ $advertisement->name }}"
                            class="w-16 h-12 object-cover rounded border">
                    @else
                        <div class="w-16 h-12 bg-gray-200 dark:bg-gray-600 rounded border flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ $advertisement->name }}
                        </h2>
                        <div class="flex items-center space-x-4 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $advertisement->type === 'banner' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : '' }}
                                {{ $advertisement->type === 'adsense' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                {{ $advertisement->type === 'video' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                {{ $advertisement->type_label }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $advertisement->position_label }}
                            </span>
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
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Creata il</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $advertisement->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-eye text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Visualizzazioni</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ number_format($advertisement->views) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-mouse-pointer text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Click</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ number_format($advertisement->clicks) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-percentage text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">CTR</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $advertisement->views > 0 ? number_format(($advertisement->clicks / $advertisement->views) * 100, 2) : '0.00' }}%
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Priorità</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $advertisement->priority }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Chart Placeholder -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-line mr-2"></i>
                Andamento nel tempo
            </h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chart-line text-4xl mb-4"></i>
                    <p class="text-lg font-medium">Grafico performance</p>
                    <p class="text-sm">I dati verranno raccolti nel tempo</p>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Date Range -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-calendar mr-2"></i>
                    Periodo di validità
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data di inizio</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            @if($advertisement->start_date)
                                {{ $advertisement->start_date->format('d/m/Y H:i') }}
                            @else
                                <span class="text-green-600 dark:text-green-400">Immediata</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data di fine</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            @if($advertisement->end_date)
                                {{ $advertisement->end_date->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Nessuna scadenza</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-tools mr-2"></i>
                    Azioni rapide
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.advertisements.edit', $advertisement) }}"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-edit w-4 h-4 mr-2"></i>
                        Modifica Pubblicità
                    </a>
                    
                    <form method="POST" action="{{ route('admin.advertisements.toggle-status', $advertisement) }}">
                        @csrf
                        @method('POST')
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 
                            {{ $advertisement->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} 
                            text-white font-medium rounded-lg transition-colors">
                            <i class="fas {{ $advertisement->is_active ? 'fa-pause' : 'fa-play' }} w-4 h-4 mr-2"></i>
                            {{ $advertisement->is_active ? 'Disattiva' : 'Attiva' }} Pubblicità
                        </button>
                    </form>

                    @if($advertisement->link_url)
                        <a href="{{ $advertisement->link_url }}" target="_blank"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-external-link-alt w-4 h-4 mr-2"></i>
                            Apri Link di destinazione
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Preview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-eye mr-2"></i>
                Anteprima contenuto
            </h3>
            
            @if($advertisement->type === 'banner')
                @if($advertisement->image_url)
                    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $advertisement->image_url) }}"
                                alt="{{ $advertisement->name }}"
                                class="max-w-full h-auto mx-auto rounded">
                            @if($advertisement->link_url)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    Link: <a href="{{ $advertisement->link_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $advertisement->link_url }}</a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            @elseif($advertisement->type === 'adsense' || $advertisement->type === 'video')
                @if($advertisement->code)
                    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Codice {{ $advertisement->type_label }}:</div>
                        <pre class="bg-gray-800 text-green-400 p-4 rounded text-xs overflow-x-auto">{{ $advertisement->code }}</pre>
                    </div>
                @endif
            @endif

            @if($advertisement->content)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Contenuto aggiuntivo:</p>
                    <p class="text-gray-900 dark:text-white">{{ $advertisement->content }}</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>