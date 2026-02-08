<x-admin-layout>
    <div class="space-y-6">
        <!-- Period Summary -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-6 text-white">
            <h3 class="text-lg font-semibold mb-2">{{ __('ui.admin_statistics_period_title') }}</h3>
            <p class="text-red-100">
                @if($period === 'week')
                    {{ __('ui.admin_statistics_period_week') }}
                @elseif($period === 'month')
                    {{ __('ui.admin_statistics_period_month') }}
                @else
                    {{ __('ui.admin_statistics_period_year') }}
                @endif
            </p>
        </div>

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Users Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_statistics_users_title') }}</h4>
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_total') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($stats['users']['total']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_new') }}</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            +{{ number_format($stats['users']['new']) }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        @php
                            $userGrowthPercentage = $stats['users']['total'] > 0 ? 
                                ($stats['users']['new'] / $stats['users']['total']) * 100 : 0;
                        @endphp
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ min($userGrowthPercentage, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Videos Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_statistics_videos_title') }}</h4>
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <i class="fas fa-video text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_total') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($stats['videos']['total']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_published') }}</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            {{ number_format($stats['videos']['published']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_new') }}</span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400">
                            +{{ number_format($stats['videos']['new']) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Views Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_statistics_views_title') }}</h4>
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <i class="fas fa-eye text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_total') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($stats['views']['total']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_period') }}</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            {{ number_format($stats['views']['new']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_avg_per_video') }}</span>
                        <span class="font-semibold text-purple-600 dark:text-purple-400">
                            @if($stats['videos']['published'] > 0)
                                {{ number_format($stats['views']['total'] / $stats['videos']['published'], 1) }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Comments Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_statistics_comments_title') }}</h4>
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <i class="fas fa-comments text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_total') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($stats['comments']['total']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_period') }}</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            +{{ number_format($stats['comments']['new']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_engagement') }}</span>
                        <span class="font-semibold text-orange-600 dark:text-orange-400">
                            @if($stats['views']['total'] > 0)
                                {{ number_format(($stats['comments']['total'] / $stats['views']['total']) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Growth Chart Placeholder -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('ui.admin_statistics_user_growth') }}
                </h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_new_users') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $stats['users']['new'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-green-600 dark:text-green-400">{{ __('ui.admin_statistics_growth') }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stats['users']['total'] > 0 ? round(($stats['users']['new'] / $stats['users']['total']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Performance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('ui.admin_statistics_video_performance') }}
                </h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_publish_rate') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                @if($stats['videos']['total'] > 0)
                                    {{ round(($stats['videos']['published'] / $stats['videos']['total']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('ui.admin_statistics_status') }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stats['videos']['published'] }}/{{ $stats['videos']['total'] }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('ui.admin_statistics_processing') }}</p>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                {{ $stats['videos']['pending'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-orange-600 dark:text-orange-400">{{ __('ui.admin_statistics_pending') }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('ui.admin_statistics_videos_pending') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                {{ __('ui.admin_statistics_additional_metrics') }}
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Content Quality -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('ui.admin_statistics_content_quality') }}</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('ui.admin_statistics_content_quality_help') }}
                    </p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                        @if($stats['videos']['total'] > 0)
                            {{ round(($stats['videos']['published'] / $stats['videos']['total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
                
                <!-- User Engagement -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-heart text-white text-xl"></i>
                    </div>
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('ui.admin_statistics_user_engagement') }}</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('ui.admin_statistics_comments_per_view') }}
                    </p>
                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                        @if($stats['views']['total'] > 0)
                            {{ number_format(($stats['comments']['total'] / $stats['views']['total']) * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
                
                <!-- Platform Health -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('ui.admin_statistics_platform_health') }}</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('ui.admin_statistics_growth') }} complessiva
                    </p>
                    <p class="text-lg font-semibold text-purple-600 dark:text-purple-400">
                        {{ __('ui.admin_statistics_positive') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

