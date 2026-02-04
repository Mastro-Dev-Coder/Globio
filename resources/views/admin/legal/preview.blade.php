<x-admin-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-8">
                    <!-- Header di anteprima admin -->
                    <div
                        class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Anteprima Admin
                                </h3>
                                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                    Stai visualizzando l'anteprima di {{ $legalPage->title }}. Le modifiche sono già
                                    attive sul sito.
                                </p>
                            </div>
                            <div class="ml-auto">
                                <a href="{{ route('admin.legal.edit', $legalPage->slug) }}"
                                    class="inline-flex items-center rounded-md bg-yellow-600 px-2 py-1 text-xs font-medium text-white hover:bg-yellow-700">
                                    Modifica
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contenuto della pagina -->
                    <div class="prose prose-gray dark:prose-invert max-w-none">
                        {!! $legalPage->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
