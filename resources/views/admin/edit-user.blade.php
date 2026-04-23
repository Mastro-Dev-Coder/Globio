<x-admin-layout>
    @php
        $pageHeader = [
            'title' => 'Modifica Utente',
            'subtitle' => 'Modifica le informazioni dell\'utente ' . $user->name,
            'actions' =>
                '<div class="flex items-center space-x-3">
                <a href="' .
                route('admin.users.show', $user) .
                '" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>Visualizza
                </a>
                <a href="' .
                route('admin.users') .
                '" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Torna alla Lista
                </a>
            </div>',
        ];
    @endphp

    <div class="max-w-4xl mx-auto space-y-8">
        <!-- User Basic Info Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user mr-3 text-gray-500"></i>
                    Informazioni Base
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- User Avatar and Basic Info -->
                <div class="flex items-center space-x-6 mb-6">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        @if ($user->userprofile->avatar_url)
                            <img src="{{ asset('storage/' . $user->userprofile->avatar_url) }}"
                                alt="{{ $user->name }}">
                        @else
                            <span class="text-white text-xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->name }}</h4>
                        <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Registrato il
                            {{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome Completo *
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email *
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                        @error('email')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Ruolo
                        </label>
                        <select id="role" name="role"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                            <option value="user" {{ ($user->role ?? 'user') === 'user' ? 'selected' : '' }}>Utente
                            </option>
                            <option value="moderator" {{ ($user->role ?? 'user') === 'moderator' ? 'selected' : '' }}>
                                Moderatore</option>
                            <option value="admin" {{ ($user->role ?? 'user') === 'admin' ? 'selected' : '' }}>
                                Amministratore</option>
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ruolo dell'utente nel sistema</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Salva Modifiche
                    </button>
                </div>
            </form>
        </div>

        <!-- Channel Profile Form -->
        @if ($user->userProfile || true)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-broadcast-tower mr-3 text-gray-500"></i>
                        Informazioni Canale
                    </h3>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="channel_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome Canale
                        </label>
                        <input type="text" id="channel_name" name="channel_name"
                            value="{{ old('channel_name', $user->userProfile?->channel_name ?? '') }}"
                            placeholder="Nome del canale"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nome visualizzato del canale
                            dell'utente</p>
                    </div>

                    <div>
                        <label for="channel_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrizione Canale
                        </label>
                        <textarea id="channel_description" name="channel_description" rows="4" placeholder="Descrizione del canale..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">{{ old('channel_description', $user->userProfile?->channel_description ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Descrizione che appare sul profilo
                            dell'utente</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Salva Profilo
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Danger Zone -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-l-4 border-red-500">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    Zona Pericolosa
                </h3>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Elimina Utente</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Una volta eliminato, l'utente e tutti i suoi dati non potranno essere recuperati.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Sei sicuro di voler eliminare questo utente? Questa azione è irreversibile!')"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Elimina Utente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
