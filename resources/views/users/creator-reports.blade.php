<x-layout>
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <i class="fas fa-flag text-yellow-500"></i>
                    Le Mie Segnalazioni
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Visualizza le segnalazioni relative ai tuoi contenuti e i feedback degli amministratori
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('creator.reports', ['tab' => 'reports']) }}"
                    class="px-3 py-2 text-sm rounded-lg border {{ request('tab') !== 'feedback' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-700 dark:text-red-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <i class="fas fa-flag mr-1"></i> Segnalazioni
                    @if ($pendingReports > 0)
                        <span
                            class="ml-1 px-1.5 py-0.5 text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full">{{ $pendingReports }}</span>
                    @endif
                </a>
                <a href="{{ route('creator.reports', ['tab' => 'feedback']) }}"
                    class="px-3 py-2 text-sm rounded-lg border {{ request('tab') === 'feedback' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-700 dark:text-red-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <i class="fas fa-comment-dots mr-1"></i> Feedback
                    @if ($unreadFeedback > 0)
                        <span
                            class="ml-1 px-1.5 py-0.5 text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full">{{ $unreadFeedback }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Statistiche rapide -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide">
                            Segnalazioni Totali
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalReports }}</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flag text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">
                            In Attesa
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $pendingReports }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">
                            Feedback Ricevuti
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalFeedback ?? 0 }}</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comment-dots text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                            Non Letti
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $unreadFeedback }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenuto principale -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Header tabella -->
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    @if (request('tab') === 'feedback')
                        <i class="fas fa-comment-dots text-blue-500"></i> Feedback dagli Amministratori
                    @else
                        <i class="fas fa-flag text-yellow-500"></i> Segnalazioni sui Tuoi Contenuti
                    @endif
                </h2>
                @if (request('status'))
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>Filtrato per: <span class="font-medium capitalize">{{ request('status') }}</span></span>
                        <a href="{{ route('creator.reports', ['tab' => request('tab')]) }}"
                            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                @endif
            </div>

            <div class="p-6">
                @if (request('tab') !== 'feedback')
                    <!-- Tab Segnalazioni -->
                    @forelse($reports as $report)
                        <div
                            class="mb-4 p-4 rounded-xl border {{ $report->priority == 'urgent' ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10' : ($report->priority == 'high' ? 'border-yellow-300 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/10' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30') }}">
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Info principale -->
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $report->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                        {{ $report->status === 'resolved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                        {{ $report->status === 'dismissed' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                                        {{ $report->status === 'escalated' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}">
                                            <i class="fas fa-clock mr-1"></i>{{ $report->getStatusLabelAttribute() }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $report->priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                        {{ $report->priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : '' }}
                                        {{ $report->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                        {{ $report->priority === 'low' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}">
                                            <i
                                                class="fas fa-circle text-xs mr-1"></i>{{ $report->getPriorityLabelAttribute() }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ $report->getTypeLabelAttribute() }}
                                    </h3>

                                    <p class="text-gray-700 dark:text-gray-300 mb-2">
                                        <span class="font-medium">Motivo:</span> {{ $report->reason }}
                                    </p>

                                    @if ($report->description)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                                            <span class="font-medium">Dettagli:</span> {{ $report->description }}
                                        </p>
                                    @endif

                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-user"></i> Segnalato da: {{ $report->reporter->name }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-clock"></i> {{ $report->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Contenuto segnalato -->
                                    @if ($report->video)
                                        <div
                                            class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-1">
                                                <i class="fas fa-video mr-1"></i>Video segnalato:
                                            </p>
                                            <a href="{{ route('videos.show', $report->video) }}"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm"
                                                target="_blank">
                                                {{ $report->video->title }}
                                            </a>
                                        </div>
                                    @elseif($report->comment)
                                        <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                                <i class="fas fa-comment mr-1"></i>Commento segnalato:
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-400 text-sm italic mb-1">
                                                "{{ Str::limit($report->comment->content, 100) }}"</p>
                                            @if ($report->comment->video)
                                                <a href="{{ route('videos.show', $report->comment->video) }}"
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                                    Su: {{ $report->comment->video->title }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Sidebar -->
                                <div class="lg:w-64 flex flex-col gap-2">
                                    @if ($report->admin)
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                <i class="fas fa-user-check mr-1"></i>Assegnato a:
                                            </p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $report->admin->name }}</p>
                                        </div>
                                    @endif

                                    @if ($report->admin_notes)
                                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                <i class="fas fa-sticky-note mr-1"></i>Note amministratore:
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $report->admin_notes }}</p>
                                        </div>
                                    @endif

                                    <div class="mt-auto text-right">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">ID:
                                            #{{ $report->id }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <i class="fas fa-flag text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessuna segnalazione
                                trovata</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                @if (request('status'))
                                    Non ci sono segnalazioni con lo stato "{{ request('status') }}"
                                @else
                                    Non hai ancora ricevuto nessuna segnalazione sui tuoi contenuti
                                @endif
                            </p>
                        </div>
                    @endforelse

                    @if ($reports instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-6">
                            {{ $reports->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <!-- Tab Feedback -->
                    @forelse($feedback as $item)
                        <div
                            class="mb-4 p-4 rounded-xl border {{ !$item->is_read ? 'border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/10' : 'border-gray-200 dark:border-gray-700' }}">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-3">
                                <div>
                                    <h3
                                        class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-1">
                                        <i
                                            class="fas fa-{{ $item->type === 'report_resolution' ? 'check-circle text-green-500' : ($item->type === 'report_dismissed' ? 'times-circle text-gray-500' : ($item->type === 'report_escalated' ? 'exclamation-triangle text-red-500' : 'info-circle text-blue-500')) }}"></i>
                                        {{ $item->title }}
                                    </h3>
                                    <div
                                        class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-user"></i> Da: {{ $item->admin->name }}
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-clock"></i> {{ $item->created_at->diffForHumans() }}
                                        </span>
                                        @if (isset($item->type_color))
                                            <span
                                                class="px-2 py-0.5 text-xs rounded-full
                                            {{ $item->type === 'report_resolution' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                            {{ $item->type === 'report_dismissed' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                                            {{ $item->type === 'report_escalated' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                            {{ $item->type === 'general' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}">
                                                {{ $item->type_label ?? 'Feedback' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if (!$item->is_read)
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full whitespace-nowrap">
                                        Nuovo
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3 text-gray-700 dark:text-gray-300">
                                {{ $item->message }}
                            </div>

                            @if ($item->report)
                                <div
                                    class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mb-3">
                                    <p class="text-sm text-blue-800 dark:text-blue-300 mb-1">
                                        <i class="fas fa-link mr-1"></i>
                                        <span class="font-medium">Relativo alla segnalazione
                                            #{{ $item->report->id }}</span>
                                    </p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">
                                        Tipo: {{ $item->report->getTypeLabelAttribute() }} • Stato:
                                        {{ $item->report->getStatusLabelAttribute() }}
                                    </p>
                                </div>
                            @endif

                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    @if ($item->is_read)
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-check text-green-500"></i>
                                            Letto il {{ $item->read_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-envelope text-gray-500"></i>
                                            Non letto
                                        </span>
                                    @endif
                                </div>
                                @if (!$item->is_read)
                                    <button onclick="markAsRead({{ $item->id }})"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/40 rounded-lg transition-colors">
                                        <i class="fas fa-check"></i> Segna come letto
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <i class="fas fa-comment-dots text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun feedback ricevuto
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Non hai ancora ricevuto feedback dagli amministratori.
                            </p>
                        </div>
                    @endforelse

                    @if ($unreadFeedback > 0)
                        <div class="mt-4 text-center">
                            <button onclick="markAllAsRead()"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg transition-colors">
                                <i class="fas fa-check-double"></i> Segna tutto come letto
                            </button>
                        </div>
                    @endif

                    @if ($feedback instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-6">
                            {{ $feedback->appends(request()->query())->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Informazioni aggiuntive -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div
                class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800 rounded-t-xl">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Informazioni sulle Segnalazioni
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-flag text-yellow-500"></i> Come funzionano
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Quando qualcuno segnala i tuoi contenuti, riceverai una notifica. Gli amministratori
                            esamineranno la segnalazione e ti contatteranno con aggiornamenti.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-green-500"></i> I tuoi diritti
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Hai il diritto di sapere perché i tuoi contenuti sono stati segnalati e di ricevere
                            spiegazioni sulle decisioni degli amministratori.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-question-circle text-blue-500"></i> Hai domande?
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Se hai domande sulle segnalazioni ricevute o vuoi contestare una decisione, contatta il
                            nostro team di supporto.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestione lettura feedback
            window.markAsRead = function(feedbackId) {
                fetch(`/creator/feedback/${feedbackId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            };

            window.markAllAsRead = function() {
                if (confirm('Sei sicuro di voler segnare tutti i feedback come letti?')) {
                    fetch('/creator/feedback/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            };

            // Auto-refresh ogni 30 secondi se ci sono segnalazioni in attesa
            @if ($pendingReports > 0)
                setInterval(function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const tab = urlParams.get('tab');
                    if (tab !== 'feedback') {
                        location.reload();
                    }
                }, 30000);
            @endif
        });
    </script>
</x-layout>
