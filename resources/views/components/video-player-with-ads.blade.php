<div class="artplayer-app relative w-full max-w-screen-xl mx-auto p-4" style="overflow: visible;">
    <!-- Ambient light container - extends beyond video for the glow effect -->
    <div id="ambientLightContainer" class="absolute inset-0 -m-8 pointer-events-none z-0" style="overflow: visible;">
        <div id="ambientLightGlow" class="w-full h-full transition-all duration-500"></div>
    </div>
    
    <!-- Video container - rounded corners for video content only -->
    <div id="videoWrapper" class="relative w-full aspect-video bg-black group z-10 shadow-2xl rounded-xl overflow-visible" style="overflow: visible;">
        <div class="absolute inset-0 rounded-xl overflow-hidden">
            @if ($video->status !== 'published')
                <div class="absolute inset-0 flex items-center justify-center bg-gray-900 z-20 rounded-xl">
                    <div class="text-center p-8">
                        <div class="mb-6">
                            <div
                                class="w-20 h-20 border-4 border-yellow-500 border-t-transparent rounded-full animate-spin mx-auto">
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold text-white mb-3">Elaborazione video in corso</h3>
                        <p class="text-gray-300 mb-6 max-w-md mx-auto">Il video è attualmente in fase di elaborazione e sarà
                            disponibile a breve.</p>
                        <div class="flex items-center justify-center space-x-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse" style="animation-delay: 0.2s">
                            </div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse" style="animation-delay: 0.4s">
                            </div>
                        </div>
                        @if ($video->status === 'rejected')
                            <p class="red-400 mt-6 text-sm font-medium">Stato: Rifiutato</p>
                        @elseif($video->status === 'transcoding')
                            <p class="blue-400 mt-6 text-sm font-medium">Stato: Transcoding in corso...</p>
                        @else
                            <p class="yellow-400 mt-6 text-sm font-medium">Stato: {{ ucfirst($video->status) }}</p>
                        @endif
                    </div>
                </div>
            @else
                @php
                    $availableQualities = $video->getAvailableQualities();
                    $qualitiesJson = json_encode($availableQualities, JSON_THROW_ON_ERROR);

                    // Prepare playlist data for autoplay
                    $playlistData = [];
                    $playlistId = null;
                    $currentPlaylistIndex = -1;

                    if (!empty($playlistNextVideos)) {
                        $playlistId = $currentPlaylistId ?? null;
                        $currentPlaylistIndex = 0;

                        foreach ($playlistNextVideos as $idx => $nextV) {
                            $playlistData[] = [
                                'id' => $nextV->id,
                                'url' => route('videos.show', $nextV),
                                'title' => $nextV->title,
                                'poster' => $nextV->thumbnail_path ? asset('storage/' . $nextV->thumbnail_path) : '',
                            ];
                        }
                    }

                    $nextVideoUrl = isset($nextVideo) && $nextVideo ? route('videos.show', $nextVideo) : '';
                    $nextVideoPoster =
                        isset($nextVideo) && $nextVideo->thumbnail_path
                            ? asset('storage/' . $nextVideo->thumbnail_path)
                            : '';
                    $nextVideoTitle = isset($nextVideo) && $nextVideo ? $nextVideo->title : '';

                    $playlistJson = !empty($playlistData) ? json_encode($playlistData, JSON_THROW_ON_ERROR) : '[]';
                @endphp
                <div id="artplayer" class="w-full h-full rounded-xl"
                    style="overflow: visible;"
                    data-video-src="{{ asset('storage/' . $video->video_path) }}"
                    data-poster="{{ asset('storage/' . $video->thumbnail_path) }}" data-video-id="{{ $video->id }}"
                    data-video-title="{{ $video->title }}" data-csrf-token="{{ csrf_token() }}"
                    data-logo-url="{{ asset('storage/' . \App\Models\Setting::getValue('site_logo', 'logos/logo_1765197721.png')) }}"
                    data-player-color="{{ \App\Models\Setting::getValue('primary_color', '#FF0000') }}"
                    data-language="{{ app()->getLocale() }}" data-next-video-url="{{ $nextVideoUrl }}"
                    data-next-video-poster="{{ $nextVideoPoster }}" data-next-video-title="{{ $nextVideoTitle }}"
                    data-playlist-id="{{ $playlistId }}" data-playlist-index="{{ $currentPlaylistIndex }}"
                    data-playlist-videos="{{ $playlistJson }}" data-ad-settings="{!! json_encode(
                        [
                            'pre_roll_enabled' => \App\Models\Setting::getBooleanValue('ads_pre_roll_enabled', true),
                            'mid_roll_enabled' => \App\Models\Setting::getBooleanValue('ads_mid_roll_enabled', true),
                            'post_roll_enabled' => \App\Models\Setting::getBooleanValue('ads_post_roll_enabled', true),
                            'skip_delay' => (int) \App\Models\Setting::getValue('ads_skip_delay', 5),
                            'mid_roll_positions' => explode(',', \App\Models\Setting::getValue('ads_mid_roll_positions', '25,50,75')),
                            'max_ads_per_video' => (int) \App\Models\Setting::getValue('ads_max_per_video', 3),
                            'vast_enabled' => \App\Models\Setting::getBooleanValue('ads_vast_enabled', false),
                            'vast_source_type' => \App\Models\Setting::getValue('ads_vast_source_type', 'xml'),
                            'vast_url' => \App\Models\Setting::getValue('ads_vast_url', ''),
                            'vast_xml' => \App\Models\Setting::getValue('ads_vast_xml', ''),
                            'vast_clickthrough_url' => \App\Models\Setting::getValue('ads_vast_clickthrough_url', ''),
                            'vast_custom_text' => \App\Models\Setting::getValue('ads_vast_custom_text', ''),
                        ],
                        JSON_THROW_ON_ERROR,
                    ) !!}"
                    data-qualities="{{ $qualitiesJson }}"
                    data-subtitles="@if ($video->getAvailableSubtitles()) {!! json_encode($video->getAvailableSubtitles(), JSON_THROW_ON_ERROR) !!}
                @else[] @endif">
                </div>
            @endif
        </div>
    </div>
</div>

<script type="module">
    import {
        initArtPlayer
    } from '{{ Vite::asset('resources/js/artplayer-init.js') }}';

    document.addEventListener('DOMContentLoaded', () => {
        @if ($video->status === 'published')
            initArtPlayer('artplayer');
        @endif
    });
</script>
