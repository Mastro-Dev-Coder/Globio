<x-admin-layout>
    @php
        $title = __('ui.admin_ffmpeg_title');

        $breadcrumbs = [];
        if (isset($breadcrumbs) && is_array($breadcrumbs)) {
            foreach ($breadcrumbs as $breadcrumb) {
                $breadcrumbs[] = $breadcrumb;
            }
        } else {
            $breadcrumbs = [
                ['name' => __('ui.admin_ffmpeg_breadcrumb_admin'), 'url' => route('admin.dashboard')],
                ['name' => __('ui.admin_ffmpeg_breadcrumb_settings'), 'url' => '#'],
            ];
        }

        $pageHeader = [
            'title' => __('ui.admin_ffmpeg_header_title'),
            'subtitle' => __('ui.admin_ffmpeg_header_subtitle'),
            'actions' => '<div class="flex items-center space-x-3">' .
                '<button type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    onclick="testFFmpeg()">
                    <i class="fas fa-check-circle mr-2"></i>' . __('ui.admin_ffmpeg_test') . '</button>' .
                '<button type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    onclick="migrateSettings()">
                    <i class="fas fa-sync-alt mr-2"></i>' . __('ui.admin_ffmpeg_migrate_env') . '</button>' .
            '</div>',
        ];
    @endphp

    <!-- Main Content -->
    <div class="space-y-6">
        <!-- Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" id="settingsSearch" 
                            placeholder="@{{ __('ui.admin_settings_search_placeholder') }}"
                            class="w-64 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button onclick="performSearch()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                        <i class="fas fa-search mr-2"></i>@{{ __('ui.search') }}
                    </button>
                    <span id="searchResultsCount" class="text-sm text-gray-600 dark:text-gray-400"></span>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-keyboard"></i>
                    <span>Ctrl+K @{{ __('ui.admin_settings_to_search') }}</span>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium">{{ __('ui.admin_settings_errors_title') }}</h3>
                        <ul class="mt-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>â€¢ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Settings Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    @php
                        $activeTab = request()->get('tab', 'ffmpeg');
                    @endphp

                    <button onclick="switchTab('ffmpeg')" id="tab-ffmpeg"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'ffmpeg' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-video mr-2"></i>
                        {{ __('ui.admin_ffmpeg_tab_ffmpeg') }}
                    </button>

                    <button onclick="switchTab('qualities')" id="tab-qualities"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'qualities' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-medal mr-2"></i>
                        {{ __('ui.admin_ffmpeg_tab_qualities') }}
                    </button>

                    <button onclick="switchTab('thumbnails')" id="tab-thumbnails"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'thumbnails' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-image mr-2"></i>
                        {{ __('ui.admin_ffmpeg_tab_thumbnails') }}
                    </button>

                    <button onclick="switchTab('transcoding')" id="tab-transcoding"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'transcoding' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <i class="fas fa-cogs mr-2"></i>
                        {{ __('ui.admin_ffmpeg_tab_transcoding') }}
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tab FFmpeg -->
                <div id="content-ffmpeg" class="tab-content {{ $activeTab === 'ffmpeg' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_ffmpeg_config_title') }}</h3>
                            <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span>{{ __('ui.admin_ffmpeg_system_online') }}</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.settings.ffmpeg') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- FFmpeg Settings -->
                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        {{ __('ui.admin_ffmpeg_base_config') }}</h4>

                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('ui.admin_ffmpeg_enabled') }}
                                        </label>
                                        <input type="checkbox" name="ffmpeg_enabled" value="1"
                                            {{ $settings['ffmpeg_enabled'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    </div>

                                    <div>
                                        <label for="ffmpeg_path"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_ffmpeg_path') }}
                                        </label>
                                        <input type="text" name="ffmpeg_path" value="{{ $settings['ffmpeg_path'] }}"
                                            placeholder="/usr/bin/ffmpeg"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_ffmpeg_path_help') }}</p>
                                    </div>

                                    <div>
                                        <label for="ffprobe_path"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_ffprobe_path') }}
                                        </label>
                                        <input type="text" name="ffprobe_path"
                                            value="{{ $settings['ffprobe_path'] }}" placeholder="/usr/bin/ffprobe"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_ffprobe_path_help') }}</p>
                                    </div>
                                </div>

                                <!-- Performance Settings -->
                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Performance</h4>

                                    <div>
                                        <label for="ffmpeg_timeout"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_ffmpeg_timeout') }}
                                        </label>
                                        <input type="number" name="ffmpeg_timeout"
                                            value="{{ $settings['ffmpeg_timeout'] }}" min="60" max="7200"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_ffmpeg_timeout_help') }}</p>
                                    </div>

                                    <div>
                                        <label for="ffmpeg_threads"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_ffmpeg_threads') }}
                                        </label>
                                        <input type="number" name="ffmpeg_threads"
                                            value="{{ $settings['ffmpeg_threads'] }}" min="1" max="16"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_ffmpeg_threads_help') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('ui.admin_ffmpeg_save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab QualitÃ  Video -->
                <div id="content-qualities" class="tab-content {{ $activeTab === 'qualities' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_qualities_title') }}
                            </h3>
                        </div>

                        <form action="{{ route('admin.settings.qualities') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        {{ __('ui.admin_qualities_section') }}</h4>

                                    <div>
                                        <label for="video_qualities"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_qualities_available') }}
                                        </label>
                                        <input type="text" name="video_qualities"
                                            value="{{ $settings['video_qualities'] }}"
                                            placeholder="720p,1080p,original"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_qualities_available_help') }}</p>
                                    </div>

                                    <div>
                                        <label for="default_video_quality"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_qualities_default') }}
                                        </label>
                                        <select name="default_video_quality"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                            @foreach (explode(',', $settings['video_qualities']) as $quality)
                                                <option value="{{ trim($quality) }}"
                                                    {{ $settings['default_video_quality'] == trim($quality) ? 'selected' : '' }}>
                                                    {{ trim($quality) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        {{ __('ui.admin_qualities_limits') }}</h4>

                                    <div>
                                        <label for="max_video_duration"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_qualities_max_duration') }}
                                        </label>
                                        <input type="number" name="max_video_duration"
                                            value="{{ $settings['max_video_duration'] }}" min="60"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_qualities_max_duration_help') }}</p>
                                    </div>

                                    <div>
                                        <label for="max_file_size"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_qualities_max_file_size') }}
                                        </label>
                                        <input type="number" name="max_file_size"
                                            value="{{ $settings['max_file_size'] }}" min="1024"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_qualities_max_file_size_help') }}</p>
                                    </div>

                                    <div>
                                        <label for="max_video_upload_mb"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('ui.admin_qualities_max_upload_mb') }}
                                        </label>
                                        <input type="number" name="max_video_upload_mb"
                                            value="{{ $settings['max_video_upload_mb'] }}" min="10" max="5000"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_qualities_max_upload_mb_help') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('ui.admin_qualities_save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab Thumbnail -->
                <div id="content-thumbnails" class="tab-content {{ $activeTab === 'thumbnails' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_thumbnails_title') }}</h3>
                        </div>

                        <form action="{{ route('admin.settings.thumbnails') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label for="thumbnail_quality"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('ui.admin_thumbnails_jpeg_quality') }}
                                    </label>
                                    <input type="number" name="thumbnail_quality"
                                        value="{{ $settings['thumbnail_quality'] }}" min="50" max="100"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_thumbnails_jpeg_quality_help') }}</p>
                                </div>

                                <div class="space-y-2">
                                    <label for="thumbnail_width"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('ui.admin_thumbnails_width') }}
                                    </label>
                                    <input type="number" name="thumbnail_width"
                                        value="{{ $settings['thumbnail_width'] }}" min="320" max="1920"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_thumbnails_width_help') }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label for="thumbnail_height"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('ui.admin_thumbnails_height') }}
                                    </label>
                                    <input type="number" name="thumbnail_height"
                                        value="{{ $settings['thumbnail_height'] }}" min="240" max="1080"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin_thumbnails_height_help') }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('ui.admin_thumbnails_save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab Transcodifica -->
                <div id="content-transcoding" class="tab-content {{ $activeTab === 'transcoding' ? '' : 'hidden' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.admin_transcoding_title') }}
                            </h3>
                        </div>

                        <form action="{{ route('admin.settings.transcoding') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <input type="checkbox" name="enable_transcoding" value="1"
                                            {{ $settings['enable_transcoding'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-0.5">
                                        <div class="ml-3">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('ui.admin_transcoding_enable') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin_transcoding_enable_help') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <input type="checkbox" name="auto_publish" value="1"
                                            {{ $settings['auto_publish'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-0.5">
                                        <div class="ml-3">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('ui.admin_transcoding_auto_publish') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin_transcoding_auto_publish_help') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <input type="checkbox" name="delete_original_after_transcoding"
                                            value="1"
                                            {{ $settings['delete_original_after_transcoding'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-0.5">
                                        <div class="ml-3">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('ui.admin_transcoding_delete_original') }}
                                            </label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin_transcoding_delete_original_help') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('ui.admin_transcoding_save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

<!-- Modal per risultati test -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('ui.admin_ffmpeg_test_result_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="testModalBody">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>
</div>

<script>
    // Funzioni globali per le tab
    function switchTab(tab) {
        try {
            // Nascondi tutti i tab content
            const allContents = document.querySelectorAll('.tab-content');
            allContents.forEach(content => {
                content.classList.add('hidden');
                content.style.display = 'none';
            });

            // Rimuovi stile active da tutti i tab buttons
            const allButtons = document.querySelectorAll('.tab-button');
            allButtons.forEach(button => {
                button.classList.remove('border-red-500', 'text-red-600', 'dark:text-red-400');
                button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Mostra il tab selezionato
            const targetContent = document.getElementById(`content-${tab}`);
            const targetButton = document.getElementById(`tab-${tab}`);

            if (targetContent && targetButton) {
                targetContent.classList.remove('hidden');
                targetContent.style.display = 'block';

                targetButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                targetButton.classList.add('border-red-500', 'text-red-600', 'dark:text-red-400');

                // Aggiorna URL senza ricaricare
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.pushState({
                    tab: tab
                }, '', url);
            } else {
                console.warn(`Tab elements not found for: ${tab}`);
            }
        } catch (error) {
            console.error('Error switching tab:', error);
        }
    }

    function testFFmpeg() {
        const modal = new bootstrap.Modal(document.getElementById('testModal'));
        const modalBody = document.getElementById('testModalBody');

        modalBody.innerHTML =
            '<div class="text-center p-4"><div class="spinner-border" role="status"></div><p class="mt-2">{{ __('ui.admin_ffmpeg_testing') }}</p></div>';
        modal.show();

        fetch('{{ route('admin.settings.test') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                let html = '<div class="p-4">';
                if (data.ffmpeg.available && data.ffprobe.available) {
                    html +=
                        '<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 p-4 rounded-lg mb-4">';
                    html += '<h6><i class="fas fa-check-circle"></i> {{ __('ui.admin_ffmpeg_working') }}</h6>';
                    html += '</div>';
                } else {
                    html +=
                        '<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg mb-4">';
                    html += '<h6><i class="fas fa-exclamation-triangle"></i> {{ __('ui.admin_ffmpeg_problems') }}</h6>';
                    html += '</div>';
                }

                html += '<div class="grid grid-cols-2 gap-4">';
                html += '<div><strong>{{ __('ui.admin_ffmpeg_label') }}:</strong><br>';
                html +=
                    `{{ __('ui.admin_ffmpeg_available') }}: ${data.ffmpeg.available ? '<span class="text-green-600">{{ __('ui.admin_yes') }}</span>' : '<span class="text-red-600">{{ __('ui.admin_no') }}</span>'}<br>`;
                html += `{{ __('ui.admin_ffmpeg_path_label') }}: ${data.ffmpeg.path}<br>`;
                if (data.ffmpeg.version) {
                    html += `{{ __('ui.admin_ffmpeg_version') }}: ${data.ffmpeg.version}`;
                }
                html += '</div>';

                html += '<div><strong>{{ __('ui.admin_ffprobe_label') }}:</strong><br>';
                html +=
                    `{{ __('ui.admin_ffmpeg_available') }}: ${data.ffprobe.available ? '<span class="text-green-600">{{ __('ui.admin_yes') }}</span>' : '<span class="text-red-600">{{ __('ui.admin_no') }}</span>'}<br>`;
                html += `{{ __('ui.admin_ffmpeg_path_label') }}: ${data.ffprobe.path}<br>`;
                if (data.ffprobe.version) {
                    html += `{{ __('ui.admin_ffmpeg_version') }}: ${data.ffprobe.version}`;
                }
                html += '</div></div>';

                html += '</div>';
                modalBody.innerHTML = html;
            })
            .catch(error => {
                modalBody.innerHTML =
                    '<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg"><h6>{{ __('ui.admin_ffmpeg_error_test') }}</h6><p>' +
                    error.message + '</p></div>';
            });
    }

    function migrateSettings() {
        if (confirm(@json(__('ui.admin_ffmpeg_migrate_confirm')))) {
            fetch('{{ route('admin.settings.migrate') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(@json(__('ui.admin_error_prefix')) + data.message);
                    }
                })
                .catch(error => {
                    alert(@json(__('ui.admin_ffmpeg_migrate_error')) + ' ' + error.message);
                });
        }
    }

    // Inizializzazione delle tab al caricamento della pagina
    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza la tab attiva in base all'URL
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'ffmpeg';
        switchTab(activeTab);
        
        // Verifica se c'è un parametro di ricerca nell'URL
        const searchQuery = urlParams.get('q');
        if (searchQuery) {
            document.getElementById('settingsSearch').value = searchQuery;
            setTimeout(() => performSearch(), 500);
        }
        
        // Ctrl+K per aprire la ricerca
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('settingsSearch').focus();
            }
        });
        
        // Cerca mentre scrivi
        document.getElementById('settingsSearch').addEventListener('input', function() {
            if (this.value.length >= 2) {
                performSearch();
            } else if (this.value.length === 0) {
                clearSearch();
            }
        });
        
        // Invio per cercare
        document.getElementById('settingsSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    });
    
    // Funzione per evidenziare il testo cercato
    function highlightText(element, searchText) {
        if (!searchText || !element) return;
        
        // Salta gli elementi input e textarea
        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
            return;
        }
        
        // Regex case-insensitive
        const regex = new RegExp(`(${escapeRegExp(searchText)})`, 'gi');
        
        // Evita di processare nodi già processati
        if (element.dataset.highlighted === 'true') return;
        
        // Processa il testo
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    if (node.parentElement.dataset.highlighted === 'true') {
                        return NodeFilter.FILTER_REJECT;
                    }
                    if (node.textContent.match(regex)) {
                        return NodeFilter.FILTER_ACCEPT;
                    }
                    return NodeFilter.FILTER_REJECT;
                }
            }
        );
        
        const textNodes = [];
        while (walker.nextNode()) {
            textNodes.push(walker.currentNode);
        }
        
        textNodes.forEach(node => {
            const parent = node.parentElement;
            if (parent.dataset.highlighted === 'true') return;
            
            const fragment = document.createDocumentFragment();
            let lastIndex = 0;
            const text = node.textContent;
            let match;
            
            while ((match = regex.exec(text)) !== null) {
                if (match.index > lastIndex) {
                    fragment.appendChild(document.createTextNode(text.substring(lastIndex, match.index)));
                }
                
                const span = document.createElement('span');
                span.className = 'search-highlight bg-yellow-200 dark:bg-yellow-800 text-gray-900 dark:text-yellow-100 px-0.5 rounded transition-colors duration-500';
                span.dataset.highlighted = 'true';
                span.textContent = match[0];
                fragment.appendChild(span);
                
                lastIndex = regex.lastIndex;
            }
            
            if (lastIndex < text.length) {
                fragment.appendChild(document.createTextNode(text.substring(lastIndex)));
            }
            
            parent.replaceChild(fragment, node);
        });
    }
    
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\    // Inizializzazione delle tab al caricamento della pagina
    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza la tab attiva in base all'URL
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'ffmpeg';
        switchTab(activeTab);
    });
