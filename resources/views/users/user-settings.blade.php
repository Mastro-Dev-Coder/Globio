<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 py-8">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Impostazioni Account</h1>
                <p class="text-gray-600 dark:text-gray-400">Gestisci le tue informazioni personali e le preferenze dell'account</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Sidebar Menu -->
                <div class="lg:col-span-1">
                    <nav class="space-y-2">
                        <a href="#profile" onclick="showSection('profile')" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-medium">
                            <i class="fas fa-user w-5"></i>
                            <span>Profilo</span>
                        </a>
                        <a href="#account" onclick="showSection('account')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-cog w-5"></i>
                            <span>Account</span>
                        </a>
                        <a href="#notifications" onclick="showSection('notifications')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-bell w-5"></i>
                            <span>Notifiche</span>
                        </a>
                        <a href="#privacy" onclick="showSection('privacy')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-shield-alt w-5"></i>
                            <span>Privacy</span>
                        </a>
                        <a href="#security" onclick="showSection('security')"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-lock w-5"></i>
                            <span>Sicurezza</span>
                        </a>
                    </nav>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    
                    <!-- Profilo Section -->
                    <div id="profile-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Informazioni Profilo</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Aggiorna le tue informazioni personali di base</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-profile') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Avatar Upload -->
                            <div class="flex items-center gap-6">
                                <div class="relative">
                                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 border-4 border-white dark:border-gray-600 shadow-lg">
                                        @if (Auth::user()->userProfile && Auth::user()->userProfile->avatar_url)
                                            <img id="avatarPreview" src="{{ asset('storage/' . Auth::user()->userProfile->avatar_url) }}" 
                                                 alt="Avatar" class="w-full h-full object-cover">
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
                                    <h3 class="font-medium text-gray-900 dark:text-white">Foto Profilo</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">JPG, PNG o GIF. Max 2MB.</p>
                                </div>
                            </div>

                            <!-- Nome -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nome completo
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
                                    Email
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
                                    Username
                                </label>
                                <input type="text" id="username" name="username" 
                                       value="{{ old('username', Auth::user()->userProfile->username ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Questo sarà il nome del tuo canale</p>
                                @error('username')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bio -->
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Biografia
                                </label>
                                <textarea id="bio" name="bio" rows="4" 
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                                          placeholder="Racconta qualcosa su di te...">{{ old('bio', Auth::user()->userProfile->bio ?? '') }}</textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 160 caratteri</p>
                                @error('bio')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    Salva Modifiche
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Account Section -->
                    <div id="account-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Impostazioni Account</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Gestisci le preferenze del tuo account</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-preferences') }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Lingua -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Lingua
                                </label>
                                <select name="language" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="it">Italiano</option>
                                    <option value="en">English</option>
                                    <option value="es">Español</option>
                                    <option value="fr">Français</option>
                                </select>
                            </div>

                            <!-- Fuso Orario -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Fuso Orario
                                </label>
                                <select name="timezone" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Europe/Rome">Europa/Roma (UTC+1)</option>
                                    <option value="Europe/London">Europa/Londra (UTC+0)</option>
                                    <option value="America/New_York">America/New York (UTC-5)</option>
                                </select>
                            </div>

                            <!-- Tema -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Tema
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <input type="radio" name="theme" value="light" class="sr-only">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-white border border-gray-300 rounded"></div>
                                            <span class="text-gray-900 dark:text-white">Chiaro</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <input type="radio" name="theme" value="dark" class="sr-only" checked>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gray-800 border border-gray-600 rounded"></div>
                                            <span class="text-gray-900 dark:text-white">Scuro</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    Salva Preferenze
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notifiche Section -->
                    <div id="notifications-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Preferenze Notifiche</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Scegli quali notifiche ricevere</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-notifications') }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Notifiche Email -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">Notifiche Email</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Nuovi iscritti</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Ricevi email quando qualcuno si iscrive al tuo canale</p>
                                    </div>
                                    <input type="checkbox" name="email_new_subscribers" class="toggle-checkbox" checked>
                                </label>

                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Commenti sui video</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Ricevi email per nuovi commenti sui tuoi video</p>
                                    </div>
                                    <input type="checkbox" name="email_video_comments" class="toggle-checkbox" checked>
                                </label>

                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Aggiornamenti piattaforma</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Ricevi aggiornamenti su nuove funzionalità</p>
                                    </div>
                                    <input type="checkbox" name="email_platform_updates" class="toggle-checkbox">
                                </label>
                            </div>

                            <!-- Notifiche Push -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">Notifiche Push</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Notifiche in tempo reale</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Ricevi notifiche push nel browser</p>
                                    </div>
                                    <input type="checkbox" name="push_realtime_notifications" class="toggle-checkbox" checked>
                                </label>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    Salva Notifiche
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Privacy Section -->
                    <div id="privacy-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Impostazioni Privacy</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Controlla chi può vedere i tuoi contenuti</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-privacy') }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Visibilità Profilo -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">Visibilità Profilo</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Profilo pubblico</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Il tuo profilo può essere trovato dagli altri utenti</p>
                                    </div>
                                    <input type="checkbox" name="profile_public" class="toggle-checkbox" checked>
                                </label>

                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Mostra attività</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Mostra i video che guardi nel tuo feed pubblico</p>
                                    </div>
                                    <input type="checkbox" name="show_activity" class="toggle-checkbox">
                                </label>
                            </div>

                            <!-- Condivisione Dati -->
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-900 dark:text-white">Condivisione Dati</h3>
                                
                                <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Analisi anonymous</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Aiuta a migliorare la piattaforma con dati anonimi</p>
                                    </div>
                                    <input type="checkbox" name="analytics_privacy" class="toggle-checkbox" checked>
                                </label>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    Salva Privacy
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sicurezza Section -->
                    <div id="security-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Sicurezza Account</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Gestisci password e sicurezza</p>
                        </div>
                        
                        <form class="p-6 space-y-6" method="POST" action="{{ route('users.update-password') }}">
                            @csrf
                            
                            <!-- Password Attuale -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Password attuale
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
                                    Nuova password
                                </label>
                                <input type="password" id="password" name="password"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimo 8 caratteri</p>
                                @error('password')
                                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Conferma Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Conferma nuova password
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    Aggiorna Password
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