<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header Section - Personalizza Canale -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden mb-8">
                <!-- Cover Banner -->
                <div class="h-48 md:h-64 bg-gradient-to-r from-slate-700 to-slate-800 relative group overflow-hidden">
                    @if ($user->userProfile && $user->userProfile->banner_url)
                        <img src="{{ Storage::url($user->userProfile->banner_url) }}" alt="Cover"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 flex items-center justify-center">
                            <div class="text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <p class="text-sm font-medium">Cover del canale</p>
                            </div>
                        </div>
                    @endif

                    <!-- Cover Actions Overlay (Center) -->
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                        <label for="bannerInput"
                            class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $user->userProfile && $user->userProfile->banner_url ? 'Cambia cover' : 'Carica cover' }}</span>
                        </label>
                    </div>

                    <!-- Remove Banner Button (Top Right) -->
                    @if ($user->userProfile && $user->userProfile->banner_url)
                        <button type="button" onclick="removeBanner()"
                            class="absolute top-3 right-3 w-9 h-9 bg-red-500/90 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all shadow-lg backdrop-blur-sm opacity-0 group-hover:opacity-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Channel Info -->
                <div class="px-6 pb-6">
                    <div class="flex flex-col md:flex-row items-start md:items-end gap-5 -mt-12 md:-mt-16">
                        <!-- Avatar -->
                        <div class="relative group">
                            <div
                                class="w-24 h-24 md:w-28 md:h-28 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden shadow-xl bg-gradient-to-br from-red-500 to-red-600 ring-2 ring-gray-100 dark:ring-gray-700">
                                @if ($user->userProfile && $user->userProfile->avatar_url)
                                    <img id="avatarPreview" src="{{ Storage::url($user->userProfile->avatar_url) }}"
                                        alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-500 to-red-600">
                                        <span class="text-3xl md:text-4xl font-bold text-white">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Avatar Actions Overlay (Center) -->
                            <div class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                <label for="avatarInput"
                                    class="cursor-pointer flex flex-col items-center gap-1 p-3 text-white">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>
                                    </svg>
                                </label>
                            </div>

                            <!-- Remove Avatar Button (Top Right) -->
                            @if ($user->userProfile && $user->userProfile->avatar_url)
                                <button type="button" onclick="removeAvatar()"
                                    class="absolute -top-1 -right-1 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all shadow-md opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12">
                                        </path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <!-- Channel Details -->
                        <div class="flex-1 pt-2 md:pt-0">
                            <div class="flex items-center gap-2.5 mb-1.5">
                                <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $user->name }}
                                </h1>
                                @if ($user->userProfile && $user->userProfile->is_verified)
                                    <div
                                        class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-sm">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                @if ($user->userProfile && $user->userProfile->channel_name)
                                    {{ $user->userProfile->channel_name }}
                                @else
                                    Nome del canale non impostato
                                @endif
                            </p>

                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ $user->videos()->published()->count() }} video
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    {{ $user->subscribers()->count() }} iscritti
                                </span>
                            </div>
                        </div>

                        <!-- Personalizza Button -->
                        <div class="flex gap-2 pt-2 md:pt-0">
                            <label for="avatarInput"
                                class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Foto profilo
                            </label>
                            <label for="bannerInput"
                                class="cursor-pointer flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Cover
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <form id="profileForm" method="POST" action="{{ route('users.update-profile') }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8">

                        <!-- Basic Information -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-user-circle text-red-600"></i>
                                    Informazioni di base
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Le informazioni di base del tuo canale sono visibili pubblicamente
                                </p>
                            </div>

                            <div class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-user mr-2 text-gray-500"></i>
                                            Nome
                                        </label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                        @error('name')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-envelope mr-2 text-gray-500"></i>
                                            Email
                                        </label>
                                        <input type="email" name="email"
                                            value="{{ old('email', $user->email) }}"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                        @error('email')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-tv mr-2 text-gray-500"></i>
                                            Nome del canale
                                        </label>
                                        <input type="text" name="channel_name"
                                            value="{{ old('channel_name', $user->userProfile->channel_name ?? '') }}"
                                            placeholder="Il nome del tuo canale"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-globe mr-2 text-gray-500"></i>
                                            Paese
                                        </label>
                                        <select name="country"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                            <option value="">Seleziona paese</option>
                                            <option value="IT"
                                                {{ old('country', $user->userProfile->country ?? '') == 'IT' ? 'selected' : '' }}>
                                                🇮🇹 Italia
                                            </option>
                                            <option value="US"
                                                {{ old('country', $user->userProfile->country ?? '') == 'US' ? 'selected' : '' }}>
                                                🇺🇸 Stati Uniti
                                            </option>
                                            <option value="GB"
                                                {{ old('country', $user->userProfile->country ?? '') == 'GB' ? 'selected' : '' }}>
                                                🇬🇧 Regno Unito
                                            </option>
                                            <option value="FR"
                                                {{ old('country', $user->userProfile->country ?? '') == 'FR' ? 'selected' : '' }}>
                                                🇫🇷 Francia
                                            </option>
                                            <option value="DE"
                                                {{ old('country', $user->userProfile->country ?? '') == 'DE' ? 'selected' : '' }}>
                                                🇩🇪 Germania
                                            </option>
                                            <option value="ES"
                                                {{ old('country', $user->userProfile->country ?? '') == 'ES' ? 'selected' : '' }}>
                                                🇪🇸 Spagna
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-align-left mr-2 text-gray-500"></i>
                                        Descrizione del canale
                                        <span id="descriptionCount" class="text-xs text-gray-500 ml-2">0/500</span>
                                    </label>
                                    <textarea name="channel_description" rows="4"
                                        placeholder="Descrivi il tuo canale, i tuoi contenuti e cosa possono aspettarsi gli spettatori..."
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all resize-none">{{ old('channel_description', $user->userProfile->channel_description ?? '') }}</textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        Una buona descrizione aiuta gli spettatori a capire di cosa tratta il tuo
                                        canale.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-share-alt text-red-600"></i>
                                    Link social
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Collega i tuoi profili social per far scoprire agli spettatori i tuoi altri canali
                                </p>
                            </div>

                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fab fa-twitter mr-2 text-blue-400"></i>
                                            Twitter
                                        </label>
                                        <input type="url" name="social_links[twitter]"
                                            value="{{ old('social_links.twitter', $user->userProfile->social_links['twitter'] ?? '') }}"
                                            placeholder="https://twitter.com/tuoaccount"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                        <i class="fab fa-twitter absolute left-3 top-9 text-blue-400"></i>
                                    </div>

                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fab fa-instagram mr-2 text-pink-500"></i>
                                            Instagram
                                        </label>
                                        <input type="url" name="social_links[instagram]"
                                            value="{{ old('social_links.instagram', $user->userProfile->social_links['instagram'] ?? '') }}"
                                            placeholder="https://instagram.com/tuoaccount"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                        <i class="fab fa-instagram absolute left-3 top-9 text-pink-500"></i>
                                    </div>

                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fab fa-youtube mr-2 text-red-600"></i>
                                            YouTube
                                        </label>
                                        <input type="url" name="social_links[youtube]"
                                            value="{{ old('social_links.youtube', $user->userProfile->social_links['youtube'] ?? '') }}"
                                            placeholder="https://youtube.com/@tuoaccount"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                        <i class="fab fa-youtube absolute left-3 top-9 text-red-600"></i>
                                    </div>

                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fab fa-tiktok mr-2 text-black"></i>
                                            TikTok
                                        </label>
                                        <input type="url" name="social_links[tiktok]"
                                            value="{{ old('social_links.tiktok', $user->userProfile->social_links['tiktok'] ?? '') }}"
                                            placeholder="https://tiktok.com/@tuoaccount"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
                                        <i class="fab fa-tiktok absolute left-3 top-9 text-black"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">

                        <!-- Channel Statistics -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-chart-bar text-red-600"></i>
                                    Statistiche canale
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Video pubblicati</span>
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white">{{ $user->videos()->published()->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Iscritti</span>
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white">{{ number_format($user->subscribers()->count()) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Visualizzazioni
                                        totali</span>
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white">{{ number_format($user->videos()->published()->sum('views_count')) }}</span>
                                </div>
                                @if ($user->userProfile && $user->userProfile->channel_created_at)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Canale creato</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ $user->userProfile->channel_created_at->format('M Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Channel Settings -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-cog text-red-600"></i>
                                    Impostazioni canale
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Canale attivo
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Il tuo canale è visibile agli altri utenti
                                        </p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="is_channel_enabled" value="1"
                                            {{ $user->userProfile && $user->userProfile->is_channel_enabled ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Verifica del canale
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Badge di verifica del canale
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($user->userProfile && $user->userProfile->is_verified)
                                            <div
                                                class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                            <span
                                                class="text-xs text-blue-600 dark:text-blue-400 font-medium">Verificato</span>
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Non
                                                verificato</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-bolt text-red-600"></i>
                                    Azioni rapide
                                </h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <a href="{{ route('channel.edit', Auth::user()->userProfile->channel_name) }}?tab=content&upload=true"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors font-medium">
                                    <i class="fas fa-upload"></i>
                                    Carica nuovo video
                                </a>
                                <a href="{{ route('channel.show', $user) }}"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium">
                                    <i class="fas fa-eye"></i>
                                    Visualizza canale
                                </a>
                                <a href="{{ route('analytics') }}"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium">
                                    <i class="fas fa-chart-line"></i>
                                    Analitiche
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden File Inputs -->
                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden">
                <input type="file" id="bannerInput" name="banner" accept="image/*" class="hidden">

                <!-- Submit Button -->
                <div class="fixed bottom-6 right-6 z-50">
                    <button type="submit"
                        class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl shadow-2xl hover:shadow-red-500/25 transition-all font-semibold flex items-center gap-2 transform hover:scale-105">
                        <i class="fas fa-save"></i>
                        Salva modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Character counter for description
        document.querySelector('textarea[name="channel_description"]').addEventListener('input', function(e) {
            const count = e.target.value.length;
            const counter = document.getElementById('descriptionCount');
            counter.textContent = count + '/500';

            if (count > 500) {
                counter.classList.add('text-red-600');
            } else {
                counter.classList.remove('text-red-600');
            }
        });

        // Avatar preview
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview) {
                        preview.src = event.target.result;
                    } else {
                        const avatarContainer = document.querySelector('.w-24.h-24, .w-28.h-28, .w-32.h-32');
                        if (avatarContainer) {
                            avatarContainer.innerHTML = `
                                <img src="${event.target.result}" alt="Avatar" 
                                     class="w-full h-full object-cover rounded-full">
                            `;
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Banner preview
        document.getElementById('bannerInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const bannerContainer = document.querySelector('.h-48, .h-64');
                    if (bannerContainer) {
                        bannerContainer.innerHTML = `
                            <img src="${event.target.result}" alt="Cover" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                <label for="bannerInput" class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Cambia cover</span>
                                </label>
                            </div>
                            <button type="button" onclick="removeBanner()" class="absolute top-3 right-3 w-9 h-9 bg-red-500/90 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all shadow-lg backdrop-blur-sm opacity-0 group-hover:opacity-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        `;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove avatar
        function removeAvatar() {
            if (confirm('Rimuovere la foto profilo?')) {
                const avatarContainer = document.querySelector('.w-24.h-24, .w-28.h-28, .w-32.h-32');
                if (avatarContainer) {
                    avatarContainer.innerHTML = `
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-500 to-red-600 rounded-full">
                            <span class="text-3xl md:text-4xl font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                            <label for="avatarInput" class="cursor-pointer flex flex-col items-center gap-1 p-3 text-white">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </label>
                        </div>
                    `;
                }

                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_avatar';
                removeInput.value = '1';
                document.getElementById('profileForm').appendChild(removeInput);
            }
        }

        // Remove banner
        function removeBanner() {
            if (confirm('Rimuovere la cover del canale?')) {
                const bannerContainer = document.querySelector('.h-48, .h-64');
                if (bannerContainer) {
                    bannerContainer.innerHTML = `
                        <div class="w-full h-full bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 flex items-center justify-center">
                            <div class="text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm font-medium">Cover del canale</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                            <label for="bannerInput" class="cursor-pointer flex flex-col items-center gap-2 px-6 py-4 bg-white/95 backdrop-blur-sm text-slate-800 rounded-xl hover:bg-white transition-all font-medium shadow-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Carica cover</span>
                            </label>
                        </div>
                    `;
                }

                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_banner';
                removeInput.value = '1';
                document.getElementById('profileForm').appendChild(removeInput);
            }
        }

        // Initialize character count
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.querySelector('textarea[name="channel_description"]');
            if (textarea) {
                const count = textarea.value.length;
                document.getElementById('descriptionCount').textContent = count + '/500';
            }
        });
    </script>
</x-layout>
