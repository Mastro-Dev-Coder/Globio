<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-8">
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

        <!-- General Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cog mr-3 text-gray-500"></i>
                    {{ __('ui.admin_settings_general_title') }}
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
                class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_site_name_label') }}
                        </label>
                        <input type="text" id="site_name" name="site_name"
                            value="{{ old('site_name', $settings['site_name']) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('ui.admin_settings_site_name_help') }}</p>
                    </div>

                    <div>
                        <label for="max_upload_size"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_max_upload_label') }}
                        </label>
                        <input type="number" id="max_upload_size" name="max_upload_size"
                            value="{{ old('max_upload_size', $settings['max_upload_size']) }}" min="50"
                            max="2000"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('ui.admin_settings_max_upload_help') }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label for="require_approval"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('ui.admin_settings_require_approval_label') }}
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="require_approval" value="0">
                            <label class="toggle-switch">
                                <input type="checkbox" id="require_approval" name="require_approval" value="1"
                                    {{ $settings['require_approval'] ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('ui.admin_settings_require_approval_help') }}
                            </span>
                        </div>
                    </div>

                    <!-- Sezione commenti e like rimossa come richiesto -->
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-palette mr-3 text-gray-500"></i>
                        {{ __('ui.admin_settings_brand_colors_title') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('ui.admin_settings_brand_colors_help') }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- {{ __('ui.admin_settings_primary_color_title') }} -->
                        <div class="space-y-4">
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('ui.admin_settings_primary_color_title') }}</h5>

                            <div>
                                <label for="primary_color"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.admin_settings_primary_color_title') }} Principale
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="primary_color_picker"
                                        value="{{ $settings['primary_color'] }}"
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer">
                                    <input type="text" id="primary_color" name="primary_color"
                                        value="{{ old('primary_color', $settings['primary_color']) }}"
                                        pattern="^#[a-fA-F0-9]{6}$"
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="#dc2626">
                                </div>
                            </div>

                            <div>
                                <label for="primary_color_light"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.admin_settings_primary_color_title') }} Chiaro
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="primary_color_light_picker"
                                        value="{{ $settings['primary_color_light'] }}"
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer">
                                    <input type="text" id="primary_color_light" name="primary_color_light"
                                        value="{{ old('primary_color_light', $settings['primary_color_light']) }}"
                                        pattern="^#[a-fA-F0-9]{6}$"
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="#ef4444">
                                </div>
                            </div>

                            <div>
                                <label for="primary_color_dark"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.admin_settings_primary_color_title') }} Scuro
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="primary_color_dark_picker"
                                        value="{{ $settings['primary_color_dark'] }}"
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer">
                                    <input type="text" id="primary_color_dark" name="primary_color_dark"
                                        value="{{ old('primary_color_dark', $settings['primary_color_dark']) }}"
                                        pattern="^#[a-fA-F0-9]{6}$"
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="#b91c1c">
                                </div>
                            </div>
                        </div>

                        <!-- {{ __('ui.admin_settings_accent_color_title') }} -->
                        <div class="space-y-4">
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('ui.admin_settings_accent_color_title') }}</h5>

                            <div>
                                <label for="accent_color"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.admin_settings_accent_color_main_label') }}
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="accent_color_picker"
                                        value="{{ $settings['accent_color'] }}"
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer">
                                    <input type="text" id="accent_color" name="accent_color"
                                        value="{{ old('accent_color', $settings['accent_color']) }}"
                                        pattern="^#[a-fA-F0-9]{6}$"
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="#dc2626">
                                </div>
                            </div>

                            <div>
                                <label for="accent_color_light"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.admin_settings_accent_color_light_label') }}
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="accent_color_light_picker"
                                        value="{{ $settings['accent_color_light'] }}"
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer">
                                    <input type="text" id="accent_color_light" name="accent_color_light"
                                        value="{{ old('accent_color_light', $settings['accent_color_light']) }}"
                                        pattern="^#[a-fA-F0-9]{6}$"
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="#ef4444">
                                </div>
                            </div>

                            <div>
                                <label for="accent_color_dark"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.admin_settings_accent_color_dark_label') }}
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="accent_color_dark_picker"
                                        value="{{ $settings['accent_color_dark'] }}"
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer">
                                    <input type="text" id="accent_color_dark" name="accent_color_dark"
                                        value="{{ old('accent_color_dark', $settings['accent_color_dark']) }}"
                                        pattern="^#[a-fA-F0-9]{6}$"
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="#b91c1c">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- {{ __('ui.admin_settings_colors_preview_title') }} -->
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('ui.admin_settings_colors_preview_title') }}</h6>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div id="preview-primary" class="w-full h-12 rounded-md mb-2"
                                    style="background-color: {{ $settings['primary_color'] }};"></div>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400">{{ __('ui.admin_settings_preview_primary') }}</span>
                            </div>
                            <div class="text-center">
                                <div id="preview-primary-light" class="w-full h-12 rounded-md mb-2"
                                    style="background-color: {{ $settings['primary_color_light'] }};"></div>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400">{{ __('ui.admin_settings_preview_primary_light') }}</span>
                            </div>
                            <div class="text-center">
                                <div id="preview-accent" class="w-full h-12 rounded-md mb-2"
                                    style="background-color: {{ $settings['accent_color'] }};"></div>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400">{{ __('ui.admin_settings_preview_accent') }}</span>
                            </div>
                            <div class="text-center">
                                <div id="preview-accent-light" class="w-full h-12 rounded-md mb-2"
                                    style="background-color: {{ $settings['accent_color_light'] }};"></div>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400">{{ __('ui.admin_settings_preview_accent_light') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- {{ __('ui.admin_settings_custom_logo_title') }} -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-8">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-image mr-3 text-gray-500"></i>
                        {{ __('ui.admin_settings_custom_logo_title') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('ui.admin_settings_custom_logo_help') }}
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label for="custom_logo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_settings_upload_logo_label') }}
                            </label>
                            <input type="file" id="custom_logo" name="custom_logo" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('ui.admin_settings_upload_logo_help') }}
                            </p>
                        </div>

                        @if (isset($settings['logo']) && $settings['logo'])
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('ui.admin_settings_current_logo_title') }}</h6>
                                <div class="flex items-center space-x-4">
                                    <img src="{{ Storage::url($settings['logo']) }}"
                                        alt="{{ __('ui.admin_settings_current_logo_alt') }}"
                                        class="h-12 w-auto object-contain">
                                    <button type="button" onclick="removeLogo()"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        <i class="fas fa-trash mr-1"></i>{{ __('ui.admin_settings_remove_logo') }}
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div id="logo-preview" class="hidden bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.admin_settings_logo_preview_title') }}</h6>
                            <img id="preview-image" src=""
                                alt="{{ __('ui.admin_settings_logo_preview_alt') }}"
                                class="h-12 w-auto object-contain">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 text-white rounded-md hover:opacity-90 transition-all"
                        style="background-color: var(--primary-color);">
                        <i class="fas fa-save mr-2"></i>{{ __('ui.admin_settings_save') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- SMTP Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-envelope mr-3 text-gray-500"></i>
                    {{ __('ui.admin_settings_email_title') }}
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="smtp_host"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_smtp_host_label') }}
                        </label>
                        <input type="text" id="smtp_host" name="smtp_host"
                            value="{{ old('smtp_host', $settings['smtp_host']) }}" placeholder="smtp.gmail.com"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label for="smtp_port"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_smtp_port_label') }}
                        </label>
                        <input type="number" id="smtp_port" name="smtp_port"
                            value="{{ old('smtp_port', $settings['smtp_port']) }}" placeholder="587"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="smtp_username"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_smtp_username_label') }}
                        </label>
                        <input type="text" id="smtp_username" name="smtp_username"
                            value="{{ old('smtp_username', $settings['smtp_username']) }}"
                            placeholder="your-email@gmail.com"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label for="smtp_password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_smtp_password_label') }}
                        </label>
                        <input type="password" id="smtp_password" name="smtp_password"
                            value="{{ old('smtp_password', $settings['smtp_password']) }}"
                            placeholder="Your app password"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="smtp_encryption"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_smtp_encryption_label') }}
                        </label>
                        <select id="smtp_encryption" name="smtp_encryption"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                            <option value="tls"
                                {{ ($settings['smtp_encryption'] ?: 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ $settings['smtp_encryption'] === 'ssl' ? 'selected' : '' }}>SSL
                            </option>
                            <option value="null" {{ !$settings['smtp_encryption'] ? 'selected' : '' }}>
                                {{ __('ui.admin_settings_smtp_encryption_none') }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="from_address"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.admin_settings_from_address_label') }}
                        </label>
                        <input type="email" id="from_address" name="from_address"
                            value="{{ old('from_address', $settings['from_address']) }}"
                            placeholder="noreply@globio.app"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>{{ __('ui.admin_settings_save_email') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- FFmpeg Settings Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-film mr-3 text-gray-500"></i>
                    {{ __('ui.admin_settings_video_ffmpeg_title') }}
                </h3>
            </div>

            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('ui.admin_settings_video_ffmpeg_help') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('ui.admin_settings_ffmpeg_status_label') }}</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                <i class="fas fa-check-circle mr-1"></i>
                                Online
                            </span>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('ui.admin_settings_transcoding_label') }}</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                <i class="fas fa-cog mr-1"></i>
                                Abilitata
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('ui.admin_settings_ffmpeg_config_help') }}
                    </p>
                    <a href="{{ route('admin.settings.index') }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-cogs mr-2"></i>{{ __('ui.admin_settings_configure_ffmpeg') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-info-circle mr-3 text-gray-500"></i>
                    {{ __('ui.admin_settings_system_info_title') }}
                </h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                            {{ __('ui.admin_settings_laravel_version') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white">{{ app()->version() }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                            {{ __('ui.admin_settings_php_version') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white">{{ phpversion() }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                            {{ __('ui.admin_settings_environment') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ app()->environment() }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                            {{ __('ui.admin_settings_storage') }}</h4>
                        @php
                            $diskSpace = disk_free_space('.');
                            $totalSpace = disk_total_space('.');
                            $percentage = $totalSpace ? round((($totalSpace - $diskSpace) / $totalSpace) * 100, 1) : 0;
                        @endphp
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full"
                                    style="width: {{ $percentage }}%; background-color: var(--primary-color);">
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $percentage }}%</span>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                            {{ __('ui.admin_settings_cache_driver') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white">{{ config('cache.default') }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                            {{ __('ui.admin_settings_database') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ config("__('ui.admin_settings_database').default") }}</p>
                    </div>
                </div>

                <!-- System Actions -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-tools mr-3 text-gray-500"></i>
                        {{ __('ui.admin_settings_system_actions_title') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('ui.admin_settings_system_actions_help') }}
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <button onclick="clearCache()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="fas fa-broom mr-2"></i>
                            {{ __('ui.admin_settings_clear_cache') }}
                        </button>

                        <a href="{{ route('admin.settings.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="fas fa-cogs mr-2"></i>
                            {{ __('ui.admin_settings_configure_ffmpeg') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript per la gestione dei colori -->
    <script>
        // Sincronizzazione color picker e input di testo
        const colorInputs = [{
                picker: 'primary_color_picker',
                input: 'primary_color'
            },
            {
                picker: 'primary_color_light_picker',
                input: 'primary_color_light'
            },
            {
                picker: 'primary_color_dark_picker',
                input: 'primary_color_dark'
            },
            {
                picker: 'accent_color_picker',
                input: 'accent_color'
            },
            {
                picker: 'accent_color_light_picker',
                input: 'accent_color_light'
            },
            {
                picker: 'accent_color_dark_picker',
                input: 'accent_color_dark'
            }
        ];

        colorInputs.forEach(({
            picker,
            input
        }) => {
            const pickerElement = document.getElementById(picker);
            const inputElement = document.getElementById(input);

            if (pickerElement && inputElement) {
                // Sincronizzazione picker -> input
                pickerElement.addEventListener('input', function() {
                    inputElement.value = this.value;
                    updatePreview();
                });

                // Sincronizzazione input -> picker
                inputElement.addEventListener('input', function() {
                    if (this.value.match(/^#[a-fA-F0-9]{6}$/)) {
                        pickerElement.value = this.value;
                        updatePreview();
                    }
                });
            }
        });

        // Funzione per aggiornare l'anteprima
        function updatePreview() {
            const primaryColor = document.getElementById('primary_color').value;
            const primaryLight = document.getElementById('primary_color_light').value;
            const accentColor = document.getElementById('accent_color').value;
            const accentLight = document.getElementById('accent_color_light').value;

            const primaryPreview = document.getElementById('preview-primary');
            const primaryLightPreview = document.getElementById('preview-primary-light');
            const accentPreview = document.getElementById('preview-accent');
            const accentLightPreview = document.getElementById('preview-accent-light');

            if (primaryPreview) primaryPreview.style.backgroundColor = primaryColor;
            if (primaryLightPreview) primaryLightPreview.style.backgroundColor = primaryLight;
            if (accentPreview) accentPreview.style.backgroundColor = accentColor;
            if (accentLightPreview) accentLightPreview.style.backgroundColor = accentLight;
        }

        // Aggiorna l'anteprima all'avvio
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });

        // Form validation per i colori
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const colorFields = [
                    'primary_color', 'primary_color_light', 'primary_color_dark',
                    'accent_color', 'accent_color_light', 'accent_color_dark'
                ];
                let hasErrors = false;

                colorFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input && input.value && !input.value.match(/^#[a-fA-F0-9]{6}$/)) {
                        input.classList.add('border-red-500');
                        hasErrors = true;
                    } else if (input) {
                        input.classList.remove('border-red-500');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    alert(@json(__('ui.admin_settings_color_invalid_alert')));
                }
            });
        });

        // Gestione caricamento logo
        document.addEventListener('DOMContentLoaded', function() {
            const logoInput = document.getElementById('custom_logo');
            const previewDiv = document.getElementById('logo-preview');
            const previewImage = document.getElementById('preview-image');

            if (logoInput && previewDiv && previewImage) {
                logoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validazione tipo file
                        if (!file.type.match('image.*')) {
                            alert(@json(__('ui.admin_settings_logo_invalid_type')));
                            this.value = '';
                            return;
                        }

                        // Validazione dimensione file (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert(@json(__('ui.admin_settings_logo_too_large')));
                            this.value = '';
                            return;
                        }

                        // Mostra anteprima
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                            previewDiv.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        // Funzione per rimuovere il logo
        function removeLogo() {
            if (confirm(@json(__('ui.admin_settings_remove_logo_confirm')))) {
                // Crea un form dinamico per inviare la richiesta di rimozione
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.settings.update') }}';

                // CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);

                // Method
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'PUT';
                form.appendChild(method);

                // Action per rimuovere logo
                const removeAction = document.createElement('input');
                removeAction.type = 'hidden';
                removeAction.name = 'remove_logo';
                removeAction.value = '1';
                form.appendChild(removeAction);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-admin-layout>
