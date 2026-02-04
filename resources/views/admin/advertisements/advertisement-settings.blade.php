<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cogs text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Impostazioni Pubblicità Video
                            Avanzate</h1>
                        <p class="text-gray-600 dark:text-gray-400">Configura VAST, VMAP, Google AdSense e altre
                            pubblicità video</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.advertisements.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Traditional Ads Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-video mr-2"></i>
                        Pubblicità Tradizionali
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Pre-roll Ads -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pre-Roll Ads</label>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="ads_pre_roll_enabled" value="1"
                                        {{ $settings['ads_pre_roll_enabled'] ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mostra pubblicità prima del video
                                principale</p>
                        </div>

                        <!-- Mid-roll Ads -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Mid-Roll Ads</label>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="ads_mid_roll_enabled" value="1"
                                        {{ $settings['ads_mid_roll_enabled'] ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mostra pubblicità durante la
                                riproduzione</p>
                        </div>

                        <!-- Post-roll Ads -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Post-Roll
                                    Ads</label>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="ads_post_roll_enabled" value="1"
                                        {{ $settings['ads_post_roll_enabled'] ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mostra pubblicità alla fine del video
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VAST Ads Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-code mr-2"></i>
                        VAST (Video Ad Serving Template)
                    </h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Abilita VAST
                                    Ads</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Supporto per VAST 4.0 con tracking
                                    events</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="ads_vast_enabled" value="1"
                                    {{ $settings['ads_vast_enabled'] ?? false ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tipo di sorgente VAST
                            </label>
                            <select name="ads_vast_source_type" id="ads_vast_source_type"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="url" {{ ($settings['ads_vast_source_type'] ?? 'url') === 'url' ? 'selected' : '' }}>
                                    URL VAST (link esterno)
                                </option>
                                <option value="xml" {{ ($settings['ads_vast_source_type'] ?? 'url') === 'xml' ? 'selected' : '' }}>
                                    XML VAST diretto
                                </option>
                            </select>
                        </div>

                        <!-- URL VAST Field -->
                        <div id="ads_vast_url_field" class="space-y-3" style="display: {{ ($settings['ads_vast_source_type'] ?? 'url') === 'url' ? 'block' : 'none' }};">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                URL VAST (uno per riga)
                            </label>
                            <textarea name="ads_vast_urls" rows="4"
                                placeholder="https://example.com/vast.xml&#10;https://another-vast-url.com/vast.xml"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ $settings['ads_vast_urls'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Inserisci gli URL VAST supportati dal
                                tuo ad server</p>
                        </div>

                        <!-- XML VAST Field -->
                        <div id="ads_vast_xml_field" class="space-y-3" style="display: {{ ($settings['ads_vast_source_type'] ?? 'url') === 'xml' ? 'block' : 'none' }};">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                XML VAST diretto
                            </label>
                            <textarea name="ads_vast_xml" rows="12"
                                placeholder='&lt;VAST version=&quot;4.0&quot;&gt;&#10;  &lt;Ad&gt;&#10;    &lt;InLine&gt;&#10;      &lt;Creatives&gt;&#10;        &lt;Creative&gt;&#10;          &lt;Linear&gt;&#10;            &lt;MediaFiles&gt;&#10;              &lt;MediaFile delivery=&quot;progressive&quot; type=&quot;video/mp4&quot; width=&quot;640&quot; height=&quot;480&quot;&gt;https://example.com/ad.mp4&lt;/MediaFile&gt;&#10;            &lt;/MediaFiles&gt;&#10;          &lt;/Linear&gt;&#10;        &lt;/Creative&gt;&#10;      &lt;/Creatives&gt;&#10;    &lt;/InLine&gt;&#10;  &lt;/Ad&gt;&#10;&lt;/VAST&gt;'
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm">{{ $settings['ads_vast_xml'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Incolla direttamente il codice XML VAST. Supporta VAST 4.0 con tracking events.</p>
                        </div>

                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">VAST Information
                                    </h4>
                                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                        VAST è uno standard per la distribuzione di pubblicità video. Supporta pre-roll,
                                        mid-roll, post-roll con tracking events e skip functionality.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- VAST Custom Settings -->
                        <div class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-sliders-h mr-2"></i>
                                Impostazioni Personalizzate VAST
                            </h3>
                            
                            <!-- Clickthrough URL -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Link della Pubblicità (Clickthrough URL)
                                </label>
                                <input type="url" name="ads_vast_clickthrough_url"
                                    value="{{ $settings['ads_vast_clickthrough_url'] ?? '' }}"
                                    placeholder="https://example.com/landing-page"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    URL di destinazione quando l'utente clicca sulla pubblicità. Se lasciato vuoto, verrà usato l'URL specificato nel VAST XML.
                                </p>
                            </div>

                            <!-- Custom Text -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Testo Personalizzato della Pubblicità
                                </label>
                                <input type="text" name="ads_vast_custom_text"
                                    value="{{ $settings['ads_vast_custom_text'] ?? '' }}"
                                    placeholder="Scopri di più su questo prodotto"
                                    maxlength="100"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Testo personalizzato da mostrare sulla pubblicità (massimo 100 caratteri). Se lasciato vuoto, verrà usato il testo predefinito.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VMAP Ads Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-list mr-2"></i>
                        VMAP (Video Multiple Ad Playlist)
                    </h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Abilita VMAP
                                    Ads</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Playlist multiple di pubblicità con
                                    timing flessibile</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="ads_vmap_enabled" value="1"
                                    {{ $settings['ads_vmap_enabled'] ?? false ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tipo di sorgente VMAP
                            </label>
                            <select name="ads_vmap_source_type" id="ads_vmap_source_type"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="url" {{ ($settings['ads_vmap_source_type'] ?? 'url') === 'url' ? 'selected' : '' }}>
                                    URL VMAP (link esterno)
                                </option>
                                <option value="xml" {{ ($settings['ads_vmap_source_type'] ?? 'url') === 'xml' ? 'selected' : '' }}>
                                    XML VMAP diretto
                                </option>
                            </select>
                        </div>

                        <!-- URL VMAP Field -->
                        <div id="ads_vmap_url_field" class="space-y-3" style="display: {{ ($settings['ads_vmap_source_type'] ?? 'url') === 'url' ? 'block' : 'none' }};">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                URL VMAP (uno per riga)
                            </label>
                            <textarea name="ads_vmap_urls" rows="4"
                                placeholder="https://example.com/vmap.xml&#10;https://another-vmap-url.com/vmap.xml"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ $settings['ads_vmap_urls'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Inserisci gli URL VMAP per playlist di
                                pubblicità multiple</p>
                        </div>

                        <!-- XML VMAP Field -->
                        <div id="ads_vmap_xml_field" class="space-y-3" style="display: {{ ($settings['ads_vmap_source_type'] ?? 'url') === 'xml' ? 'block' : 'none' }};">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                XML VMAP diretto
                            </label>
                            <textarea name="ads_vmap_xml" rows="12"
                                placeholder='&lt;vmap:VMAP version=&quot;1.0&quot; xmlns:vmap=&quot;http://www.iab.net/vmap-1.0&quot;&gt;&#10;  &lt;vmap:AdBreak breakType=&quot;linear&quot; breakId=&quot;pre-roll&quot; timeOffset=&quot;start&quot;&gt;&#10;    &lt;vmap:AdSource id=&quot;ad1&quot;&gt;&#10;      &lt;vmap:AdTagURI&gt;&#10;        &lt;![CDATA[https://example.com/vast.xml]]&gt;&#10;      &lt;/vmap:AdTagURI&gt;&#10;    &lt;/vmap:AdSource&gt;&#10;  &lt;/vmap:AdBreak&gt;&#10;  &lt;vmap:AdBreak breakType=&quot;linear&quot; breakId=&quot;mid-roll&quot; timeOffset=&quot;00:00:30&quot;&gt;&#10;    &lt;vmap:AdSource id=&quot;ad2&quot;&gt;&#10;      &lt;vmap:AdTagURI&gt;&#10;        &lt;![CDATA[https://example.com/vast2.xml]]&gt;&#10;      &lt;/vmap:AdTagURI&gt;&#10;    &lt;/vmap:AdSource&gt;&#10;  &lt;/vmap:AdBreak&gt;&#10;&lt;/vmap:VMAP&gt;'
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm">{{ $settings['ads_vmap_xml'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Incolla direttamente il codice XML VMAP. Supporta VMAP 1.0 con AdBreaks personalizzati.</p>
                        </div>

                        <div
                            class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-green-600 dark:text-green-400 mt-0.5"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-green-900 dark:text-green-100">VMAP Information
                                    </h4>
                                    <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                        VMAP permette di specificare multiple posizioni di pubblicità in una playlist,
                                        supportando timeOffset personalizzati e AdBreaks flessibili.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google AdSense Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fab fa-google mr-2"></i>
                        Google AdSense for Video
                    </h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Abilita Google
                                    AdSense</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Pubblicità automatica di Google con
                                    revenue sharing</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="ads_google_adsense_enabled" value="1"
                                    {{ $settings['ads_google_adsense_enabled'] ?? false ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Google Ad Client ID
                                </label>
                                <input type="text" name="ads_google_ad_client"
                                    value="{{ $settings['ads_google_ad_client'] ?? '' }}"
                                    placeholder="ca-pub-xxxxxxxxxxxxxxxx"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Google Ad Slot ID
                                </label>
                                <input type="text" name="ads_google_ad_slot"
                                    value="{{ $settings['ads_google_ad_slot'] ?? '' }}" placeholder="1234567890"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ad Format
                                </label>
                                <select name="ads_google_ad_format"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="video"
                                        {{ ($settings['ads_google_ad_format'] ?? 'video') === 'video' ? 'selected' : '' }}>
                                        Video</option>
                                    <option value="text_html"
                                        {{ ($settings['ads_google_ad_format'] ?? '') === 'text_html' ? 'selected' : '' }}>
                                        Text/HTML</option>
                                    <option value="auto"
                                        {{ ($settings['ads_google_ad_format'] ?? '') === 'auto' ? 'selected' : '' }}>
                                        Auto</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ad Theme
                                </label>
                                <select name="ads_google_ad_theme"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="dark"
                                        {{ ($settings['ads_google_ad_theme'] ?? 'dark') === 'dark' ? 'selected' : '' }}>
                                        Dark</option>
                                    <option value="light"
                                        {{ ($settings['ads_google_ad_theme'] ?? '') === 'light' ? 'selected' : '' }}>
                                        Light</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ad Language
                                </label>
                                <select name="ads_google_ad_language"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="it"
                                        {{ ($settings['ads_google_ad_language'] ?? 'it') === 'it' ? 'selected' : '' }}>
                                        Italiano</option>
                                    <option value="en"
                                        {{ ($settings['ads_google_ad_language'] ?? '') === 'en' ? 'selected' : '' }}>
                                        English</option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <i class="fab fa-google text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-900 dark:text-yellow-100">Google AdSense
                                        Information</h4>
                                    <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                        Richiede un account Google AdSense approvato. Utilizza i tuoi Ad Client e Ad
                                        Slot ID per configurare le pubblicità automatiche.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skip Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-forward mr-2"></i>
                        Impostazioni Skip
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Skip Delay -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Ritardo Skip (secondi)
                            </label>
                            <input type="number" name="ads_skip_delay" value="{{ $settings['ads_skip_delay'] }}"
                                min="0" max="60"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tempo di attesa prima di poter saltare
                                la pubblicità</p>
                        </div>

                        <!-- Frequency Cap -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Frequenza Massima
                            </label>
                            <input type="number" name="ads_frequency_cap"
                                value="{{ $settings['ads_frequency_cap'] }}" min="1" max="5"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Numero massimo di ads per sessione
                                utente</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mid-roll Positions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-percentage mr-2"></i>
                        Posizioni Mid-Roll
                    </h2>

                    <div class="space-y-4">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Posizioni Mid-Roll (percentuali separate da virgola)
                            </label>
                            <input type="text" name="ads_mid_roll_positions"
                                value="{{ $settings['ads_mid_roll_positions'] }}" placeholder="25,50,75"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Percentuali del video dove mostrare le pubblicità mid-roll (es: 25,50,75 per 25%, 50% e
                                75% del video)
                            </p>
                        </div>

                        <!-- Max Ads per Video -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Massimo Ads per Video
                            </label>
                            <input type="number" name="ads_max_per_video"
                                value="{{ $settings['ads_max_per_video'] }}" min="1" max="10"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Numero massimo di pubblicità da
                                mostrare per ogni video</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-vial mr-2"></i>
                        Test e Debug
                    </h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="/api/test/vast-ads" target="_blank"
                                class="inline-flex items-center px-4 py-2 border border-blue-300 dark:border-blue-600 rounded-lg text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                                <i class="fas fa-code mr-2"></i>
                                Test VAST
                            </a>

                            <a href="/api/test/vmap-ads?vmap_url=https://example.com/vmap.xml" target="_blank"
                                class="inline-flex items-center px-4 py-2 border border-green-300 dark:border-green-600 rounded-lg text-sm font-medium text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors">
                                <i class="fas fa-list mr-2"></i>
                                Test VMAP
                            </a>

                            <a href="/api/test/google-adsense" target="_blank"
                                class="inline-flex items-center px-4 py-2 border border-yellow-300 dark:border-yellow-600 rounded-lg text-sm font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/40 transition-colors">
                                <i class="fab fa-google mr-2"></i>
                                Test Google AdSense
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.advertisements') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Torna alle Pubblicità
                </a>

                <div class="flex space-x-3">
                    <button type="reset"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Ripristina
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Salva Impostazioni
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Real-time preview updates
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const previewElements = {
                'ads_pre_roll_enabled': document.querySelector('[data-preview="pre-roll"]'),
                'ads_mid_roll_enabled': document.querySelector('[data-preview="mid-roll"]'),
                'ads_post_roll_enabled': document.querySelector('[data-preview="post-roll"]'),
                'ads_skip_delay': document.querySelector('[data-preview="skip-delay"]'),
                'ads_mid_roll_positions': document.querySelector('[data-preview="mid-roll-positions"]'),
                'ads_max_per_video': document.querySelector('[data-preview="max-per-video"]')
            };

            form.addEventListener('change', function(e) {
                const input = e.target;
                if (previewElements[input.name]) {
                    updatePreview(input.name, input.type === 'checkbox' ? input.checked : input.value);
                }
            });

            function updatePreview(field, value) {
                const element = previewElements[field];
                if (!element) return;

                if (field.includes('enabled')) {
                    element.textContent = value ? 'Abilitato' : 'Disabilitato';
                    element.className = `font-medium ${value ? 'text-green-600' : 'text-red-600'}`;
                } else if (field === 'ads_skip_delay') {
                    element.textContent = value + 's';
                } else {
                    element.textContent = value;
                }
            }

            // Toggle VAST source type (URL vs XML)
            const vastSourceTypeSelect = document.getElementById('ads_vast_source_type');
            const vastUrlField = document.getElementById('ads_vast_url_field');
            const vastXmlField = document.getElementById('ads_vast_xml_field');

            if (vastSourceTypeSelect && vastUrlField && vastXmlField) {
                vastSourceTypeSelect.addEventListener('change', function() {
                    const selectedType = this.value;
                    if (selectedType === 'url') {
                        vastUrlField.style.display = 'block';
                        vastXmlField.style.display = 'none';
                    } else if (selectedType === 'xml') {
                        vastUrlField.style.display = 'none';
                        vastXmlField.style.display = 'block';
                    }
                });
            }

            // Toggle VMAP source type (URL vs XML)
            const vmapSourceTypeSelect = document.getElementById('ads_vmap_source_type');
            const vmapUrlField = document.getElementById('ads_vmap_url_field');
            const vmapXmlField = document.getElementById('ads_vmap_xml_field');

            if (vmapSourceTypeSelect && vmapUrlField && vmapXmlField) {
                vmapSourceTypeSelect.addEventListener('change', function() {
                    const selectedType = this.value;
                    if (selectedType === 'url') {
                        vmapUrlField.style.display = 'block';
                        vmapXmlField.style.display = 'none';
                    } else if (selectedType === 'xml') {
                        vmapUrlField.style.display = 'none';
                        vmapXmlField.style.display = 'block';
                    }
                });
            }

            // Form validation
            form.addEventListener('submit', function(e) {
                const midRollPositions = document.querySelector('input[name="ads_mid_roll_positions"]')
                    .value;
                const positions = midRollPositions.split(',').map(p => parseInt(p.trim())).filter(p => !
                    isNaN(p));

                if (positions.some(p => p <= 0 || p >= 100)) {
                    e.preventDefault();
                    alert('Le posizioni mid-roll devono essere percentuali valide tra 0 e 100 (esclusi)');
                    return;
                }

                if (positions.length === 0) {
                    e.preventDefault();
                    alert('Inserisci almeno una posizione valida per le pubblicità mid-roll');
                    return;
                }

                // Validazione Google AdSense
                const googleEnabled = document.querySelector('input[name="ads_google_adsense_enabled"]')
                    .checked;
                const adClient = document.querySelector('input[name="ads_google_ad_client"]').value;
                const adSlot = document.querySelector('input[name="ads_google_ad_slot"]').value;

                if (googleEnabled && (!adClient || !adSlot)) {
                    e.preventDefault();
                    alert('Per Google AdSense sono richiesti sia Ad Client ID che Ad Slot ID');
                    return;
                }

                // Validazione VAST - solo se VAST è abilitato
                // NOTA: Permettiamo il salvataggio del tipo di sorgente anche se il contenuto è vuoto
                const vastEnabled = document.querySelector('input[name="ads_vast_enabled"]').checked;
                const vastSourceType = document.getElementById('ads_vast_source_type');
                if (vastEnabled && vastSourceType) {
                    if (vastSourceType.value === 'url') {
                        const vastUrls = document.querySelector('textarea[name="ads_vast_urls"]').value.trim();
                        if (!vastUrls) {
                            // Non blocchiamo il salvataggio, ma mostriamo un avviso
                            // L'utente dovrà fornire gli URL prima di usare VAST
                        }
                    } else if (vastSourceType.value === 'xml') {
                        const vastXml = document.querySelector('textarea[name="ads_vast_xml"]').value.trim();
                        if (!vastXml) {
                            // Non blocchiamo il salvataggio, ma mostriamo un avviso
                            // L'utente dovrà fornire l'XML prima di usare VAST
                        } else {
                            // Validiamo l'XML solo se è stato fornito
                            if (!vastXml.includes('<VAST')) {
                                e.preventDefault();
                                alert('Il XML VAST deve contenere un elemento <VAST> valido');
                                return;
                            }
                        }
                    }
                }

                // Validazione VMAP
                // NOTA: Permettiamo il salvataggio del tipo di sorgente anche se il contenuto è vuoto
                const vmapEnabled = document.querySelector('input[name="ads_vmap_enabled"]').checked;
                const vmapSourceType = document.getElementById('ads_vmap_source_type');
                if (vmapEnabled && vmapSourceType) {
                    if (vmapSourceType.value === 'url') {
                        const vmapUrls = document.querySelector('textarea[name="ads_vmap_urls"]').value.trim();
                        if (!vmapUrls) {
                            // Non blocchiamo il salvataggio, ma mostriamo un avviso
                            // L'utente dovrà fornire gli URL prima di usare VMAP
                        }
                    } else if (vmapSourceType.value === 'xml') {
                        const vmapXml = document.querySelector('textarea[name="ads_vmap_xml"]').value.trim();
                        if (!vmapXml) {
                            // Non blocchiamo il salvataggio, ma mostriamo un avviso
                            // L'utente dovrà fornire l'XML prima di usare VMAP
                        } else {
                            // Validiamo l'XML solo se è stato fornito
                            if (!vmapXml.includes('<vmap:VMAP') && !vmapXml.includes('<VMAP')) {
                                e.preventDefault();
                                alert('Il XML VMAP deve contenere un elemento <vmap:VMAP> o <VMAP> valido');
                                return;
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout>
