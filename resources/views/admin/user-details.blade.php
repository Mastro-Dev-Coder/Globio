<x-admin-layout>
    @php
        $pageHeader = [
            'title' => 'Dettagli Utente',
            'subtitle' => 'Informazioni complete e statistiche dell\'utente ' . $user->name,
            'actions' => '<div class="flex items-center space-x-3">
                <a href="' . route('admin.users.edit', $user) . '" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Modifica
                </a>
                <a href="' . route('admin.users') . '" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Torna alla Lista
                </a>
            </div>'
        ];
    @endphp

    <div class="space-y-6">
        <!-- User Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-start space-x-6">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                <i class="fas fa-check-circle mr-1"></i>
                                Verificato
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                            @elseif($user->role === 'moderator') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                            {{ ucfirst($user->role ?? 'user') }}
                        </span>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $user->email }}</p>
                    
                    @if($user->userProfile && $user->userProfile->channel_name)
                        <p class="text-gray-800 dark:text-gray-200 mb-2">
                            <strong>Canale:</strong> {{ $user->userProfile->channel_name }}
                        </p>
                    @endif
                    
                    @if($user->userProfile && $user->userProfile->channel_description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ $user->userProfile->channel_description }}
                        </p>
                    @endif
                    
                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <span>
                            <i class="fas fa-calendar mr-1"></i>
                            Registrato il {{ $user->created_at->format('d/m/Y') }}
                        </span>
                        <span>
                            <i class="fas fa-clock mr-1"></i>
                            {{ $user->created_at->diffForHumans() }}
                        </span>
                        @if($user->email_verified_at)
                            <span>
                                <i class="fas fa-check-circle mr-1"></i>
                                Email verificata
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <i class="fas fa-video text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Video Totali</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['videos_count'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pubblicati</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['published_videos'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <i class="fas fa-eye text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Visualizzazioni Totali</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_views']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Iscritti</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['subscribers_count']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Videos -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Video Recenti</h3>
            </div>
            
            <div class="p-6">
                @if($user->videos->count() > 0)
                    <div class="space-y-4">
                        @foreach($user->videos()->latest()->take(5)->get() as $video)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="w-20 h-12 bg-gray-200 dark:bg-gray-600 rounded overflow-hidden flex-shrink-0">
                                    @if ($video->thumbnail_path)
                                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-video text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $video->title }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($video->views_count) }} visualizzazioni • {{ $video->created_at->diffForHumans() }}
                                    </p>
                                    <div class="flex items-center mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($video->status === 'published') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                            @elseif($video->status === 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                            @elseif($video->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ ucfirst($video->status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @if($video->status === 'published')
                                        <a href="{{ route('videos.show', $video) }}" target="_blank"
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                           title="Visualizza video">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($user->videos->count() > 5)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                E altri {{ $user->videos->count() - 5 }} video...
                            </p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-video text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Nessun video caricato</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Commenti Recenti</h3>
            </div>
            
            <div class="p-6">
                @if($user->comments->count() > 0)
                    <div class="space-y-4">
                        @foreach($user->comments()->latest()->take(5)->get() as $comment)
                            <div class="border-l-4 border-gray-200 dark:border-gray-600 pl-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900 dark:text-white line-clamp-2">
                                            {{ $comment->content }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Su "{{ Str::limit($comment->video->title, 30) }}" • {{ $comment->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if($comment->video && $comment->video->status === 'published')
                                        <a href="{{ route('videos.show', $comment->video) }}" target="_blank"
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 ml-3">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($user->comments->count() > 5)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                E altri {{ $user->comments->count() - 5 }} commenti...
                            </p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-comments text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Nessun commento scritto</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- User Profile Details -->
        @if($user->userProfile)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informazioni Profilo</h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Nome Canale</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->userProfile->channel_name ?: 'Non specificato' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Stato Email</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->email_verified_at) bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                @if($user->email_verified_at) Verificato @else Non verificato @endif
                            </span>
                        </div>
                        
                        @if($user->userProfile->channel_description)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Descrizione</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->userProfile->channel_description }}</p>
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Data Creazione Profilo</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->userProfile->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>