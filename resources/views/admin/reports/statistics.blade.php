<x-admin-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-chart-bar mr-2 text-red-600"></i>
                    Statistiche Segnalazioni
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
                        <li class="text-gray-900 dark:text-gray-100">Statistiche</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i> Torna alla lista
                </a>
            </div>
        </div>

        <!-- Filtri periodo -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-calendar text-blue-500"></i> Filtri Periodo
                </h2>
            </div>
            <form method="GET" action="{{ route('admin.reports.statistics') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Periodo</label>
                        <select name="period" id="period" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" onchange="updateDateRange()">
                            <option value="7" {{ $period == 7 ? 'selected' : '' }}>Ultimi 7 giorni</option>
                            <option value="30" {{ $period == 30 ? 'selected' : '' }}>Ultimi 30 giorni</option>
                            <option value="90" {{ $period == 90 ? 'selected' : '' }}>Ultimi 90 giorni</option>
                            <option value="365" {{ $period == 365 ? 'selected' : '' }}>Ultimo anno</option>
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data inizio</label>
                        <input type="date" name="start_date" id="start_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="{{ request('start_date', now()->subDays($period)->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data fine</label>
                        <input type="date" name="end_date" id="end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">&nbsp;</label>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-search"></i> Aggiorna Statistiche
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistiche principali -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flag text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Totale
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $period }} giorni</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Segnalazioni ricevute</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        In Attesa
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['pending'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0 }}%</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Da processare</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Risolte
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['resolved'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100, 1) : 0 }}%</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Completate</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Tempo Medio
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        @php
                            $resolved = \App\Models\Report::where(
                                'status',
                                \App\Models\Report::STATUS_RESOLVED,
                            )
                                ->whereBetween('created_at', [
                                    request('start_date', now()->subDays($period)->toDateString()),
                                    request('end_date', now()->toDateString()),
                                ])
                                ->whereNotNull('resolved_at')
                                ->get();

                            if ($resolved->count() > 0) {
                                $avgTime = $resolved->avg(function ($report) {
                                    return $report->resolved_at->diffInHours($report->created_at);
                                });
                                echo round($avgTime, 1) . 'h';
                            } else {
                                echo 'N/A';
                            }
                        @endphp
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Ore</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Risoluzione</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Distribuzione per tipo -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-pie text-blue-500"></i> Distribuzione per Tipo
                    </h2>
                </div>
                <div class="p-6">
                    <div class="h-64 mb-4">
                        <canvas id="typeChart" class="w-full h-full"></canvas>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($typeDistribution as $type)
                            <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                    {{ $type->type }}
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $type->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Distribuzione per priorità -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-bar text-blue-500"></i> Distribuzione per Priorità
                    </h2>
                </div>
                <div class="p-6">
                    <div class="h-64 mb-4">
                        <canvas id="priorityChart" class="w-full h-full"></canvas>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($priorityDistribution as $priority)
                            <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priority->priority == 'urgent'
                                    ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400'
                                    : ($priority->priority == 'high'
                                        ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400'
                                        : ($priority->priority == 'medium'
                                                                    ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400'
                                                                    : 'bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300')) }}">
                                    {{ ucfirst($priority->priority) }}
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $priority->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend temporale -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-500"></i> Trend Segnalazioni nel Tempo
                </h2>
            </div>
            <div class="p-6">
                <div class="h-80">
                    <canvas id="trendChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabelle dettagliate -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top utenti più segnalati -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-users text-red-500"></i> Top Utenti Più Segnalati
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Utente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Segnalazioni</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ultima</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $mostReported = \App\Models\Report::selectRaw(
                                    'reported_user_id, COUNT(*) as report_count, MAX(created_at) as last_report',
                                )
                                    ->whereBetween('created_at', [
                                        request('start_date', now()->subDays($period)->toDateString()),
                                        request('end_date', now()->toDateString()),
                                    ])
                                    ->whereNotNull('reported_user_id')
                                    ->groupBy('reported_user_id')
                                    ->orderBy('report_count', 'desc')
                                    ->limit(10)
                                    ->get();
                            @endphp
                            @forelse($mostReported as $report)
                                @php
                                    $user = \App\Models\User::find($report->reported_user_id);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($user && $user->userProfile->avatar)
                                                <img src="{{ asset('storage/' . $user->userProfile->avatar) }}"
                                                    class="w-8 h-8 rounded-full mr-3">
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $user->name ?? 'Utente eliminato' }}</div>
                                                <small class="text-gray-500 dark:text-gray-400">{{ $user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400">{{ $report->report_count }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <small>{{ \Carbon\Carbon::parse($report->last_report)->format('d/m/Y') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                            <p class="text-gray-500 dark:text-gray-400">Nessun dato disponibile</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top segnalatori -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-user-shield text-green-500"></i> Top Segnalatori
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Utente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Segnalazioni</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ultima</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $topReporters = \App\Models\Report::selectRaw(
                                    'reporter_id, COUNT(*) as report_count, MAX(created_at) as last_report',
                                )
                                    ->whereBetween('created_at', [
                                        request('start_date', now()->subDays($period)->toDateString()),
                                        request('end_date', now()->toDateString()),
                                    ])
                                    ->groupBy('reporter_id')
                                    ->orderBy('report_count', 'desc')
                                    ->limit(10)
                                    ->get();
                            @endphp
                            @forelse($topReporters as $report)
                                @php
                                    $user = \App\Models\User::find($report->reporter_id);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($user && $user->userProfile->avatar)
                                                <img src="{{ asset('storage/' . $user->userProfile->avatar) }}"
                                                    class="w-8 h-8 rounded-full mr-3">
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $user->name ?? 'Utente eliminato' }}</div>
                                                <small class="text-gray-500 dark:text-gray-400">{{ $user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">{{ $report->report_count }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <small>{{ \Carbon\Carbon::parse($report->last_report)->format('d/m/Y') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-user-shield text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                            <p class="text-gray-500 dark:text-gray-400">Nessun dato disponibile</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Performance amministratori -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-user-tie text-blue-500"></i> Performance Amministratori
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amministratore</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assegnate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Risolte</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tempo Medio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Efficienza</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $admins = \App\Models\User::where('role', 'admin')->get();
                        @endphp
                        @forelse($admins as $admin)
                            @php
                                $assigned = \App\Models\Report::where('admin_id', $admin->id)
                                    ->whereBetween('created_at', [
                                        request('start_date', now()->subDays($period)->toDateString()),
                                        request('end_date', now()->toDateString()),
                                    ])
                                    ->count();

                                $resolved = \App\Models\Report::where('admin_id', $admin->id)
                                    ->where('status', \App\Models\Report::STATUS_RESOLVED)
                                    ->whereBetween('created_at', [
                                        request('start_date', now()->subDays($period)->toDateString()),
                                        request('end_date', now()->toDateString()),
                                    ])
                                    ->whereNotNull('resolved_at')
                                    ->get();

                                $avgTime =
                                    $resolved->count() > 0
                                        ? round(
                                            $resolved->avg(function ($report) {
                                                return $report->resolved_at->diffInHours(
                                                    $report->created_at,
                                                );
                                            }),
                                            1,
                                        )
                                        : null;

                                $efficiency =
                                    $assigned > 0 ? round(($resolved->count() / $assigned) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if ($admin->userProfile->avatar)
                                            <img src="{{ asset('storage/' . $admin->userProfile->avatar) }}"
                                                class="w-8 h-8 rounded-full mr-3">
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $admin->name }}</div>
                                            <small class="text-gray-500 dark:text-gray-400">{{ $admin->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400">{{ $assigned }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">{{ $resolved->count() }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($avgTime)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400">{{ $avgTime }}h</span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-5 relative">
                                        <div class="h-5 rounded-full flex items-center justify-center text-xs font-medium text-white {{ $efficiency >= 80 ? 'bg-green-500' : ($efficiency >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                            style="width: {{ $efficiency }}%">
                                            {{ $efficiency }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-user-tie text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Nessun amministratore trovato</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dati per i grafici
            const typeData = @json($typeDistribution);
            const priorityData = @json($priorityDistribution);
            const trendData = @json($trendData);

            // Funzione per gestire il tema dei grafici
            function getChartOptions() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                            }
                        },
                        x: {
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                            }
                        }
                    }
                };
            }

            // Grafico distribuzione per tipo
            const typeCtx = document.getElementById('typeChart').getContext('2d');
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: typeData.map(item => getTypeLabel(item.type)),
                    datasets: [{
                        data: typeData.map(item => item.count),
                        backgroundColor: [
                            '#ef4444', '#f97316', '#eab308', '#22c55e', 
                            '#3b82f6', '#8b5cf6', '#ec4899'
                        ]
                    }]
                },
                options: getChartOptions()
            });

            // Grafico distribuzione per priorità
            const priorityCtx = document.getElementById('priorityChart').getContext('2d');
            new Chart(priorityCtx, {
                type: 'bar',
                data: {
                    labels: priorityData.map(item => getPriorityLabel(item.priority)),
                    datasets: [{
                        label: 'Numero segnalazioni',
                        data: priorityData.map(item => item.count),
                        backgroundColor: [
                            '#ef4444', // urgent - red
                            '#f59e0b', // high - yellow  
                            '#3b82f6', // medium - blue
                            '#6b7280' // low - gray
                        ]
                    }]
                },
                options: {
                    ...getChartOptions(),
                    plugins: {
                        ...getChartOptions().plugins,
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Grafico trend temporale
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendData.map(item => formatDate(item.date)),
                    datasets: [{
                        label: 'Segnalazioni per giorno',
                        data: trendData.map(item => item.count),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: getChartOptions()
            });

            // Funzioni di utilità
            function getTypeLabel(type) {
                const labels = {
                    'spam': 'Spam',
                    'harassment': 'Molestie',
                    'copyright': 'Copyright',
                    'inappropriate_content': 'Contenuto inappropriato',
                    'fake_information': 'Informazioni false',
                    'other': 'Altro'
                };
                return labels[type] || type;
            }

            function getPriorityLabel(priority) {
                const labels = {
                    'urgent': 'Urgente',
                    'high': 'Alta',
                    'medium': 'Media',
                    'low': 'Bassa'
                };
                return labels[priority] || priority;
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('it-IT', {
                    month: 'short',
                    day: 'numeric'
                });
            }

            // Aggiorna i grafici quando cambia il tema
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        // Ricarica la pagina per aggiornare i grafici con il nuovo tema
                        setTimeout(() => window.location.reload(), 100);
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true
            });
        });

        function updateDateRange() {
            const period = document.getElementById('period').value;
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            const end = new Date();
            const start = new Date();
            start.setDate(start.getDate() - parseInt(period));

            startDate.value = start.toISOString().split('T')[0];
            endDate.value = end.toISOString().split('T')[0];
        }
    </script>
</x-admin-layout>
