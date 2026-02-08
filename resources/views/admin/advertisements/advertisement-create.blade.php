<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('ui.admin_ads_create_title') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('ui.admin_ads_form_create_subtitle') }}
                </p>
            </div>
            <a href="{{ route('admin.advertisements') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left w-4 h-4 mr-2"></i>
                {{ __('ui.admin_ads_back_list') }}
            </a>
        </div>

        <form method="POST" action="{{ route('admin.advertisements.store') }}"
            enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('ui.admin_ads_basic_info') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_ads_name_label') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="{{ __('ui.admin_ads_name_placeholder') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_ads_type_label') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type-select" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">{{ __('ui.admin_ads_type_select') }}</option>
                            <option value="banner" {{ old('type') === 'banner' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_type_banner_label') }}
                            </option>
                            <option value="adsense" {{ old('type') === 'adsense' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_type_adsense_label') }}
                            </option>
                            <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_type_video_label') }}
                            </option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_ads_position_label') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="position" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">{{ __('ui.admin_ads_position_select') }}</option>
                            <option value="header" {{ old('position') === 'header' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_position_header_label') }}
                            </option>
                            <option value="sidebar" {{ old('position') === 'sidebar' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_position_sidebar_label') }}
                            </option>
                            <option value="footer" {{ old('position') === 'footer' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_position_footer_label') }}
                            </option>
                            <option value="between_videos" {{ old('position') === 'between_videos' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_position_between_videos') }}
                            </option>
                            <option value="video_overlay" {{ old('position') === 'video_overlay' ? 'selected' : '' }}>
                                {{ __('ui.admin_ads_position_video_overlay') }}
                            </option>
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_ads_priority_label') }}
                        </label>
                        <input type="number" name="priority" min="0" max="100"
                            value="{{ old('priority', 0) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('ui.admin_ads_priority_help') }}
                        </p>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            {{ __('ui.admin_ads_active_label') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-edit mr-2"></i>
                    {{ __('ui.admin_ads_content_title') }}
                </h3>

                <div class="space-y-6">
                    <!-- Banner Fields -->
                    <div id="banner-fields" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_ads_banner_image_label') }}
                            </label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('ui.admin_ads_supported_formats') }}
                            </p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_ads_link_label') }}
                            </label>
                            <input type="url" name="link_url" value="{{ old('link_url') }}"
                                placeholder="https://example.com"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @error('link_url')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_ads_alt_text_label') }}
                            </label>
                            <textarea name="content" rows="3" placeholder="{{ __('ui.admin_ads_alt_text_placeholder') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- AdSense Fields -->
                    <div id="adsense-fields" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_ads_adsense_code_label') }}
                            </label>
                            <textarea name="code" rows="8" placeholder="{{ __('ui.admin_ads_adsense_code_placeholder') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono text-sm">{{ old('code') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('ui.admin_ads_adsense_code_help') }}
                            </p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Video Fields -->
                    <div id="video-fields" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_ads_video_code_label') }}
                            </label>
                            <textarea name="code" rows="8" placeholder="{{ __('ui.admin_ads_video_code_placeholder') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono text-sm">{{ old('code') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('ui.admin_ads_video_code_help') }}
                            </p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_ads_video_duration_label') }}
                            </label>
                            <input type="number" name="video_duration" min="5" max="300"
                                value="{{ old('video_duration') }}" placeholder="{{ __('ui.admin_ads_video_duration_placeholder') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('ui.admin_ads_video_duration_help') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ __('ui.admin_ads_schedule_title') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_ads_start_date_label') }}
                        </label>
                        <input type="datetime-local" name="start_date"
                            value="{{ old('start_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('ui.admin_ads_start_date_help') }}
                        </p>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_ads_end_date_label') }}
                        </label>
                        <input type="datetime-local" name="end_date"
                            value="{{ old('end_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('ui.admin_ads_end_date_help') }}
                        </p>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.advertisements') }}"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('ui.admin_ads_cancel') }}
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus w-4 h-4 mr-2"></i>
                    {{ __('ui.admin_ads_create') }} {{ __('ui.admin_ads_table_ad') }}
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('type-select');
                const bannerFields = document.getElementById('banner-fields');
                const adsenseFields = document.getElementById('adsense-fields');
                const videoFields = document.getElementById('video-fields');

                function updateFields() {
                    const selectedType = typeSelect.value;

                    // Nascondi tutti i campi
                    if (bannerFields) bannerFields.style.display = 'none';
                    if (adsenseFields) adsenseFields.style.display = 'none';
                    if (videoFields) videoFields.style.display = 'none';

                    // Mostra i campi appropriati
                    if (selectedType === 'banner') {
                        if (bannerFields) bannerFields.style.display = 'block';
                    } else if (selectedType === 'adsense') {
                        if (adsenseFields) adsenseFields.style.display = 'block';
                    } else if (selectedType === 'video') {
                        if (videoFields) videoFields.style.display = 'block';
                    }
                }

                // Verifica che tutti gli elementi esistano
                if (typeSelect && bannerFields && adsenseFields && videoFields) {
                    typeSelect.addEventListener('change', updateFields);
                    updateFields(); // Chiamata iniziale
                } else {
                    console.error('Elementi del form non trovati:', {
                        typeSelect: !!typeSelect,
                        bannerFields: !!bannerFields,
                        adsenseFields: !!adsenseFields,
                        videoFields: !!videoFields
                    });
                }
            });
        </script>
    @endpush
</x-admin-layout>
