<x-layout>
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4">
        <div class="text-center">
            <!-- 404 Icon -->
            <div class="mb-8">
                <div
                    class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-red-500 to-red-600 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-white text-6xl"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
                Pagina non trovata
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                La pagina che stai cercando non esiste o è stata spostata. Torna alla home per continuare a esplorare i
                contenuti.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <i class="fas fa-home mr-2"></i>
                    Torna alla Home
                </a>

                <button onclick="window.history.back()"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Torna Indietro
                </button>
            </div>
        </div>
    </div>
</x-layout>
