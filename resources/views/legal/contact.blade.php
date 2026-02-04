<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ $page->title ?? 'Contattaci' }}</h1>
                        @if ($page && $page->content)
                            <div
                                class="prose prose-gray dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 mb-4">
                                {!! $page->content !!}
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">Hai domande o suggerimenti? Siamo qui per
                                aiutarti!
                            </p>
                        @endif
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Informazioni di contatto -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informazioni di Contatto
                            </h2>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                        <p class="text-gray-900 dark:text-white">info@globio.com</p>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Indirizzo</p>
                                        <p class="text-gray-900 dark:text-white">Roma, Italia</p>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Orari</p>
                                        <p class="text-gray-900 dark:text-white">Lun-Ven 9:00 - 18:00</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form di contatto -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Inviaci un Messaggio
                            </h2>

                            @if (session('success'))
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('send.contact') }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="nome"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome
                                        *</label>
                                    <input type="text" id="nome" name="nome" required
                                        class="mt-1 p-2 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    @error('nome')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email
                                        *</label>
                                    <input type="email" id="email" name="email" required
                                        class="mt-1 p-2 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="oggetto"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Oggetto
                                        *</label>
                                    <input type="text" id="oggetto" name="oggetto" required
                                        class="mt-1 p-2 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    @error('oggetto')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="messaggio"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Messaggio
                                        *</label>
                                    <textarea id="messaggio" name="messaggio" rows="4" required
                                        class="mt-1 p-2 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"></textarea>
                                    @error('messaggio')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                                    Invia Messaggio
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
