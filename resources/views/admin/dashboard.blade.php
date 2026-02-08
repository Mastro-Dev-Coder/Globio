<x-admin-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-tachometer-alt mr-3 text-red-600"></i>{{ __('ui.admin_dashboard_title') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('ui.admin_dashboard_subtitle') }}
            </p>
        </div>

        <!-- Filtri periodo -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_dashboard_period') }}</h2>
                <form method="GET" class="flex items-center gap-4">
                    <select name="period" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="7" {{ $period == 7 ? 'selected' : '' }}>{{ __('ui.admin_dashboard_last_7_days') }}</option>
                        <option value="30" {{ $period == 30 ? 'selected' : '' }}>{{ __('ui.admin_dashboard_last_30_days') }}</option>
                        <option value="90" {{ $period == 90 ? 'selected' : '' }}>{{ __('ui.admin_dashboard_last_3_months') }}</option>
                        <option value="365" {{ $period == 365 ? 'selected' : '' }}>{{ __('ui.admin_dashboard_last_year') }}</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Statistiche principali -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Utenti -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                        +{{ number_format($userStats['new']) }}
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($userStats['total']) }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_dashboard_users_total', ['new' => $userStats['new']]) }}
                </p>
            </div>

            <!-- Video -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-video text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                        +{{ number_format($contentStats['new_videos']) }}
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($contentStats['total_videos']) }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_dashboard_videos_total', ['new' => $contentStats['new_videos']]) }}
                </p>
            </div>

            <!-- Visualizzazioni -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($analyticsStats->total_views ?? 0) }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_dashboard_total_views') }}
                </p>
            </div>

            <!-- Ricavi -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-euro-sign text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                        {{ number_format($monetizationStats->total_revenue ?? 0, 2) }}&euro;
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($monetizationStats->total_transactions ?? 0) }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_dashboard_transactions', ['pending' => number_format($monetizationStats->pending_transactions ?? 0)]) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Trend giornaliero -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-line mr-2 text-blue-500"></i>
                        {{ __('ui.admin_dashboard_daily_trend') }}
                    </h2>
                </div>
                <div class="p-6">
                    @if (isset($dailyTrends) && count($dailyTrends) > 0)
                        <div class="relative">
                            <canvas id="dailyTrendsChart" width="400" height="200"></canvas>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chart-line text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">{{ __('ui.admin_dashboard_no_data') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Segnalazioni -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-flag mr-2 text-red-500"></i>
                        {{ __('ui.admin_dashboard_reports_status') }}
                    </h2>
                    <a href="{{ route('admin.reports') }}"
                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-700">
                        {{ __('ui.admin_dashboard_view_all') }}
                    </a>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.admin_dashboard_pending') }}</span>
                            </div>
                            <span class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $reportsStats['pending'] ?? 0 }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 dark:text-green-400 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.admin_dashboard_resolved') }}</span>
                            </div>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ $reportsStats['resolved'] ?? 0 }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-times text-gray-600 dark:text-gray-400 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ __('ui.admin_dashboard_dismissed') }}</span>
                            </div>
                            <span class="text-xl font-bold text-gray-600 dark:text-gray-400">
                                {{ $reportsStats['dismissed'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Creators -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-crown mr-2 text-yellow-500"></i>
                        {{ __('ui.admin_dashboard_top_creators') }}
                    </h2>
                </div>
                <div class="p-6">
                    @if (isset($topCreators) && $topCreators->count() > 0)
                        <div class="space-y-4">
                            @foreach ($topCreators->take(5) as $creator)
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                        @if ($creator->userProfile->avatar_url)
                                            <img src="{{ asset('storage/' . $creator->userProfile->avatar_url) }}"alt="{{ __('ui.avatar') }}"
                                                class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $creator->userProfile && $creator->userProfile->channel_name ? $creator->userProfile->channel_name : $creator->name }}
                                        </h4>
                                        <div
                                            class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <span>
                                                <i class="fas fa-video mr-1"></i>
                                                {{ $creator->videos_count }}
                                            </span>
                                            <span>
                                                <i class="fas fa-eye mr-1"></i>
                                                {{ number_format($creator->videos_views_count_sum ?? 0) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">{{ __('ui.admin_dashboard_no_creators') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Azioni rapide -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-bolt mr-2 text-orange-500"></i>
                        {{ __('ui.admin_dashboard_quick_actions') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('admin.users') }}"
                            class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="text-center">
                                <div
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ __('ui.admin_dashboard_manage_users') }}</h3>
                            </div>
                        </a>

                        <a href="{{ route('admin.reports') }}"
                            class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="text-center">
                                <div
                                    class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-flag text-red-600 dark:text-red-400"></i>
                                </div>
                                <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ __('ui.admin_dashboard_reports') }}</h3>
                            </div>
                        </a>

                        <a href="{{ route('admin.analytics') }}"
                            class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="text-center">
                                <div
                                    class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-chart-bar text-green-600 dark:text-green-400"></i>
                                </div>
                                <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ __('ui.admin_dashboard_analytics') }}</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (isset($dailyTrends) && count($dailyTrends) > 0)
                const locale = @json(app()->getLocale());
                const ctx = document.getElementById('dailyTrendsChart').getContext('2d');
                const trendsData = @json($dailyTrends);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendsData.map(item => {
                            return new Date(item.date).toLocaleDateString(locale, {
                                day: 'numeric',
                                month: 'short'
                            });
                        }),
                        datasets: [{
                                label: @json(__('ui.admin_dashboard_users_label')),
                                data: trendsData.map(item => item.users),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                yAxisID: 'y'
                            },
                            {
                                label: @json(__('ui.admin_dashboard_videos_label')),
                                data: trendsData.map(item => item.videos),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                yAxisID: 'y1'
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
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            }
                        }
                    }
                });
            @endif
        });
    </script>
</x-admin-layout>
