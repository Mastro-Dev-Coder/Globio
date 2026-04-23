<x-admin-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-flag mr-2 text-red-600"></i>
                    {{ __('ui.admin_report_detail_title', ['id' => $report->id]) }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">{{ __('ui.dashboard') }}</a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-400"></i></li>
                        <li>
                            <a href="{{ route('admin.reports') }}"
                                class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">{{ __('ui.admin_reports_title') }}</a>
                        </li>
                        <li><i class="fas fa-chevron-right text-gray-400"></i></li>
                        <li class="text-gray-900 dark:text-gray-100">
                            {{ __('ui.admin_report_detail_title', ['id' => $report->id]) }}</li>
                    </ol>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i> {{ __('ui.admin_report_detail_back_list') }}
                </a>
                <a href="{{ route('admin.reports.create-notification') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-bell"></i> {{ __('ui.admin_report_detail_send_notification') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Dettagli principali -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informazioni di base -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-info-circle text-red-500"></i>
                            {{ __('ui.admin_report_detail_general_info') }}
                        </h2>
                        <div class="flex gap-2">
                            <span
                                class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full {{ $report->getStatusColorAttribute() === 'primary' ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400' : ($report->getStatusColorAttribute() === 'success' ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400' : ($report->getStatusColorAttribute() === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400' : ($report->getStatusColorAttribute() === 'danger' ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'))) }}">
                                {{ $report->getStatusLabelAttribute() }}
                            </span>
                            <span
                                class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full {{ $report->getPriorityColorAttribute() === 'danger' ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' : ($report->getPriorityColorAttribute() === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400' : 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400') }}">
                                {{ $report->getPriorityLabelAttribute() }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_report_type') }}</label>
                                <p class="text-red-600 dark:text-red-400 font-medium">
                                    {{ $report->getTypeLabelAttribute() }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_priority') }}</label>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->getPriorityColorAttribute() === 'danger' ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' : ($report->getPriorityColorAttribute() === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400' : 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400') }}">
                                    {{ $report->getPriorityLabelAttribute() }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_reason') }}</label>
                            <div class="bg-gray-50 dark:bg-gray-700/50 border-l-4 border-red-500 p-4 rounded-lg">
                                <p class="text-gray-900 dark:text-gray-100">{{ $report->reason }}</p>
                            </div>
                        </div>

                        @if ($report->description)
                            <div class="mb-6">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_description') }}</label>
                                <div class="bg-gray-50 dark:bg-gray-700/50 border-l-4 border-blue-500 p-4 rounded-lg">
                                    <p class="text-gray-900 dark:text-gray-100">{{ $report->description }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_creation_date') }}</label>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ $report->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            @if ($report->resolved_at)
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_resolution_date') }}</label>
                                    <p class="text-gray-900 dark:text-gray-100">
                                        {{ $report->resolved_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contenuto segnalato -->
                @if ($report->video)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-video text-red-500"></i>
                                {{ __('ui.admin_report_detail_reported_video') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    @if ($report->video->thumbnail)
                                        <img src="{{ asset('storage/' . $report->video->thumbnail) }}"
                                            class="w-full rounded-lg" alt="{{ __('ui.video_thumbnail') }}">
                                    @else
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center"
                                            style="height: 150px;">
                                            <i class="fas fa-video text-4xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                                        {{ $report->video->title }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $report->video->description }}
                                    </p>
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-eye"></i> {{ number_format($report->video->views) }}
                                                {{ __('ui.views_metric') }}
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-clock"></i>
                                                {{ $report->video->duration ?? __('ui.admin_reports_na') }}
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ route('videos.show', $report->video) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                        target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                        {{ __('ui.admin_report_detail_view_video') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($report->comment)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-comment text-blue-500"></i>
                                {{ __('ui.admin_report_detail_reported_comment') }}
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <strong
                                    class="text-blue-800 dark:text-blue-300">{{ __('ui.admin_report_detail_content_comment') }}</strong>
                                <p class="mt-2 text-gray-900 dark:text-gray-100">{{ $report->comment->content }}</p>
                            </div>
                            @if ($report->comment->parent_id)
                                <div
                                    class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <strong
                                        class="text-gray-700 dark:text-gray-300">{{ __('ui.admin_report_detail_reply_to') }}</strong>
                                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                                        {{ $report->comment->parent->content }}</p>
                                </div>
                            @endif
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-thumbs-up"></i> {{ $report->comment->likes_count ?? 0 }}
                                        {{ __('ui.admin_report_detail_likes') }}
                                    </small>
                                </div>
                                <div>
                                    <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-clock"></i>
                                        {{ $report->comment->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($report->channel)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-tv text-purple-500"></i>
                                {{ __('ui.admin_report_detail_reported_channel') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    @if ($report->channel->userProfile && $report->channel->userProfile->avatar)
                                        <img src="{{ asset('storage/' . $report->channel->userProfile->avatar) }}"
                                            class="w-24 h-24 rounded-full mx-auto" alt="{{ __('ui.avatar') }}">
                                    @else
                                        <div
                                            class="w-24 h-24 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center mx-auto">
                                            <i class="fas fa-tv text-4xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ $report->channel->userProfile->channel_name ?? $report->channel->name }}
                                    </h3>
                                    @if ($report->channel->userProfile && $report->channel->userProfile->bio)
                                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                                            {{ $report->channel->userProfile->bio }}</p>
                                    @endif
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-user"></i> {{ $report->channel->name }}
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-calendar"></i> {{ __('ui.created_on') }}
                                                {{ $report->channel->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ route('channels.show', $report->channel->userProfile->slug ?? $report->channel->id) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-purple-300 dark:border-purple-600 text-purple-700 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors"
                                        target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                        {{ __('ui.admin_report_detail_view_channel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Azioni amministrative -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-tools text-yellow-500"></i>
                            {{ __('ui.admin_report_detail_admin_actions') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                            @csrf

                            <div class="mb-6">
                                <label for="resolution_action"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_resolution_action') }}</label>
                                <select name="resolution_action" id="resolution_action"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required>
                                    <option value="">{{ __('ui.admin_report_detail_select_action') }}</option>
                                    <option value="{{ \App\Models\Report::ACTION_CONTENT_REMOVED }}">
                                        {{ __('ui.admin_report_detail_action_remove_content') }}</option>
                                    <option value="{{ \App\Models\Report::ACTION_USER_WARNED }}">
                                        {{ __('ui.admin_report_detail_action_warn_user') }}</option>
                                    <option value="{{ \App\Models\Report::ACTION_USER_SUSPENDED }}">
                                        {{ __('ui.admin_report_detail_action_suspend_user') }}</option>
                                    <option value="{{ \App\Models\Report::ACTION_USER_BANNED }}">
                                        {{ __('ui.admin_report_detail_action_ban_user') }}</option>
                                    <option value="{{ \App\Models\Report::ACTION_FALSE_REPORT }}">
                                        {{ __('ui.admin_report_detail_action_false_report') }}</option>
                                    <option value="{{ \App\Models\Report::ACTION_NO_ACTION }}">
                                        {{ __('ui.admin_report_detail_action_no_action') }}</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label for="admin_notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_admin_notes') }}</label>
                                <textarea name="admin_notes" id="admin_notes"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    rows="3" placeholder="{{ __('ui.admin_report_detail_notes_placeholder') }}"></textarea>
                            </div>

                            <div class="mb-6">
                                <div class="flex items-start">
                                    <input
                                        class="h-4 w-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500 dark:bg-gray-700 mt-1"
                                        type="checkbox" name="send_feedback" id="send_feedback" value="1">
                                    <label class="ml-3" for="send_feedback">
                                        <strong
                                            class="block text-gray-900 dark:text-white">{{ __('ui.admin_report_detail_send_feedback') }}</strong>
                                        <small
                                            class="block text-gray-600 dark:text-gray-400">{{ __('ui.admin_report_detail_feedback_desc') }}</small>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-6 hidden" id="feedback_message_group">
                                <label for="feedback_message"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_feedback_message') }}</label>
                                <textarea name="feedback_message" id="feedback_message"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    rows="3" placeholder="{{ __('ui.admin_report_detail_feedback_placeholder') }}" maxlength="500"></textarea>
                                <small
                                    class="text-gray-500 dark:text-gray-400">{{ __('ui.admin_report_detail_chars_remaining', ['count' => 500]) }}</small>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-check"></i> {{ __('ui.admin_report_detail_resolve') }}
                                </button>
                                <button type="button"
                                    class="inline-flex items-center gap-2 px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                    onclick="showDismissModal()">
                                    <i class="fas fa-times"></i> {{ __('ui.admin_report_detail_dismiss_report') }}
                                </button>
                                <button type="button"
                                    class="inline-flex items-center gap-2 px-6 py-2 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg transition-colors"
                                    onclick="showEscalateModal()">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ __('ui.admin_report_detail_escalate_report') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Note amministrative -->
                @if ($report->admin_notes)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-sticky-note text-gray-500"></i>
                                {{ __('ui.admin_report_detail_admin_notes') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="bg-gray-50 dark:bg-gray-700/50 border-l-4 border-gray-400 p-4 rounded-lg mb-4">
                                <p class="text-gray-900 dark:text-gray-100">{{ $report->admin_notes }}</p>
                            </div>
                            @if ($report->admin)
                                <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i class="fas fa-user"></i> {{ __('ui.added_by') }}: {{ $report->admin->name }}
                                    {{ __('ui.admin_report_detail_on_date') }}
                                    {{ $report->updated_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Utenti coinvolti -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-users text-blue-500"></i>
                            {{ __('ui.admin_report_detail_users_involved') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        <!-- Segnalatore -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-green-600 dark:text-green-400 flex items-center gap-2 mb-3">
                                <i class="fas fa-user-plus"></i> {{ __('ui.admin_report_detail_reported_by') }}
                            </h3>
                            <div class="flex items-center">
                                @if ($report->reporter->userProfile->avatar)
                                    <img src="{{ asset('storage/' . $report->reporter->userProfile->avatar) }}"
                                        class="w-12 h-12 rounded-full mr-3">
                                @endif
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        {{ $report->reporter->name }}</h4>
                                    <small
                                        class="text-gray-500 dark:text-gray-400 block">{{ $report->reporter->email }}</small>
                                    <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                        <i class="fas fa-clock"></i>
                                        {{ $report->reporter->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Utente segnalato -->
                        @if ($report->reportedUser)
                            <div>
                                <h3 class="font-semibold text-red-600 dark:text-red-400 flex items-center gap-2 mb-3">
                                    <i class="fas fa-user-times"></i> {{ __('ui.admin_report_detail_reported_user') }}
                                </h3>
                                <div class="flex items-center">
                                    @if ($report->reportedUser->userProfile->avatar)
                                        <img src="{{ asset('storage/' . $report->reportedUser->userProfile->avatar) }}"
                                            class="w-12 h-12 rounded-full mr-3">
                                    @endif
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            {{ $report->reportedUser->name }}</h4>
                                        <small
                                            class="text-gray-500 dark:text-gray-400 block">{{ $report->reportedUser->email }}</small>
                                        <small class="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                            <i class="fas fa-clock"></i>
                                            {{ $report->reportedUser->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('admin.users.show', $report->reportedUser) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <i class="fas fa-eye"></i> {{ __('ui.admin_report_detail_user_profile') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assegnazione -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-user-check text-blue-500"></i>
                            {{ __('ui.admin_report_detail_assigned_to') }}
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
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $report->admin->name }}
                                    </h4>
                                    <small
                                        class="text-gray-500 dark:text-gray-400">{{ __('ui.admin_report_detail_assigned_date') }}
                                        {{ $report->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                {{ __('ui.admin_reports_not_assigned') }}</p>
                            <form method="POST" action="{{ route('admin.reports.assign', $report) }}">
                                @csrf
                                <div class="mb-4">
                                    <select name="admin_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                        required>
                                        <option value="">{{ __('ui.select') }}...</option>
                                        @foreach (\App\Models\User::where('role', 'admin')->get() as $admin)
                                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-user-plus"></i> {{ __('ui.assign') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Statistiche -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-chart-bar text-gray-500"></i> {{ __('ui.statistics') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="border-r border-gray-200 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-red-600 dark:text-red-400">
                                    {{ \App\Models\Report::where('reporter_id', $report->reporter_id)->count() }}
                                </h3>
                                <small
                                    class="text-gray-500 dark:text-gray-400 block">{{ __('ui.admin_reports_reports_sent') }}</small>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ \App\Models\Report::where('reported_user_id', $report->reported_user_id)->count() }}
                                </h3>
                                <small
                                    class="text-gray-500 dark:text-gray-400 block">{{ __('ui.admin_reports_reports_received') }}</small>
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
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('ui.admin_report_detail_dismiss_report') }}</h5>
                    <button onclick="hideDismissModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                    @csrf
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('ui.admin_report_detail_dismiss_confirm') }}</p>
                        <div class="mb-4">
                            <label for="dismiss_notes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_admin_notes') }}
                                ({{ __('ui.optional') }})</label>
                            <textarea name="admin_notes" id="dismiss_notes"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                rows="3"></textarea>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-start">
                                <input
                                    class="h-4 w-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500 dark:bg-gray-700 mt-1"
                                    type="checkbox" name="send_feedback" id="dismiss_send_feedback" value="1">
                                <label class="ml-3 text-sm text-gray-700 dark:text-gray-300"
                                    for="dismiss_send_feedback">
                                    {{ __('ui.admin_report_detail_notify_creator') }}
                                </label>
                            </div>
                        </div>
                        <div class="mb-4 hidden" id="dismiss_feedback_message_group">
                            <label for="dismiss_feedback_message"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_feedback_message') }}</label>
                            <textarea name="feedback_message" id="dismiss_feedback_message"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                rows="3"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">{{ __('ui.cancel') }}</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">{{ __('ui.admin_report_detail_dismiss_report') }}</button>
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
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('ui.admin_report_detail_escalate_report') }}</h5>
                    <button onclick="hideEscalateModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.reports.escalate', $report) }}">
                    @csrf
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('ui.admin_report_detail_escalate_confirm') }}</p>
                        <div class="mb-4">
                            <label for="escalate_notes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_escalation_reason') }}
                                <span class="text-red-600 dark:text-red-400">*</span></label>
                            <textarea name="admin_notes" id="escalate_notes"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                rows="3" required></textarea>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-start">
                                <input
                                    class="h-4 w-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500 dark:bg-gray-700 mt-1"
                                    type="checkbox" name="send_feedback" id="escalate_send_feedback" value="1">
                                <label class="ml-3 text-sm text-gray-700 dark:text-gray-300"
                                    for="escalate_send_feedback">
                                    {{ __('ui.admin_report_detail_notify_creator') }}
                                </label>
                            </div>
                        </div>
                        <div class="mb-4 hidden" id="escalate_feedback_message_group">
                            <label for="escalate_feedback_message"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.admin_report_detail_feedback_message') }}</label>
                            <textarea name="feedback_message" id="escalate_feedback_message"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                rows="3"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">{{ __('ui.cancel') }}</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">{{ __('ui.admin_report_detail_escalate_report') }}</button>
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
                charCount.className = remaining < 50 ? 'text-red-600 dark:text-red-400' :
                    'text-gray-500 dark:text-gray-400';
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