</script>');
    }
    
    // Funzione principale di ricerca
    function performSearch() {
        const searchInput = document.getElementById('settingsSearch');
        const searchText = searchInput.value.trim();
        
        // Aggiorna URL con parametro di ricerca
        const url = new URL(window.location);
        if (searchText) {
            url.searchParams.set('q', searchText);
        } else {
            url.searchParams.delete('q');
        }
        window.history.replaceState({}, '', url);
        
        // Pulisci evidenziazioni precedenti
        clearHighlights();
        
        if (!searchText) {
            document.getElementById('searchResultsCount').textContent = '';
            return;
        }
        
        // Cerca in tutti i contenuti delle tab
        const searchableElements = document.querySelectorAll('.tab-content label, .tab-content h3, .tab-content h4, .tab-content p, .tab-content span, .tab-content div');
        let matchCount = 0;
        const regex = new RegExp(escapeRegExp(searchText), 'gi');
        
        searchableElements.forEach(el => {
            if (el.textContent.match(regex)) {
                highlightText(el, searchText);
                matchCount++;
            }
        });
        
        // Mostra conteggio risultati
        const resultsElement = document.getElementById('searchResultsCount');
        if (matchCount > 0) {
            resultsElement.textContent = `${matchCount} {{ __('ui.admin_settings_results_found') }}`;
            resultsElement.className = 'text-sm text-green-600 dark:text-green-400';
            
            // Trova il primo elemento evidenziato e scrolla verso di esso
            setTimeout(() => {
                const firstHighlight = document.querySelector('.search-highlight');
                if (firstHighlight) {
                    firstHighlight.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Animazione per evidenziare il primo risultato
                    firstHighlight.classList.add('bg-yellow-400', 'dark:bg-yellow-700');
                    setTimeout(() => {
                        firstHighlight.classList.remove('bg-yellow-400', 'dark:bg-yellow-700');
                    }, 2000);
                }
            }, 100);
            
            // Rimuovi evidenziazione dopo 10 secondi
            setTimeout(clearHighlights, 10000);
        } else {
            resultsElement.textContent = '{{ __('ui.no_results') }}';
            resultsElement.className = 'text-sm text-red-600 dark:text-red-400';
        }
    }
    
    function clearHighlights() {
        document.querySelectorAll('.search-highlight').forEach(el => {
            const parent = el.parentElement;
            const text = el.textContent;
            parent.replaceChild(document.createTextNode(text), el);
            parent.normalize();
        });
    }
    
    function clearSearch() {
        clearHighlights();
        document.getElementById('searchResultsCount').textContent = '';
        
        // Rimuovi parametro di ricerca dall'URL
        const url = new URL(window.location);
        url.searchParams.delete('q');
        window.history.replaceState({}, '', url);
    }
</script>


