<x-admin-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-flag mr-2 text-red-600"></i>
                    Segnalazione #{{ $report->id }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">Dashboard</a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-400"></i></li>
                        <li>
                            <a href="{{ route('admin.reports') }}" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">Segnalazioni</a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-400"></i></li>
                        <li class="text-gray-900 dark:text-gray-100">Segnalazione #{{ $report->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i> Torna alla lista
                </a>
                <a href="{{ route('admin.reports.create-notification') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-bell"></i> Invia Segnalazione a Creator
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Dettagli principali -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informazioni di base -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-info-circle text-red-500"></i> Informazioni Generali
                        </h2>
                        <div class="flex gap-2">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full {{ $report->getStatusColorAttribute() === 'primary' ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400' : ($report->getStatusColorAttribute() === 'success' ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400' : ($report->getStatusColorAttribute() === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400' : ($report->getStatusColorAttribute() === 'danger' ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'))) }}">
                                {{ $report->getStatusLabelAttribute() }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full {{ $report->getPriorityColorAttribute() === 'danger' ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' : ($report->getPriorityColorAttribute() === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400' : 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400') }}">
                                {{ $report->getPriorityLabelAttribute() }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo di Segnalazione</label>
                                <p class="text-red-600 dark:text-red-400 font-medium">{{ $report->getTypeLabelAttribute() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priorità</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->getPriorityColorAttribute() === 'danger' ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' : ($report->getPriorityColorAttribute() === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400' : 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400') }}">
                                    {{ $report->getPriorityLabelAttribute() }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo</label>
                            <div class="bg-gray-50 dark:bg-gray-700/50 border-l-4 border-red-500 p-4 rounded-lg">
                                <p class="text-gray-900 dark:text-gray-100">{{ $report->reason }}</p>
                            </div>
                        </div>

                        @if ($report->description)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descrizione Aggiuntiva</label>
                                <div class="bg-gray-50 dark:bg-gray-700/50 border-l-4 border-blue-500 p-4 rounded-lg">
                                    <p class="text-gray-900 dark:text-gray-100">{{ $report->description }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Creazione</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $report->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            @if ($report->resolved_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Risoluzione</label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $report->resolved_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contenuto segnalato -->
                @if ($report->video)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-video text-red-500"></i> Video Segnalato
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    @if ($report->video->thumbnail)
                                        <img src="{{ asset('storage/' . $report->video->thumbnail) }}"
                                            class="w-full rounded-lg" alt="Thumbnail">
                                    @else
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center" style="height: 150px;">
                                            <i class="fas fa-video text-4xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">{{ $report->video->title }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $report->video->description }}</p>
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-eye"></i> {{ number_format($report->video->views) }} visualizzazioni
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-clock"></i> {{ $report->video->duration ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ route('videos.show', $report->video) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Visualizza video
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($report->comment)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-comment text-blue-500"></i> Commento Segnalato
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <strong class="text-blue-800 dark:text-blue-300">Contenuto del commento:</strong>
                                <p class="mt-2 text-gray-900 dark:text-gray-100">{{ $report->comment->content }}</p>
                            </div>
                            @if ($report->comment->parent_id)
                                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <strong class="text-gray-700 dark:text-gray-300">In risposta a:</strong>
                                    <p class="mt-2 text-gray-900 dark:text-gray-100">{{ $report->comment->parent->content }}</p>
                                </div>
                            @endif
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-thumbs-up"></i> {{ $report->comment->likes_count ?? 0 }} like
                                    </small>
                                </div>
                                <div>
                                    <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-clock"></i> {{ $report->comment->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($report->channel)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-tv text-purple-500"></i> Canale Segnalato
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    @if ($report->channel->userProfile && $report->channel->userProfile->avatar)
                                        <img src="{{ asset('storage/' . $report->channel->userProfile->avatar) }}"
                                            class="w-24 h-24 rounded-full mx-auto" alt="Avatar canale">
                                    @else
                                        <div class="w-24 h-24 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center mx-auto">
                                            <i class="fas fa-tv text-4xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ $report->channel->userProfile->channel_name ?? $report->channel->name }}
                                    </h3>
                                    @if($report->channel->userProfile && $report->channel->userProfile->bio)
                                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $report->channel->userProfile->bio }}</p>
                                    @endif
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-user"></i> {{ $report->channel->name }}
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-calendar"></i> Creato il {{ $report->channel->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ route('channels.show', $report->channel->userProfile->slug ?? $report->channel->id) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-purple-300 dark:border-purple-600 text-purple-700 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Visualizza canale
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Azioni amministrative -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-tools text-yellow-500"></i> Azioni Amministrative
                        </h2>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                            @csrf

                            <div class="mb-6">
                                <label for="resolution_action" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Azione di Risoluzione</label>
                                <select name="resolution_action" id="resolution_action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                                    <option value="">Seleziona un'azione...</option>
                                    <option value="{{ \App\Models\Report::ACTION_CONTENT_REMOVED }}">Rimuovi contenuto</option>
                                    <option value="{{ \App\Models\Report::ACTION_USER_WARNED }}">Ammonisci utente</option>
                                    <option value="{{ \App\Models\Report::ACTION_USER_SUSPENDED }}">Sospendi utente</option>
                                    <option value="{{ \App\Models\Report::ACTION_USER_BANNED }}">Banna utente</option>
                                    <option value="{{ \App\Models\Report::ACTION_FALSE_REPORT }}">Segnalazione falsa</option>
                                    <option value="{{ \App\Models\Report::ACTION_NO_ACTION }}">Nessuna azione</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note Amministrative</label>
                                <textarea name="admin_notes" id="admin_notes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="3"
                                    placeholder="Note interne per altri amministratori..."></textarea>
                            </div>

                            <div class="mb-6">
                                <div class="flex items-start">
                                    <input class="h-4 w-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500 dark:bg-gray-700 mt-1" type="checkbox" name="send_feedback" id="send_feedback" value="1">
                                    <label class="ml-3" for="send_feedback">
                                        <strong class="block text-gray-900 dark:text-white">Invia feedback al creator</strong>
                                        <small class="block text-gray-600 dark:text-gray-400">Il creator riceverà una notifica con aggiornamenti sulla segnalazione</small>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-6 hidden" id="feedback_message_group">
                                <label for="feedback_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Messaggio per il creator</label>
                                <textarea name="feedback_message" id="feedback_message" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="3"
                                    placeholder="Messaggio personalizzato per il creator..." maxlength="500"></textarea>
                                <small class="text-gray-500 dark:text-gray-400">Caratteri rimanenti: <span id="char_count">500</span></small>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-check"></i> Risolvi Segnalazione
                                </button>
                                <button type="button" class="inline-flex items-center gap-2 px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors" onclick="showDismissModal()">
                                    <i class="fas fa-times"></i> Respingi
                                </button>
                                <button type="button" class="inline-flex items-center gap-2 px-6 py-2 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg transition-colors" onclick="showEscalateModal()">
                                    <i class="fas fa-exclamation-triangle"></i> Escalazione
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Note amministrative -->
                @if ($report->admin_notes)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-sticky-note text-gray-500"></i> Note Amministrative
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="bg-gray-50 dark:bg-gray-700/50 border-l-4 border-gray-400 p-4 rounded-lg mb-4">
                                <p class="text-gray-900 dark:text-gray-100">{{ $report->admin_notes }}</p>
                            </div>
                            @if ($report->admin)
                                <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i class="fas fa-user"></i> Aggiunto da: {{ $report->admin->name }} il {{ $report->updated_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Utenti coinvolti -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-users text-blue-500"></i> Utenti Coinvolti
                        </h2>
                    </div>
                    <div class="p-6">
                        <!-- Segnalatore -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-green-600 dark:text-green-400 flex items-center gap-2 mb-3">
                                <i class="fas fa-user-plus"></i> Segnalato da
                            </h3>
                            <div class="flex items-center">
                                @if ($report->reporter->userProfile->avatar)
                                    <img src="{{ asset('storage/' . $report->reporter->userProfile->avatar) }}"
                                        class="w-12 h-12 rounded-full mr-3">
                                @endif
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $report->reporter->name }}</h4>
                                    <small class="text-gray-500 dark:text-gray-400 block">{{ $report->reporter->email }}</small>
                                    <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                        <i class="fas fa-clock"></i> {{ $report->reporter->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Utente segnalato -->
                        @if ($report->reportedUser)
                            <div>
                                <h3 class="font-semibold text-red-600 dark:text-red-400 flex items-center gap-2 mb-3">
                                    <i class="fas fa-user-times"></i> Segnalato
                                </h3>
                                <div class="flex items-center">
                                    @if ($report->reportedUser->userProfile->avatar)
                                        <img src="{{ asset('storage/' . $report->reportedUser->userProfile->avatar) }}"
                                            class="w-12 h-12 rounded-full mr-3">
                                    @endif
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $report->reportedUser->name }}</h4>
                                        <small class="text-gray-500 dark:text-gray-400 block">{{ $report->reportedUser->email }}</small>
                                        <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                            <i class="fas fa-clock"></i> {{ $report->reportedUser->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('admin.users.show', $report->reportedUser) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <i class="fas fa-eye"></i> Profilo utente
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assegnazione -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-user-check text-blue-500"></i> Assegnazione
                        </h2>
                    </div>
                    <div class="p-6">
                        @if ($report->admin)
                            <div class="flex items-center">
                                @if ($report->admin->userProfile->avatar)
                                    <img src="{{ asset('storage/' . $report->admin->userProfile->avatar) }}"
                                        class="w-10 h-10 rounded-full mr-3">
                                @endif
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $report->admin->name }}</h4>
                                    <small class="text-gray-500 dark:text-gray-400">Assegnato il {{ $report->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Non assegnato</p>
                            <form method="POST" action="{{ route('admin.reports.assign', $report) }}">
                                @csrf
                                <div class="mb-4">
                                    <select name="admin_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                                        <option value="">Assegna a...</option>
                                        @foreach (\App\Models\User::where('role', 'admin')->get() as $admin)
                                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-user-plus"></i> Assegna
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Statistiche -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-chart-bar text-gray-500"></i> Statistiche
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="border-r border-gray-200 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-red-600 dark:text-red-400">
                                    {{ \App\Models\Report::where('reporter_id', $report->reporter_id)->count() }}
                                </h3>
                                <small class="text-gray-500 dark:text-gray-400 block">Segnalazioni<br>inviate</small>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ \App\Models\Report::where('reported_user_id', $report->reported_user_id)->count() }}
                                </h3>
                                <small class="text-gray-500 dark:text-gray-400 block">Segnalazioni<br>ricevute</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per respingere segnalazione -->
    <div class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="dismissModal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white">Respingi Segnalazione</h5>
                    <button onclick="hideDismissModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                    @csrf
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">Sei sicuro di voler respingere questa segnalazione?</p>
                        <div class="mb-4">
                            <label for="dismiss_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note (opzionale)</label>
                            <textarea name="admin_notes" id="dismiss_notes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="3"></textarea>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-start">
                                <input class="h-4 w-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500 dark:bg-gray-700 mt-1" type="checkbox" name="send_feedback" id="dismiss_send_feedback" value="1">
                                <label class="ml-3 text-sm text-gray-700 dark:text-gray-300" for="dismiss_send_feedback">
                                    Invia notifica al creator
                                </label>
                            </div>
                        </div>
                        <div class="mb-4 hidden" id="dismiss_feedback_message_group">
                            <label for="dismiss_feedback_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Messaggio per il creator</label>
                            <textarea name="feedback_message" id="dismiss_feedback_message" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors" onclick="hideDismissModal()">Annulla</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Respingi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal per escalazione -->
    <div class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="escalateModal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white">Escalazione Segnalazione</h5>
                    <button onclick="hideEscalateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.reports.escalate', $report) }}">
                    @csrf
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">Sei sicuro di voler escalare questa segnalazione?</p>
                        <div class="mb-4">
                            <label for="escalate_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo dell'escalazione <span class="text-red-600 dark:text-red-400">*</span></label>
                            <textarea name="admin_notes" id="escalate_notes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="3" required></textarea>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-start">
                                <input class="h-4 w-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500 dark:bg-gray-700 mt-1" type="checkbox" name="send_feedback" id="escalate_send_feedback" value="1">
                                <label class="ml-3 text-sm text-gray-700 dark:text-gray-300" for="escalate_send_feedback">
                                    Invia notifica al creator
                                </label>
                            </div>
                        </div>
                        <div class="mb-4 hidden" id="escalate_feedback_message_group">
                            <label for="escalate_feedback_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Messaggio per il creator</label>
                            <textarea name="feedback_message" id="escalate_feedback_message" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors" onclick="hideEscalateModal()">Annulla</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Escalazione</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestione feedback message
            const sendFeedback = document.getElementById('send_feedback');
            const feedbackMessageGroup = document.getElementById('feedback_message_group');
            const feedbackMessage = document.getElementById('feedback_message');
            const charCount = document.getElementById('char_count');

            sendFeedback.addEventListener('change', function() {
                if (this.checked) {
                    feedbackMessageGroup.classList.remove('hidden');
                    feedbackMessage.focus();
                } else {
                    feedbackMessageGroup.classList.add('hidden');
                }
            });

            feedbackMessage.addEventListener('input', function() {
                const remaining = 500 - this.value.length;
                charCount.textContent = remaining;
                charCount.className = remaining < 50 ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400';
            });

            // Gestione modal dismiss
            const dismissSendFeedback = document.getElementById('dismiss_send_feedback');
            const dismissFeedbackGroup = document.getElementById('dismiss_feedback_message_group');

            dismissSendFeedback.addEventListener('change', function() {
                dismissFeedbackGroup.classList.toggle('hidden', !this.checked);
            });

            // Gestione modal escalate
            const escalateSendFeedback = document.getElementById('escalate_send_feedback');
            const escalateFeedbackGroup = document.getElementById('escalate_feedback_message_group');

            escalateSendFeedback.addEventListener('change', function() {
                escalateFeedbackGroup.classList.toggle('hidden', !this.checked);
            });
        });

        function showDismissModal() {
            document.getElementById('dismissModal').classList.remove('hidden');
        }

        function hideDismissModal() {
            document.getElementById('dismissModal').classList.add('hidden');
        }

        function showEscalateModal() {
            document.getElementById('escalateModal').classList.remove('hidden');
        }

        function hideEscalateModal() {
            document.getElementById('escalateModal').classList.add('hidden');
        }
    </script>
</x-admin-layout>