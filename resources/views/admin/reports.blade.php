<x-admin-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-flag mr-3 text-red-600"></i>Gestione Segnalazioni
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Dashboard completa per gestire segnalazioni, moderazione contenuti e analytics di compliance
            </p>
        </div>

        <!-- Filtri e controlli -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" onchange="updateReports()"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>In Attesa</option>
                            <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Risolte</option>
                            <option value="dismissed" {{ $status === 'dismissed' ? 'selected' : '' }}>Respinte</option>
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tutte</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priorità</label>
                        <select name="priority" onchange="updateReports()"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="">Tutte</option>
                            <option value="urgent" {{ $priority === 'urgent' ? 'selected' : '' }}>Urgente</option>
                            <option value="high" {{ $priority === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="medium" {{ $priority === 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="low" {{ $priority === 'low' ? 'selected' : '' }}>Bassa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                        <select name="type" onchange="updateReports()"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="">Tutti</option>
                            <option value="spam" {{ $type === 'spam' ? 'selected' : '' }}>Spam</option>
                            <option value="harassment" {{ $type === 'harassment' ? 'selected' : '' }}>Molestie</option>
                            <option value="copyright" {{ $type === 'copyright' ? 'selected' : '' }}>Copyright</option>
                            <option value="inappropriate_content"
                                {{ $type === 'inappropriate_content' ? 'selected' : '' }}>Contenuto Inappropriato
                            </option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.export') }}?{{ http_build_query(request()->all()) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm inline-flex items-center">
                        <i class="fas fa-download mr-2"></i>Esporta
                    </a>
                    <button onclick="openBulkActionModal()"
                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors text-sm inline-flex items-center">
                        <i class="fas fa-tasks mr-2"></i>Azioni Bulk
                    </button>
                    <form method="GET" action="" class="inline">
                        @foreach(request()->except('page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors text-sm inline-flex items-center">
                            <i class="fas fa-sync-alt mr-2"></i>Aggiorna
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistiche principali -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Segnalazioni in Sospeso -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <span class="text-red-600 dark:text-red-400 text-sm font-medium">
                        +{{ $reportStats['pending_growth'] ?? 0 }}
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ $reportStats['pending'] ?? 0 }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Segnalazioni in attesa
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full"
                        style="width: {{ min(100, (($reportStats['pending'] ?? 0) / 100) * 100) }}%"></div>
                </div>
            </div>

            <!-- Tempo Medio Risoluzione -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-stopwatch text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                        -{{ $reportStats['resolution_improvement'] ?? 0 }}%
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ $reportStats['avg_resolution_time'] ?? 0 }}h
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tempo medio risoluzione
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full"
                        style="width: {{ min(100, 100 - (($reportStats['avg_resolution_time'] ?? 0) / 24) * 100) }}%">
                    </div>
                </div>
            </div>

            <!-- Tasso di Precisione -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                        +{{ $reportStats['accuracy_improvement'] ?? 0 }}%
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($reportStats['accuracy_rate'] ?? 0, 1) }}%
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tasso di precisione
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full"
                        style="width: {{ $reportStats['accuracy_rate'] ?? 0 }}%"></div>
                </div>
            </div>

            <!-- Segnalazioni False -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <span class="text-red-600 dark:text-red-400 text-sm font-medium">
                        {{ $reportStats['false_reports_rate'] ?? 0 }}%
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ $reportStats['false_reports'] ?? 0 }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Segnalazioni false
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full"
                        style="width: {{ min(100, (($reportStats['false_reports'] ?? 0) / ($reportStats['total'] ?? 1)) * 100) }}%">
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafici e analisi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Trend segnalazioni -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-line mr-2 text-blue-500"></i>
                        Trend Segnalazioni
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative h-80">
                        <canvas id="reportsTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribuzione per tipo -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                        Distribuzione per Tipo
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative h-80">
                        <canvas id="reportTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance del team -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-users mr-2 text-green-500"></i>
                    Performance del Team di Moderazione
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($moderationTeamStats ?? [] as $admin)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $admin->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Moderatore</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Risolte:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $admin->resolved_count }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Tempo medio:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $admin->avg_resolution_time }}h</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Precisione:</span>
                                    <span
                                        class="text-sm font-medium text-green-600 dark:text-green-400">{{ number_format($admin->accuracy_rate, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Lista segnalazioni -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-list mr-2 text-gray-500"></i>
                    Lista Segnalazioni
                </h2>
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                            class="rounded border-gray-300 dark:border-gray-600 text-red-600 focus:ring-red-500">
                        Seleziona tutto
                    </label>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12">
                                <input type="checkbox" id="selectAllTop" onchange="toggleSelectAll()"
                                    class="rounded border-gray-300 dark:border-gray-600 text-red-600 focus:ring-red-500">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Segnalante</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Target</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tipo</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Priorità</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Assegnato</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Data</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($reports ?? [] as $report)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $report->id }}"
                                        class="report-checkbox rounded border-gray-300 dark:border-gray-600 text-red-600 focus:ring-red-500">
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ $report->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-400 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $report->reporter->name ?? 'Utente eliminato' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($report->video)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-video text-blue-500 text-sm"></i>
                                            <span class="text-sm text-gray-900 dark:text-white">Video</span>
                                        </div>
                                    @elseif ($report->comment)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-comment text-green-500 text-sm"></i>
                                            <span class="text-sm text-gray-900 dark:text-white">Commento</span>
                                        </div>
                                    @elseif ($report->reportedUser)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-user text-purple-500 text-sm"></i>
                                            <span class="text-sm text-gray-900 dark:text-white">Utente</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $report->type === 'spam' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                        {{ $report->type === 'harassment' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : '' }}
                                        {{ $report->type === 'copyright' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : '' }}
                                        {{ $report->type === 'inappropriate_content' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                    ">
                                        @if ($report->type === 'spam')
                                            <i class="fas fa-spam mr-1"></i>Spam
                                        @elseif ($report->type === 'harassment')
                                            <i class="fas fa-bullhorn mr-1"></i>Molestie
                                        @elseif ($report->type === 'copyright')
                                            <i class="fas fa-copyright mr-1"></i>Copyright
                                        @elseif ($report->type === 'inappropriate_content')
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Inappropriato
                                        @else
                                            {{ $report->type }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $report->priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                        {{ $report->priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : '' }}
                                        {{ $report->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                        {{ $report->priority === 'low' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                    ">
                                        <i
                                            class="fas fa-circle text-xs mr-1
                                            {{ $report->priority === 'urgent' ? 'text-red-500' : '' }}
                                            {{ $report->priority === 'high' ? 'text-orange-500' : '' }}
                                            {{ $report->priority === 'medium' ? 'text-yellow-500' : '' }}
                                            {{ $report->priority === 'low' ? 'text-green-500' : '' }}
                                        "></i>
                                        {{ ucfirst($report->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $report->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                        {{ $report->status === 'resolved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                        {{ $report->status === 'dismissed' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                                        {{ $report->status === 'escalated' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                        {{ $report->status === 'reviewed' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                                    ">
                                        @if ($report->status === 'pending')
                                            <i class="fas fa-clock mr-1"></i>In Attesa
                                        @elseif ($report->status === 'resolved')
                                            <i class="fas fa-check mr-1"></i>Risolta
                                        @elseif ($report->status === 'dismissed')
                                            <i class="fas fa-times mr-1"></i>Respinta
                                        @elseif ($report->status === 'escalated')
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Escalata
                                        @elseif ($report->status === 'reviewed')
                                            <i class="fas fa-eye mr-1"></i>In Revisione
                                        @else
                                            {{ $report->status }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $report->admin->name ?? 'Non assegnato' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $report->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.reports.show', $report) }}"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                            title="Visualizza dettagli">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($report->status === 'pending' && !$report->admin_id)
                                            <form action="{{ route('admin.reports.assign', $report) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
                                                <button type="submit"
                                                    class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300"
                                                    title="Assegna a me">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($report->status === 'pending')
                                            <form action="{{ route('admin.reports.resolve', $report) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Sei sicuro di voler risolvere questa segnalazione?');">
                                                @csrf
                                                <input type="hidden" name="resolution_action"
                                                    value="{{ \App\Models\Report::ACTION_NO_ACTION }}">
                                                <button type="submit"
                                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300"
                                                    title="Risolvi">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reports.dismiss', $report) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Sei sicuro di voler respingere questa segnalazione?');">
                                                @csrf
                                                <button type="submit"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                    title="Respingi">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p>Nessuna segnalazione trovata con i filtri selezionati</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginazione -->
            @if (isset($reports) && $reports instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Reports Trend Chart
                const trendCtx = document.getElementById('reportsTrendChart').getContext('2d');
                const trendData = @json($reportTrends ?? []);

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(item => item.date),
                        datasets: [{
                                label: 'Segnalazioni',
                                data: trendData.map(item => item.total),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Risolte',
                                data: trendData.map(item => item.resolved),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            }
                        }
                    }
                });

                // Report Type Distribution Chart
                const typeCtx = document.getElementById('reportTypeChart').getContext('2d');
                const typeData = @json($reportTypeDistribution ?? []);

                new Chart(typeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Spam', 'Molestie', 'Copyright', 'Inappropriato', 'Altro'],
                        datasets: [{
                            data: [
                                typeData.spam ?? 0,
                                typeData.harassment ?? 0,
                                typeData.copyright ?? 0,
                                typeData.inappropriate_content ?? 0,
                                typeData.other ?? 0
                            ],
                            backgroundColor: [
                                '#ef4444',
                                '#f97316',
                                '#eab308',
                                '#22c55e',
                                '#6b7280'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            });

            // Functions for interactive controls
            function updateReports() {
                const status = document.querySelector('select[name="status"]').value;
                const priority = document.querySelector('select[name="priority"]').value;
                const type = document.querySelector('select[name="type"]').value;

                const params = new URLSearchParams();
                if (status) params.append('status', status);
                if (priority) params.append('priority', priority);
                if (type) params.append('type', type);

                window.location.href = '?' + params.toString();
            }

            function toggleSelectAll() {
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.report-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            }

            function openBulkActionModal() {
                const selected = document.querySelectorAll('.report-checkbox:checked');
                if (selected.length === 0) {
                    alert('Seleziona almeno una segnalazione');
                    return;
                }

                // Rimuovi eventuali input precedenti
                const existingInputs = document.querySelectorAll('#bulkActionForm input[name="report_ids[]"]');
                existingInputs.forEach(input => input.remove());

                // Aggiungi i report_ids al form
                selected.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'report_ids[]';
                    input.value = checkbox.value;
                    document.getElementById('bulkActionForm').appendChild(input);
                });

                // Mostra il modal
                document.getElementById('bulkActionModal').classList.remove('hidden');
            }

            function closeBulkActionModal() {
                document.getElementById('bulkActionModal').classList.add('hidden');
            }

            function executeBulkAction(action) {
                document.getElementById('bulkActionForm').action = '{{ route('admin.reports.bulk-action') }}';
                document.getElementById('bulkActionType').value = action;
                document.getElementById('bulkActionForm').submit();
            }
        </script>
    @endpush

    <!-- Bulk Action Modal -->
    <div class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="bulkActionModal">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-tasks mr-2 text-yellow-500"></i>Azioni Bulk
                    </h5>
                    <button onclick="closeBulkActionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Seleziona l'azione da eseguire sulle segnalazioni selezionate:
                </p>
                <div class="space-y-3">
                    <button onclick="executeBulkAction('assign')"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg transition-colors text-left">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-plus text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Assegna a me</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Assegna le segnalazioni selezionate al tuo account</div>
                        </div>
                    </button>
                    <button onclick="executeBulkAction('resolve')"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/40 rounded-lg transition-colors text-left">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Risolvi</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Segna le segnalazioni come risolte</div>
                        </div>
                    </button>
                    <button onclick="executeBulkAction('escalate')"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg transition-colors text-left">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Escalata</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Escala le segnalazioni a un livello superiore</div>
                        </div>
                    </button>
                    <button onclick="executeBulkAction('dismiss')"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors text-left">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Respingi</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Respingi le segnalazioni selezionate</div>
                        </div>
                    </button>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button onclick="closeBulkActionModal()"
                        class="w-full px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                        Annulla
                    </button>
                </div>
            </div>
        </div>
    <!-- Bulk Action Form nascosto -->
    <form id="bulkActionForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="action" id="bulkActionType" value="">
    </form>
</x-admin-layout>
