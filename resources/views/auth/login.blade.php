<x-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 text-3xl font-bold">
                    <div class="w-10 h-10 bg-primary-gradient rounded-lg flex items-center justify-center">
                        <i class="fas fa-play text-white"></i>
                    </div>
                    <span class="text-primary-gradient">
                        {{ \App\Models\Setting::getValue('site_name') }}
                    </span>
                </a>
                <h2 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">
                    Accedi al tuo account
                </h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Benvenuto! Inserisci le tue credenziali per continuare
                </p>
            </div>

            <!-- Login Form -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required autofocus
                                value="{{ old('email') }}"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
                                placeholder="tua@email.com">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-primary dark:text-primary-light">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-primary dark:text-primary-light">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Ricordami
                            </label>
                        </div>

                        <a href="#" class="text-sm font-medium text-primary hover:text-primary-dark">
                            Password dimenticata?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-white bg-primary-gradient hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary font-medium transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Accedi
                    </button>
                </form>

                <!-- Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">
                                Oppure
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Non hai un account?
                        <a href="{{ route('register') }}" class="font-medium text-primary hover:text-primary-dark">
                            Registrati ora
                        </a>
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Torna alla home
                </a>
            </div>
        </div>
    </div>

    <script>
        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)')
                    .matches)) {
                document.documentElement.classList.add('dark');
            }
        });
    </script>
</x-layout>
