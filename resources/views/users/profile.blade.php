<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 py-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('ui.user_profile_title') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- Sidebar Menu -->
                <div class="lg:col-span-1">
                    <nav class="space-y-2">
                        <a href="#profile" onclick="showSection('profile')"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-medium">
                            <i class="fas fa-user w-5"></i>
                            <span>{{ __('ui.user_profile_profile') }}</span>
                        </a>
                        <a href="#security" onclick="showSection('security')"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-lock w-5"></i>
                            <span>{{ __('ui.user_profile_security') }}</span>
                        </a>
                        <a href="#notifications" onclick="showSection('notifications')"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-bell w-5"></i>
                            <span>{{ __('ui.user_profile_notifications') }}</span>
                        </a>
                        <a href="#preferences" onclick="showSection('preferences')"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-cog w-5"></i>
                            <span>{{ __('ui.user_profile_preferences') }}</span>
                        </a>
                        <a href="#privacy" onclick="showSection('privacy')"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-shield-alt w-5"></i>
                            <span>{{ __('ui.user_profile_privacy') }}</span>
                        </a>
                    </nav>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">

                    <!-- Profilo Section -->
                    <div id="profile-section"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_profile_info') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_profile_info_subtitle') }}</p>
                        </div>

                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-profile') }}"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf

                            <!-- Avatar Upload -->
                            <div class="flex items-center gap-6">
                                <div class="relative">
                                    <div
                                        class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 border-4 border-white dark:border-gray-600 shadow-lg">
                                        @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                                            <img id="avatarPreview"
                                                src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}"
                                                alt="{{ __('ui.avatar') }}" class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                                                <span
                                                    class="text-white font-bold text-2xl">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <label for="avatar"
                                        class="absolute bottom-0 right-0 w-8 h-8 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center cursor-pointer shadow-lg transition-colors">
                                        <i class="fas fa-camera text-white text-sm"></i>
                                        <input type="file" id="avatar" name="avatar" accept="image/*"
                                            class="hidden" onchange="previewImage(this)">
                                    </label>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_photo') }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_photo_hint') }}</p>
                                </div>
                            </div>

                            <!-- Nome -->
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_full_name') }}
                                </label>
                                <input type="text" id="name" name="name"
                                    value="{{ old('name', Auth::user()->name) }}"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('name')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_email') }}
                                </label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', Auth::user()->email) }}"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('email')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.username') }}
                                </label>
                                <input type="text" id="username" name="username"
                                    value="{{ old('username', Auth::user()->userProfile->username ?? '') }}"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ui.user_profile_username_hint') }}</p>
                                @error('username')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bio -->
                            <div>
                                <label for="bio"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_bio') }}
                                </label>
                                <textarea id="bio" name="bio" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                                    placeholder="{{ __('ui.user_profile_bio_placeholder') }}">{{ old('bio', Auth::user()->userProfile->bio ?? '') }}</textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ui.user_profile_bio_limit', ['count' => 160]) }}</p>
                                @error('bio')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_profile_save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sicurezza Section -->
                    <div id="security-section"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_profile_security_title') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_profile_security_subtitle') }}</p>
                        </div>

                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-password') }}">
                            @csrf

                            <!-- Password Attuale -->
                            <div>
                                <label for="current_password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_current_password') }}
                                </label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('current_password')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nuova Password -->
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_new_password') }}
                                </label>
                                <input type="password" id="password" name="password"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ui.user_profile_password_min') }}</p>
                                @error('password')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Conferma Password -->
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_confirm_password') }}
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_profile_update_password') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notifiche Section -->
                    <div id="notifications-section"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_profile_notifications_title') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_profile_notifications_subtitle') }}</p>
                        </div>

                        <form class="p-6 space-y-6" method="POST"
                            action="{{ route('users.update-notifications') }}">
                            @method('PUT')
                            @csrf

                            <!-- Notifiche Email -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_email_notifications') }}</h3>

                                <label
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_new_subscribers') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_new_subscribers_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="email_new_subscribers" class="toggle-checkbox"
                                        {{ old('email_new_subscribers', $preferences['notifications']['email_new_subscribers'] ?? true) ? 'checked' : '' }}>
                                </label>

                                <label
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_video_comments') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_video_comments_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="email_video_comments" class="toggle-checkbox"
                                        {{ old('email_video_comments', $preferences['notifications']['email_video_comments'] ?? true) ? 'checked' : '' }}>
                                </label>

                                <label
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_platform_updates') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_platform_updates_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="email_platform_updates" class="toggle-checkbox"
                                        {{ old('email_platform_updates', $preferences['notifications']['email_platform_updates'] ?? false) ? 'checked' : '' }}>
                                </label>
                            </div>

                            <!-- Notifiche Push -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_push_notifications') }}</h3>

                                <label
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_realtime_notifications') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_realtime_notifications_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="push_realtime_notifications" class="toggle-checkbox"
                                        {{ old('push_realtime_notifications', $preferences['notifications']['push_realtime_notifications'] ?? true) ? 'checked' : '' }}>
                                </label>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_profile_save_notifications') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Preferenze Section -->
                    <div id="preferences-section"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_profile_preferences_title') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_profile_preferences_subtitle') }}</p>
                        </div>

                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-preferences') }}">
                            @method('PUT')
                            @csrf

                            <!-- Lingua -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_language') }}
                                </label>
                                <select name="language"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="it"
                                        {{ old('language', $preferences['app_preferences']['language'] ?? 'it') == 'it' ? 'selected' : '' }}>
                                        {{ __('ui.user_profile_lang_it') }}</option>
                                    <option value="en"
                                        {{ old('language', $preferences['app_preferences']['language'] ?? 'it') == 'en' ? 'selected' : '' }}>
                                        {{ __('ui.user_profile_lang_en') }}</option>
                                    <option value="es"
                                        {{ old('language', $preferences['app_preferences']['language'] ?? 'it') == 'es' ? 'selected' : '' }}>
                                        {{ __('ui.user_profile_lang_es') }}</option>
                                    <option value="fr"
                                        {{ old('language', $preferences['app_preferences']['language'] ?? 'it') == 'fr' ? 'selected' : '' }}>
                                        {{ __('ui.user_profile_lang_fr') }}</option>
                                </select>
                            </div>

                            <!-- Fuso Orario -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_profile_timezone') }}
                                </label>
                                <select name="timezone"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Europe/Rome"
                                        {{ old('timezone', $preferences['app_preferences']['timezone'] ?? 'Europe/Rome') == 'Europe/Rome' ? 'selected' : '' }}>
                                        {{ __('ui.user_profile_tz_rome') }}</option>
                                    <option value="Europe/London"
                                        {{ old('timezone', $preferences['app_preferences']['timezone'] ?? 'Europe/Rome') == 'Europe/London' ? 'selected' : '' }}>
                                        {{ __('ui.user_profile_tz_london') }}</option>
                                    <option value="America/New_York"
                                        {{ old('timezone', $preferences['app_preferences']['timezone'] ?? 'Europe/Rome') == 'America/New_York' ? 'selected' : '' }}>
                                        {{ __('ui.user_profile_tz_new_york') }}</option>
                                </select>
                            </div>

                            <!-- Tema -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('ui.user_profile_theme') }}
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label
                                        class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <input type="radio" name="theme" value="light" class="sr-only"
                                            {{ old('theme', $preferences['app_preferences']['theme'] ?? 'dark') == 'light' ? 'checked' : '' }}>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-white border border-gray-300 rounded"></div>
                                            <span class="text-gray-900 dark:text-white">{{ __('ui.user_profile_theme_light') }}</span>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <input type="radio" name="theme" value="dark" class="sr-only"
                                            {{ old('theme', $preferences['app_preferences']['theme'] ?? 'dark') == 'dark' ? 'checked' : '' }}>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gray-800 border border-gray-600 rounded"></div>
                                            <span class="text-gray-900 dark:text-white">{{ __('ui.user_profile_theme_dark') }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_profile_save_preferences') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Privacy Section -->
                    <div id="privacy-section"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_profile_privacy_title') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_profile_privacy_subtitle') }}</p>
                        </div>

                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-privacy') }}">
                            @method('PUT')
                            @csrf

                            <!-- Visibilità Profilo -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_profile_visibility') }}</h3>

                                <label
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_public_profile') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_public_profile_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="profile_public" class="toggle-checkbox" checked>
                                </label>

                                <label
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_show_activity') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_show_activity_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="show_activity" class="toggle-checkbox">
                                </label>
                            </div>

                            <!-- Condivisione Dati -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_data_sharing') }}</h3>

                                <label
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_profile_anonymous_analytics') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_profile_anonymous_analytics_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="analytics_privacy" class="toggle-checkbox" checked>
                                </label>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_profile_save_privacy') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const userProfileI18n = {
            themeSavedLocal: @json(__('ui.user_profile_theme_saved_local')),
            autosaveError: @json(__('ui.user_profile_autosave_error')),
            savedAuto: @json(__('ui.user_profile_saved_auto')),
            saveError: @json(__('ui.user_profile_save_error')),
            loadError: @json(__('ui.user_profile_load_error'))
        };

        // Gestione Tema Dinamico
        class ThemeManager {
            constructor() {
                this.currentTheme = this.getStoredTheme() || 'dark';
                this.init();
            }

            init() {
                this.applyTheme(this.currentTheme);
                this.bindThemeEvents();
                this.updateThemeUI();
            }

            getStoredTheme() {
                return localStorage.getItem('user-theme') || sessionStorage.getItem('user-theme');
            }

            setStoredTheme(theme) {
                localStorage.setItem('user-theme', theme);
                sessionStorage.setItem('user-theme', theme);
            }

            applyTheme(theme) {
                const html = document.documentElement;
                const body = document.body;

                // Rimuovi tutte le classi tema
                html.classList.remove('light', 'dark');
                body.classList.remove('light', 'dark');

                // Applica il nuovo tema
                html.classList.add(theme);
                body.classList.add(theme);

                this.currentTheme = theme;
                this.setStoredTheme(theme);

                // Aggiorna il meta theme-color per i mobile
                this.updateMetaThemeColor(theme);

                // Dispatch evento personalizzato per altri componenti
                window.dispatchEvent(new CustomEvent('themeChanged', {
                    detail: {
                        theme
                    }
                }));
            }

            updateMetaThemeColor(theme) {
                let metaThemeColor = document.querySelector('meta[name="theme-color"]');
                if (!metaThemeColor) {
                    metaThemeColor = document.createElement('meta');
                    metaThemeColor.name = 'theme-color';
                    document.head.appendChild(metaThemeColor);
                }

                metaThemeColor.content = theme === 'dark' ? '#111827' : '#ffffff';
            }

            bindThemeEvents() {
                // Ascolta i cambiamenti dei radio button tema
                document.addEventListener('change', (e) => {
                    if (e.target.name === 'theme') {
                        this.applyTheme(e.target.value);
                        this.updateThemeUI();
                    }
                });

                // Ascolta快捷键 per il cambio tema (opzionale)
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.shiftKey && e.key === 'T') {
                        e.preventDefault();
                        this.toggleTheme();
                    }
                });
            }

            updateThemeUI() {
                const themeRadios = document.querySelectorAll('input[name="theme"]');
                themeRadios.forEach(radio => {
                    radio.checked = radio.value === this.currentTheme;
                });
            }

            toggleTheme() {
                const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
                this.applyTheme(newTheme);
                this.updateThemeUI();

                // Auto-save delle preferenze
                this.saveThemePreference(newTheme);
            }

            saveThemePreference(theme) {
                // Salva sul server tramite API call
                fetch('/users/update-preferences', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        theme: theme,
                        _method: 'PUT'
                    })
                }).catch(error => {
                    console.log(userProfileI18n.themeSavedLocal, error);
                });
            }
        }

        // Gestione Sezioni Dinamica
        function showSection(sectionName) {
            // Nascondi tutte le sezioni
            const sections = ['profile', 'security', 'notifications', 'preferences', 'privacy'];
            sections.forEach(section => {
                const element = document.getElementById(section + '-section');
                if (element) {
                    element.classList.add('hidden');
                }

                // Aggiorna sidebar
                const link = document.querySelector(`a[href="#${section}"]`);
                if (link) {
                    link.classList.remove('bg-red-50', 'dark:bg-red-900/20', 'text-red-600', 'dark:text-red-400');
                    link.classList.add('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100',
                        'dark:hover:bg-gray-800');
                }
            });

            // Mostra sezione selezionata
            const activeSection = document.getElementById(sectionName + '-section');
            if (activeSection) {
                activeSection.classList.remove('hidden');
            }

            // Aggiorna sidebar attivo
            const activeLink = document.querySelector(`a[href="#${sectionName}"]`);
            if (activeLink) {
                activeLink.classList.add('bg-red-50', 'dark:bg-red-900/20', 'text-red-600', 'dark:text-red-400');
                activeLink.classList.remove('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100',
                    'dark:hover:bg-gray-800');
            }
        }

        // Gestione Avatar Upload
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Gestione Auto-save Preferenze
        class AutoSaveManager {
            constructor() {
                this.saveTimeout = null;
                this.init();
            }

            init() {
                // Auto-save per i toggle delle notifiche
                document.addEventListener('change', (e) => {
                    if (e.target.classList.contains('toggle-checkbox')) {
                        this.scheduleSave('notifications');
                    }
                });

                // Auto-save per select language e timezone
                document.addEventListener('change', (e) => {
                    if (e.target.name === 'language' || e.target.name === 'timezone') {
                        this.scheduleSave('preferences');
                    }
                });
            }

            scheduleSave(category) {
                clearTimeout(this.saveTimeout);
                this.saveTimeout = setTimeout(() => {
                    this.savePreferences(category);
                }, 2000); // Salva dopo 2 secondi di inattività
            }

            savePreferences(category) {
                const form = document.querySelector(`#${category}-section form`);
                if (!form) return;

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(response => {
                    if (response.ok) {
                        this.showSaveIndicator(category, true);
                    } else {
                        this.showSaveIndicator(category, false);
                    }
                }).catch(error => {
                    console.log(userProfileI18n.autosaveError, error);
                    this.showSaveIndicator(category, false);
                });
            }

            showSaveIndicator(category, success) {
                const indicator = document.getElementById(`${category}-save-indicator`);
                if (indicator) {
                    indicator.textContent = success ? userProfileI18n.savedAuto : userProfileI18n.saveError;
                    indicator.className = success ?
                        'text-green-600 dark:text-green-400 text-sm' :
                        'text-red-600 dark:text-red-400 text-sm';

                    setTimeout(() => {
                        indicator.textContent = '';
                    }, 3000);
                }
            }
        }

        // Inizializzazione quando il DOM è pronto
        document.addEventListener('DOMContentLoaded', function() {
            // Inizializza gestore tema
            window.themeManager = new ThemeManager();

            // Inizializza auto-save
            window.autoSaveManager = new AutoSaveManager();

            // Configura toggle switches
            const toggles = document.querySelectorAll('.toggle-checkbox');
            toggles.forEach(toggle => {
                toggle.className =
                    'w-5 h-5 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2';
            });

            // Aggiungi indicatori di salvataggio automatico
            const sections = ['notifications', 'preferences', 'privacy'];
            sections.forEach(section => {
                const form = document.querySelector(`#${section}-section form`);
                if (form) {
                    const indicator = document.createElement('div');
                    indicator.id = `${section}-save-indicator`;
                    indicator.className = 'text-sm mt-2';
                    form.appendChild(indicator);
                }
            });

            // Carica dati dinamici dal server
            loadDynamicProfileData();
        });

        // Caricamento dati profilo dinamici
        async function loadDynamicProfileData() {
            try {
                const response = await fetch('/api/user/profile-data');
                const data = await response.json();

                if (data.success) {
                    updateProfileDisplay(data.user);
                }
            } catch (error) {
                console.log(userProfileI18n.loadError, error);
            }
        }

        function updateProfileDisplay(userData) {
            // Aggiorna nome utente se cambiato
            const nameField = document.getElementById('name');
            if (nameField && userData.name !== nameField.value) {
                nameField.value = userData.name;
            }

            // Aggiorna email se cambiata
            const emailField = document.getElementById('email');
            if (emailField && userData.email !== emailField.value) {
                emailField.value = userData.email;
            }

            // Aggiorna username se cambiato
            const usernameField = document.getElementById('username');
            if (usernameField && userData.userProfile?.username !== usernameField.value) {
                usernameField.value = userData.userProfile?.username || '';
            }

            // Aggiorna bio se cambiata
            const bioField = document.getElementById('bio');
            if (bioField && userData.userProfile?.bio !== bioField.value) {
                bioField.value = userData.userProfile?.bio || '';
            }

            // Aggiorna avatar se cambiato
            if (userData.userProfile?.avatar_url) {
                const avatarImg = document.getElementById('avatarPreview');
                if (avatarImg) {
                    avatarImg.src = `/storage/${userData.userProfile.avatar_url}`;
                }
            }
        }
    </script>

    <style>
        /* Toggle switches styling */
        .toggle-checkbox:checked {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        .toggle-checkbox:focus {
            ring-color: #dc2626;
        }

        /* Theme transition animations */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Smooth theme switching */
        html,
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Theme-specific animations */
        .bg-white,
        .bg-gray-50,
        .bg-gray-100 {
            transition: background-color 0.3s ease;
        }

        .dark .bg-white,
        .dark .bg-gray-50,
        .dark .bg-gray-100 {
            transition: background-color 0.3s ease;
        }

        /* Form elements theme support */
        input,
        textarea,
        select {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Button hover effects */
        button {
            transition: all 0.2s ease;
        }

        /* Loading indicator */
        .save-indicator {
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .save-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Avatar upload area */
        .avatar-upload-area {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .avatar-upload-area:hover {
            transform: scale(1.02);
        }

        /* Theme preview cards */
        .theme-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .theme-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .theme-card.selected {
            border-color: #dc2626;
            background-color: rgba(220, 38, 38, 0.05);
        }

        .dark .theme-card.selected {
            background-color: rgba(220, 38, 38, 0.1);
        }

        /* Dynamic content loading */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        .dark .loading-skeleton {
            background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
            background-size: 200% 100%;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Success/Error messages */
        .alert {
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .theme-card {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</x-layout>
