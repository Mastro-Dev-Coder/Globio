<x-admin-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-flag mr-2 text-red-600"></i>
                    {{ __('ui.admin_reports_title') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('ui.admin_reports_subtitle') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="refreshPage()" 
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt"></i> {{ __('ui.admin_reports_refresh') }}
                </button>
                <a href="{{ route('admin.reports.export') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-file-export"></i> {{ __('ui.admin_reports_export') }}
                </a>
                <a href="{{ route('admin.reports.statistics') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chart-bar"></i> {{ __('ui.statistics') }}
                </a>
                <a href="{{ route('admin.reports.create-notification') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-paper-plane"></i> {{ __('ui.admin_report_detail_send_notification') }}
                </a>
            </div>
        </div>

        <!-- Statistiche -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_reports_total') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flag text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_reports_in_waiting') }}</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_reports_status_resolved') }}</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['resolved'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.admin_reports_rejected') }}</p>
                        <p class="text-2xl font-bold text-gray-600">{{ $stats['dismissed'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtri -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('admin.reports') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.admin_reports_status') }}</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>{{ __('ui.admin_reports_status_all') }}</option>
                        <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>{{ __('ui.admin_reports_status_pending') }}</option>
                        <option value="reviewed" {{ ($status ?? '') == 'reviewed' ? 'selected' : '' }}>{{ __('ui.admin_reports_status_reviewed') }}</option>
                        <option value="resolved" {{ ($status ?? '') == 'resolved' ? 'selected' : '' }}>{{ __('ui.admin_reports_status_resolved') }}</option>
                        <option value="escalated" {{ ($status ?? '') == 'escalated' ? 'selected' : '' }}>{{ __('ui.admin_reports_status_escalated') }}</option>
                        <option value="dismissed" {{ ($status ?? '') == 'dismissed' ? 'selected' : '' }}>{{ __('ui.admin_reports_status_dismissed') }}</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.admin_reports_target_type') }}</label>
                    <select name="target_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="all" {{ ($targetType ?? 'all') == 'all' ? 'selected' : '' }}>{{ __('ui.all') }}</option>
                        <option value="video" {{ ($targetType ?? '') == 'video' ? 'selected' : '' }}>{{ __('ui.admin_reports_video') }}</option>
                        <option value="channel" {{ ($targetType ?? '') == 'channel' ? 'selected' : '' }}>{{ __('ui.channel') }}</option>
                        <option value="comment" {{ ($targetType ?? '') == 'comment' ? 'selected' : '' }}>{{ __('ui.comment') }}</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.admin_reports_priority') }}</label>
                    <select name="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="all" {{ ($priority ?? 'all') == 'all' ? 'selected' : '' }}>{{ __('ui.admin_reports_status_all') }}</option>
                        <option value="urgent" {{ ($priority ?? '') == 'urgent' ? 'selected' : '' }}>{{ __('ui.admin_reports_priority_urgent') }}</option>
                        <option value="high" {{ ($priority ?? '') == 'high' ? 'selected' : '' }}>{{ __('ui.admin_reports_priority_high') }}</option>
                        <option value="medium" {{ ($priority ?? '') == 'medium' ? 'selected' : '' }}>{{ __('ui.admin_reports_priority_medium') }}</option>
                        <option value="low" {{ ($priority ?? '') == 'low' ? 'selected' : '' }}>{{ __('ui.admin_reports_priority_low') }}</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.admin_reports_search') }}</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('ui.admin_reports_search_placeholder') }}" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                        <i class="fas fa-search mr-1"></i> {{ __('ui.admin_reports_filter') }}
                    </button>
                    <a href="{{ route('admin.reports') }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg text-sm transition-colors">
                        <i class="fas fa-times mr-1"></i> {{ __('ui.admin_reports_reset') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista Segnalazioni -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-list mr-2 text-red-500"></i>
                    {{ __('ui.admin_reports_reports_count', ['count' => $reports->total()]) }}
                </h2>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <label for="selectAll" class="text-sm text-gray-700 dark:text-gray-300">{{ __('ui.admin_reports_select_all') }}</label>
                </div>
            </div>

            @if($reports->count() > 0)
                <form id="bulkForm" method="POST" action="{{ route('admin.reports.bulk-action') }}">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">
                                        <input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_id') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_type') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_status') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_priority') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_reporter') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_target') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_assigned') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_date') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.admin_reports_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($reports as $report)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $report->priority == 'urgent' ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="report_ids[]" value="{{ $report->id }}" class="report-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">#{{ $report->id }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                {{ $report->getTypeLabelAttribute() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @switch($report->status)
                                                    @case('pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 @break
                                                    @case('reviewed') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 @break
                                                    @case('resolved') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 @break
                                                    @case('dismissed') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @break
                                                    @case('escalated') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 @break
                                                @endswitch">
                                                {{ $report->getStatusLabelAttribute() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @switch($report->priority)
                                                    @case('urgent') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 @break
                                                    @case('high') bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400 @break
                                                    @case('medium') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 @break
                                                    @case('low') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 @break
                                                @endswitch">
                                                {{ $report->getPriorityLabelAttribute() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $report->reporter->name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            @if($report->video)
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-video text-red-500"></i>
                                                    {{ Str::limit($report->video->title, 20) }}
                                                </span>
                                            @elseif($report->channel)
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-tv text-purple-500"></i>
                                                    {{ Str::limit($report->channel->name, 20) }}
                                                </span>
                                            @elseif($report->comment)
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-comment text-blue-500"></i>
                                                    {{ Str::limit($report->comment->content, 20) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">{{ __('ui.admin_reports_na') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            @if($report->admin)
                                                <span class="flex items-center gap-1 text-blue-600">
                                                    <i class="fas fa-user-shield"></i>
                                                    {{ $report->admin->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">{{ __('ui.admin_reports_not_assigned') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $report->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-1">
                                                <a href="{{ route('admin.reports.show', $report) }}" 
                                                   class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors" title="{{ __('ui.admin_reports_view_details') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$report->admin_id)
                                                    <form action="{{ route('admin.reports.assign', $report) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
                                                        <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded transition-colors" title="{{ __('ui.admin_reports_assign_to_me') }}">
                                                            <i class="fas fa-user-plus"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Azioni bulk -->
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <span id="selectedCount" class="font-medium text-red-600">0</span> {{ __('ui.admin_reports_selected_count', ['count' => '']) }}
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="bulkAction('assign')" class="px-3 py-1.5 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors" disabled id="btnAssign">
                                <i class="fas fa-user-check mr-1"></i> {{ __('ui.admin_reports_bulk_assign') }}
                            </button>
                            <button type="button" onclick="bulkAction('resolve')" class="px-3 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" disabled id="btnResolve">
                                <i class="fas fa-check mr-1"></i> {{ __('ui.admin_reports_status_resolved') }}
                            </button>
                            <button type="button" onclick="bulkAction('dismiss')" class="px-3 py-1.5 text-sm bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors" disabled id="btnDismiss">
                                <i class="fas fa-times mr-1"></i> {{ __('ui.admin_reports_dismiss') }}
                            </button>
                            <button type="button" onclick="bulkAction('escalate')" class="px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" disabled id="btnEscalate">
                                <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('ui.admin_reports_status_escalated') }}
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Paginazione -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <i class="fas fa-flag text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('ui.no_reports_found') }}</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const selectAllTable = document.getElementById('selectAllTable');
            const checkboxes = document.querySelectorAll('.report-checkbox');
            const selectedCount = document.getElementById('selectedCount');
            const btnAssign = document.getElementById('btnAssign');
            const btnResolve = document.getElementById('btnResolve');
            const btnDismiss = document.getElementById('btnDismiss');
            const btnEscalate = document.getElementById('btnEscalate');

            function updateButtons() {
                const checked = document.querySelectorAll('.report-checkbox:checked').length;
                selectedCount.textContent = checked;
                const disabled = checked === 0;
                btnAssign.disabled = disabled;
                btnResolve.disabled = disabled;
                btnDismiss.disabled = disabled;
                btnEscalate.disabled = disabled;
            }

            function updateSelectAll() {
                const checked = document.querySelectorAll('.report-checkbox:checked').length;
                const total = checkboxes.length;
                selectAll.checked = checked === total && total > 0;
                selectAll.indeterminate = checked > 0 && checked < total;
                selectAllTable.checked = checked === total && total > 0;
                selectAllTable.indeterminate = checked > 0 && checked < total;
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectAll();
                updateButtons();
            });

            selectAllTable.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectAll();
                updateButtons();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateSelectAll();
                    updateButtons();
                });
            });

            updateButtons();
        });

        function refreshPage() {
            window.location.reload();
        }

        function bulkAction(action) {
            const checked = document.querySelectorAll('.report-checkbox:checked');
            if (checked.length === 0) {
                alert('{{ __('ui.admin_reports_select_min_one') }}');
                return;
            }

            const messages = {
                assign: '{{ __('ui.admin_reports_bulk_assign_confirm') }}',
                resolve: '{{ __('ui.admin_reports_bulk_resolve_confirm') }}',
                dismiss: '{{ __('ui.admin_reports_bulk_dismiss_confirm') }}',
                escalate: '{{ __('ui.admin_reports_bulk_escalate_confirm') }}'
            };

            if (confirm(messages[action])) {
                const form = document.getElementById('bulkForm');
                form.action = '{{ route('admin.reports.bulk-action') }}';
                
                // Rimuovi eventuali input action precedenti
                const existingAction = form.querySelector('input[name="action"]');
                if (existingAction) existingAction.remove();

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'action';
                input.value = action;
                form.appendChild(input);

                form.submit();
            }
        }
    </script>
</x-admin-layout>
