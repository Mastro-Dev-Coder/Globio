<x-admin-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-chart-line mr-3 text-blue-600"></i>{{ __('ui.admin_analytics_title') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('ui.admin_analytics_subtitle') }}
            </p>
        </div>

        <!-- Filtri e controlli -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.admin_dashboard_period') }}</label>
                        <select name="period" onchange="updateAnalytics()" 
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="7">{{ __('ui.admin_dashboard_last_7_days') }}</option>
                            <option value="30" selected>{{ __('ui.admin_dashboard_last_30_days') }}</option>
                            <option value="90">{{ __('ui.admin_dashboard_last_3_months') }}</option>
                            <option value="365">{{ __('ui.admin_dashboard_last_year') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.admin_analytics_metric') }}</label>
                        <select name="metric" onchange="updateAnalytics()"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="all">{{ __('ui.admin_analytics_metric_all') }}</option>
                            <option value="views">{{ __('ui.views_metric') }}</option>
                            <option value="engagement">{{ __('ui.engagement') }}</option>
                            <option value="revenue">{{ __('ui.admin_analytics_metric_revenue') }}</option>
                            <option value="growth">{{ __('ui.admin_analytics_metric_growth') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="exportAnalytics()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                        <i class="fas fa-download mr-2"></i>{{ __('ui.admin_analytics_export') }}
                    </button>
                    <button onclick="refreshAnalytics()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors text-sm">
                        <i class="fas fa-sync-alt mr-2"></i>{{ __('ui.admin_analytics_refresh') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Views -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-green-600 dark:text-green-400 mr-1">
                            <i class="fas fa-arrow-up"></i>
                        </span>
                        <span class="text-green-600 dark:text-green-400 font-medium">{{ $growthRates['views'] ?? '+0%' }}</span>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($totalViews ?? 0) }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.total_views') }}
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, (($totalViews ?? 0) / 100000) * 100) }}%"></div>
                </div>
            </div>

            <!-- Average Watch Time -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-green-600 dark:text-green-400 mr-1">
                            <i class="fas fa-arrow-up"></i>
                        </span>
                        <span class="text-green-600 dark:text-green-400 font-medium">{{ $growthRates['watch_time'] ?? '+0%' }}</span>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ gmdate('H:i', $averageWatchTime ?? 0) }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_analytics_avg_watch_time') }}
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, (($averageWatchTime ?? 0) / 1800) * 100) }}%"></div>
                </div>
            </div>

            <!-- Engagement Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-heart text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-green-600 dark:text-green-400 mr-1">
                            <i class="fas fa-arrow-up"></i>
                        </span>
                        <span class="text-green-600 dark:text-green-400 font-medium">{{ $growthRates['engagement'] ?? '+0%' }}</span>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($engagementRate ?? 0, 1) }}%
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_analytics_engagement_rate') }}
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($engagementRate ?? 0)) }}%"></div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-euro-sign text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-green-600 dark:text-green-400 mr-1">
                            <i class="fas fa-arrow-up"></i>
                        </span>
                        <span class="text-green-600 dark:text-green-400 font-medium">{{ $growthRates['revenue'] ?? '+0%' }}</span>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    &euro;{{ number_format($totalRevenue ?? 0, 2) }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_analytics_revenue') }}
                </p>
                <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ min(100, (($totalRevenue ?? 0) / 10000) * 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Grafici principali -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Performance Trend -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-line mr-2 text-blue-500"></i>
                        {{ __('ui.admin_analytics_performance_trend') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative h-80">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Engagement Breakdown -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                        {{ __('ui.admin_analytics_engagement_breakdown') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative h-80">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analisi dettagliate -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Top Performing Content -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                        {{ __('ui.admin_analytics_top_content') }}
                    </h2>
                </div>
                <div class="p-6">
                    @if (isset($topContent) && $topContent->count() > 0)
                        <div class="space-y-4">
                            @foreach ($topContent->take(5) as $index => $content)
                                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="w-16 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex-shrink-0 overflow-hidden">
                                        @if ($content->thumbnail)
                                            <img src="{{ asset('storage/' . $content->thumbnail) }}" alt="{{ __('ui.video_thumbnail') }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-video text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $content->title }}
                                        </h4>
                                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <span>
                                                <i class="fas fa-eye mr-1"></i>
                                                {{ number_format($content->views) }}
                                            </span>
                                            <span>
                                                <i class="fas fa-heart mr-1"></i>
                                                {{ number_format($content->likes) }}
                                            </span>
                                            <span>
                                                <i class="fas fa-comments mr-1"></i>
                                                {{ number_format($content->comments) }}
                                            </span>
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ gmdate('H:i', $content->average_watch_time) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($content->engagement_score, 1) }}%
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.engagement') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chart-bar text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">{{ __('ui.no_data_available') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Audience Insights -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-users mr-2 text-blue-500"></i>
                        {{ __('ui.admin_analytics_audience_insights') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Demografia -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('ui.admin_analytics_top_countries') }}</h4>
                            <div class="space-y-2">
                                @foreach ($demographics['countries'] ?? [] as $country)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $country->country }}</span>
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ (($country->views / ($demographics['countries']->first()->views ?? 1)) * 100) }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($country->views) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Dispositivi -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('ui.devices') }}</h4>
                            <div class="space-y-2">
                                @foreach ($demographics['devices'] ?? [] as $device)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-{{ $device->device_type === 'mobile' ? 'mobile-alt' : ($device->device_type === 'tablet' ? 'tablet-alt' : 'desktop') }} text-gray-400"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $device->device_type }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-12 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ (($device->views / ($demographics['devices']->first()->views ?? 1)) * 100) }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($device->views) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Orari di punta -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('ui.admin_analytics_peak_hours') }}</h4>
                            <div class="grid grid-cols-4 gap-2 text-center">
                                @foreach ($peakHours ?? [] as $hour => $data)
                                    <div class="p-2 rounded-lg {{ $data['active'] ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-gray-50 dark:bg-gray-700/50' }}">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $hour }}:00</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ number_format($data['views']) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traffic Sources & Growth Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Traffic Sources -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-route mr-2 text-green-500"></i>
                        {{ __('ui.traffic_sources') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach ($trafficSources ?? [] as $source)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-{{ $source->traffic_source === 'search' ? 'search' : ($source->traffic_source === 'social' ? 'share-alt' : ($source->traffic_source === 'direct' ? 'link' : 'globe')) }} text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white capitalize">{{ $source->traffic_source }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $source->video_count }} {{ __('ui.video_short') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($source->views) }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.views') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Growth Analysis -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-area mr-2 text-purple-500"></i>
                        {{ __('ui.admin_analytics_growth_analysis') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative h-64">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-table mr-2 text-gray-500"></i>
                    {{ __('ui.admin_analytics_creator_details') }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.admin_analytics_creator') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.video') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.views_metric') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.engagement') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.watch_time') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.admin_analytics_revenue') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.admin_analytics_growth') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($creatorAnalytics ?? [] as $creator)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-400 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $creator->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $creator->video_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ number_format($creator->total_views) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        {{ number_format($creator->engagement_rate, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ gmdate('H:i:s', $creator->total_watch_time) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">&euro;{{ number_format($creator->total_revenue, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $creator->growth_rate >= 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                        <i class="fas fa-arrow-{{ $creator->growth_rate >= 0 ? 'up' : 'down' }} mr-1"></i>
                                        {{ number_format(abs($creator->growth_rate), 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p>{{ __('ui.no_data_available') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Performance Chart
                const performanceCtx = document.getElementById('performanceChart').getContext('2d');
                const performanceData = @json($performanceTrends ?? []);

                new Chart(performanceCtx, {
                    type: 'line',
                    data: {
                        labels: performanceData.map(item => item.date),
                        datasets: [
                            {
                                label: @json(__('ui.views_metric')),
                                data: performanceData.map(item => item.views),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                yAxisID: 'y'
                            },
                            {
                                label: @json(__('ui.engagement')),
                                data: performanceData.map(item => item.engagement),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    unit: 'day'
                                }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        }
                    }
                });

                // Engagement Chart
                const engagementCtx = document.getElementById('engagementChart').getContext('2d');
                const engagementData = @json($engagementBreakdown ?? []);

                new Chart(engagementCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [@json(__('ui.likes')), @json(__('ui.comments')), @json(__('ui.share')), @json(__('ui.save'))],
                        datasets: [{
                            data: [
                                engagementData.likes ?? 0,
                                engagementData.comments ?? 0,
                                engagementData.shares ?? 0,
                                engagementData.saves ?? 0
                            ],
                            backgroundColor: [
                                '#ef4444',
                                '#f97316',
                                '#eab308',
                                '#22c55e'
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

                // Growth Chart
                const growthCtx = document.getElementById('growthChart').getContext('2d');
                const growthData = @json($growthAnalysis ?? []);

                new Chart(growthCtx, {
                    type: 'bar',
                    data: {
                        labels: [@json(__('ui.users')), @json(__('ui.video')), @json(__('ui.views_metric')), @json(__('ui.admin_analytics_revenue'))],
                        datasets: [{
                            label: @json(__('ui.admin_analytics_growth_label')),
                            data: [
                                growthData.user_growth ?? 0,
                                growthData.video_growth ?? 0,
                                growthData.view_growth ?? 0,
                                growthData.revenue_growth ?? 0
                            ],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(139, 92, 246, 0.8)'
                            ],
                            borderColor: [
                                '#3b82f6',
                                '#10b981',
                                '#f59e0b',
                                '#8b5cf6'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });

            // Functions for interactive controls
            function updateAnalytics() {
                // Placeholder for updating charts with new filter data
                location.reload();
            }

            function exportAnalytics() {
                // Placeholder for export functionality
                alert(@json(__('ui.admin_analytics_export_in_progress')));
            }

            function refreshAnalytics() {
                location.reload();
            }
        </script>
    @endpush
</x-admin-layout>
