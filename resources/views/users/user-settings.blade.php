<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 py-8">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('ui.user_settings_title') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Sidebar Menu -->
                <div class="lg:col-span-1">
                    <nav class="space-y-2">
                        <a href="#profile" onclick="showSection('profile')" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-medium">
                            <i class="fas fa-user w-5"></i>
                            <span>{{ __('ui.user_settings_profile') }}</span>
                        </a>
                        <a href="#account" onclick="showSection('account')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-cog w-5"></i>
                            <span>{{ __('ui.user_settings_account') }}</span>
                        </a>
                        <a href="#notifications" onclick="showSection('notifications')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-bell w-5"></i>
                            <span>{{ __('ui.user_settings_notifications') }}</span>
                        </a>
                        <a href="#privacy" onclick="showSection('privacy')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-shield-alt w-5"></i>
                            <span>{{ __('ui.user_settings_privacy') }}</span>
                        </a>
                        <a href="#security" onclick="showSection('security')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-lock w-5"></i>
                            <span>{{ __('ui.user_settings_security') }}</span>
                        </a>
                    </nav>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    
                    <!-- Profilo Section -->
                    <div id="profile-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_settings_profile_info') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_settings_profile_info_subtitle') }}</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-profile') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Avatar Upload -->
                            <div class="flex items-center gap-6">
                                <div class="relative">
                                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 border-4 border-white dark:border-gray-600 shadow-lg">
                                        @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                                            <img id="avatarPreview" src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}" 
                                                 alt="{{ __('ui.avatar') }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                                                <span class="text-white font-bold text-2xl">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <label for="avatar" class="absolute bottom-0 right-0 w-8 h-8 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center cursor-pointer shadow-lg transition-colors">
                                        <i class="fas fa-camera text-white text-sm"></i>
                                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewImage(this)">
                                    </label>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_profile_photo') }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_photo_hint') }}</p>
                                </div>
                            </div>

                            <!-- Nome -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_full_name') }}
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('name')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_email') }}
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('email')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.username') }}
                                </label>
                                <input type="text" id="username" name="username" 
                                       value="{{ old('username', Auth::user()->userProfile->username ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ui.user_settings_username_hint') }}</p>
                                @error('username')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bio -->
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_bio') }}
                                </label>
                                <textarea id="bio" name="bio" rows="4" 
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                                          placeholder="{{ __('ui.user_settings_bio_placeholder') }}">{{ old('bio', Auth::user()->userProfile->bio ?? '') }}</textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ui.user_settings_bio_limit', ['count' => 160]) }}</p>
                                @error('bio')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_settings_save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Account Section -->
                    <div id="account-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_settings_account_settings') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_settings_account_settings_subtitle') }}</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-preferences') }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Lingua -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_language') }}
                                </label>
                                <select name="language" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="it">{{ __('ui.user_settings_lang_it') }}</option>
                                    <option value="en">{{ __('ui.user_settings_lang_en') }}</option>
                                    <option value="es">{{ __('ui.user_settings_lang_es') }}</option>
                                    <option value="fr">{{ __('ui.user_settings_lang_fr') }}</option>
                                </select>
                            </div>

                            <!-- Fuso Orario -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_timezone') }}
                                </label>
                                <select name="timezone" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Europe/Rome">{{ __('ui.user_settings_tz_rome') }}</option>
                                    <option value="Europe/London">{{ __('ui.user_settings_tz_london') }}</option>
                                    <option value="America/New_York">{{ __('ui.user_settings_tz_new_york') }}</option>
                                </select>
                            </div>

                            <!-- Tema -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('ui.user_settings_theme') }}
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <input type="radio" name="theme" value="light" class="sr-only">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-white border border-gray-300 rounded"></div>
                                            <span class="text-gray-900 dark:text-white">{{ __('ui.user_settings_theme_light') }}</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <input type="radio" name="theme" value="dark" class="sr-only" checked>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gray-800 border border-gray-600 rounded"></div>
                                            <span class="text-gray-900 dark:text-white">{{ __('ui.user_settings_theme_dark') }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_settings_save_preferences') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notifiche Section -->
                    <div id="notifications-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_settings_notifications_title') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_settings_notifications_subtitle') }}</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-notifications') }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Notifiche Email -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_email_notifications') }}</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_new_subscribers') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_new_subscribers_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="email_new_subscribers" class="toggle-checkbox" checked>
                                </label>

                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_video_comments') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_video_comments_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="email_video_comments" class="toggle-checkbox" checked>
                                </label>

                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_platform_updates') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_platform_updates_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="email_platform_updates" class="toggle-checkbox">
                                </label>
                            </div>

                            <!-- Notifiche Push -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_push_notifications') }}</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_realtime_notifications') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_realtime_notifications_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="push_realtime_notifications" class="toggle-checkbox" checked>
                                </label>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_settings_save_notifications') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Privacy Section -->
                    <div id="privacy-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_settings_privacy_title') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_settings_privacy_subtitle') }}</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-privacy') }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Visibilità Profilo -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_profile_visibility') }}</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_public_profile') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_public_profile_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="profile_public" class="toggle-checkbox" checked>
                                </label>

                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_show_activity') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_show_activity_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="show_activity" class="toggle-checkbox">
                                </label>
                            </div>

                            <!-- Condivisione Dati -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_data_sharing') }}</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.user_settings_anonymous_analytics') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.user_settings_anonymous_analytics_hint') }}</p>
                                    </div>
                                    <input type="checkbox" name="analytics_privacy" class="toggle-checkbox" checked>
                                </label>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_settings_save_privacy') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sicurezza Section -->
                    <div id="security-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('ui.user_settings_security_title') }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('ui.user_settings_security_subtitle') }}</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-password') }}">
                            @csrf
                            
                            <!-- Password Attuale -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_current_password') }}
                                </label>
                                <input type="password" id="current_password" name="current_password"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('current_password')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nuova Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_new_password') }}
                                </label>
                                <input type="password" id="password" name="password"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ui.user_settings_password_min') }}</p>
                                @error('password')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Conferma Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.user_settings_confirm_password') }}
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    {{ __('ui.user_settings_update_password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionName) {
            // Nascondi tutte le sezioni
            const sections = ['profile', 'account', 'notifications', 'privacy', 'security'];
            sections.forEach(section => {
                const element = document.getElementById(section + '-section');
                if (element) {
                    element.classList.add('hidden');
                }
                
                // Aggiorna sidebar
                const link = document.querySelector(`a[href="#${section}"]`);
                if (link) {
                    link.classList.remove('bg-red-50', 'dark:bg-red-900/20', 'text-red-600', 'dark:text-red-400');
                    link.classList.add('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-800');
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
                activeLink.classList.remove('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-800');
            }
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle switches styling
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.toggle-checkbox');
            toggles.forEach(toggle => {
                toggle.className = 'w-5 h-5 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2';
            });
        });
    </script>

    <style>
        .toggle-checkbox:checked {
            background-color: #dc2626;
            border-color: #dc2626;
        }
        
        .toggle-checkbox:focus {
            ring-color: #dc2626;
        }
    </style>
</x-layout>
