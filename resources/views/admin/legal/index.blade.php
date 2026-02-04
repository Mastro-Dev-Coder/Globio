<x-admin-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Gestione Pagine Legali</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        Modifica e gestisci i contenuti delle pagine legali del sito web
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.legal.preview', 'contatti') }}" target="_blank"
                            class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                            <i class="fas fa-eye mr-2"></i>
                            Anteprima Contatti
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div
                class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pages Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($legalPages as $page)
                <div
                    class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Page Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if ($page->slug === 'contatti')
                                        <div
                                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-envelope text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                    @elseif($page->slug === 'privacy')
                                        <div
                                            class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-shield-alt text-green-600 dark:text-green-400"></i>
                                        </div>
                                    @else
                                        <div
                                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-contract text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white capitalize">
                                        {{ $page->slug }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($page->title, 30) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Content Preview -->
                    <div class="px-6 py-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Contenuto:</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                {!! Str::limit(strip_tags($page->content), 120) !!}
                            </p>
                        </div>

                        <!-- Status Information -->
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>
                                <i class="fas fa-clock mr-1"></i>
                                {{ $page->updated_at->format('d/m/Y H:i') }}
                            </span>
                            <span>
                                <i class="fas fa-align-left mr-1"></i>
                                {{ Str::length(strip_tags($page->content)) }} caratteri
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.legal.edit', $page->slug) }}"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Modifica
                            </a>
                            <a href="{{ route('admin.legal.preview', $page->slug) }}" target="_blank"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-500 transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                Anteprima
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Statistics Card -->
        <div
            class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informazioni di Sistema</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $legalPages->count() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Pagine Totali</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $legalPages->sum(function ($page) {return Str::length(strip_tags($page->content));}) }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Caratteri Totali</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $legalPages->max('updated_at')->format('d/m') }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Ultima Modifica</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2">
                        Guida alla Gestione delle Pagine Legali
                    </h3>
                    <div class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Modifica:</strong> Clicca per modificare il titolo e il contenuto HTML della
                                pagina</li>
                            <li><strong>Anteprima:</strong> Visualizza come apparirà la pagina sul sito pubblico</li>
                            <li><strong>Contenuto HTML:</strong> Utilizza tag HTML validi per la formattazione del testo
                            </li>
                            <li><strong>Aggiornamenti:</strong> Le modifiche sono immediatamente visibili agli utenti
                            </li>
                            <li><strong>Backup:</strong> I contenuti sono automaticamente salvati nel database</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-admin-layout>
