import Artplayer from 'artplayer';
import artplayerPluginAmbilight from 'artplayer-plugin-ambilight';

// Export function for initializing ArtPlayer for reels
export function initReelPlayer(containerId, reelData = {}) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('Reel container not found:', containerId);
        return null;
    }

    const {
        videoUrl = '',
        poster = '',
        videoId = 0,
        title = '',
        duration = 0,
        qualities = [],
        autoplay = true,
        muted = false,
        loop = true,
        onEnded = null,
        onPlay = null,
        onPause = null,
        onTimeUpdate = null
    } = reelData;

    // Normalize qualities
    const qualityOrder = { '2160p': 1, '1440p': 2, '1080p': 3, '720p': 4, '480p': 5, '360p': 6, 'original': 7 };
    const normalizedQualities = (qualities || [])
        .sort((a, b) => (qualityOrder[a.quality] || 99) - (qualityOrder[b.quality] || 99))
        .map((q, idx) => ({
            html: String(q.html || q.quality),
            url: String(q.url),
            default: q.default ?? idx === 0
        }));

    let currentVideoUrl = normalizedQualities.find(q => q.default)?.url;
    if (!currentVideoUrl || currentVideoUrl === '') {
        currentVideoUrl = videoUrl;
    }

    if (!currentVideoUrl) {
        console.error('No video URL available for reel');
        return null;
    }

    // Reel-specific options
    const reelOptions = {
        container: container,
        url: currentVideoUrl,
        poster: poster,
        volume: 0.7,
        muted: muted,
        autoplay: autoplay,
        pip: false, // Disable PiP for reels as it's not suitable for vertical videos
        autoSize: false,
        autoMini: false,
        screenshot: false, // Disable screenshot for reels
        setting: true,
        loop: loop,
        flip: false, // Disable flip for reels
        playbackRate: false, // Disable playback rate for reels
        aspectRatio: false, // Use native aspect ratio for reels
        fullscreen: true,
        fullscreenWeb: true,
        miniProgressBar: true,
        theme: '#FF0000',
        lang: 'it',
        moreVideoAttr: {
            crossOrigin: 'anonymous',
            playsInline: true,
            preload: 'auto'
        },
        quality: normalizedQualities.length > 0 ? normalizedQualities : undefined,
        subtitle: { tracks: [] },
        controls: [
            // Custom play/pause button
            {
                name: 'reelPlay',
                position: 'center',
                html: `<svg class="art-icon" viewBox="0 0 36 36" width="60" height="60">
                    <circle cx="18" cy="18" r="17" fill="rgba(0,0,0,0.5)" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
                    <polygon points="14,10 26,18 14,26" fill="white"/>
                </svg>`,
                style: {
                    position: 'absolute',
                    left: '50%',
                    top: '50%',
                    transform: 'translate(-50%, -50%)',
                    cursor: 'pointer',
                    zIndex: 10
                },
                click: function (event, element) {
                    event.stopPropagation();
                    if (this.player.playing) {
                        this.player.pause();
                        this.updatePlayIcon(this.player.playing);
                    } else {
                        this.player.play();
                        this.updatePlayIcon(this.player.playing);
                    }
                },
                updatePlayIcon: function (isPlaying) {
                    const icon = this.element.querySelector('.art-icon');
                    if (icon) {
                        if (isPlaying) {
                            icon.innerHTML = `<circle cx="18" cy="18" r="17" fill="rgba(0,0,0,0.3)" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
                                <rect x="13" y="10" width="4" height="16" fill="white"/>
                                <rect x="19" y="10" width="4" height="16" fill="white"/>`;
                        } else {
                            icon.innerHTML = `<circle cx="18" cy="18" r="17" fill="rgba(0,0,0,0.5)" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
                                <polygon points="14,10 26,18 14,26" fill="white"/>`;
                        }
                    }
                },
                mounted: function (player) {
                    player.on('play', () => this.updatePlayIcon(true));
                    player.on('pause', () => this.updatePlayIcon(false));
                }
            },
            // Custom mute button
            {
                name: 'reelMute',
                position: 'top',
                html: `<svg class="reel-mute-icon" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="white" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                </svg>`,
                style: {
                    marginRight: '10px',
                    cursor: 'pointer'
                },
                click: function () {
                    this.player.muted = !this.player.muted;
                    this.updateMuteIcon(this.player.muted);
                },
                updateMuteIcon: function (isMuted) {
                    const icon = this.element.querySelector('.reel-mute-icon');
                    if (icon) {
                        if (isMuted) {
                            icon.innerHTML = `<path fill="white" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                <line x1="1" y1="1" x2="23" y2="23" stroke="white" stroke-width="2"/>`;
                        } else {
                            icon.innerHTML = `<path fill="white" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                <path fill="white" d="M5 12v2h2l5-5v10l-5-5v2"/>
                                <path fill="white" d="M17 10v4h2v-4"/>`;
                        }
                    }
                },
                mounted: function (player) {
                    player.on('mute', () => this.updateMuteIcon(true));
                    player.on('unmute', () => this.updateMuteIcon(false));
                    this.updateMuteIcon(this.player.muted);
                }
            },
            // Quality selector for reels
            {
                name: 'reelQuality',
                position: 'top',
                html: `<svg width="24" height="24" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" fill="none" stroke="white" stroke-width="2"/>
                    <text x="12" y="16" text-anchor="middle" fill="white" font-size="10" font-weight="bold">HD</text>
                </svg>`,
                style: {
                    cursor: 'pointer'
                },
                click: function () {
                    // Toggle quality menu
                    const settings = this.player.setting;
                    if (settings.show) {
                        settings.hide();
                    } else {
                        settings.show();
                    }
                }
            },
            // Custom progress bar for reels
            {
                name: 'reelProgress',
                position: 'top',
                html: `<div style="width: 100%; height: 3px; background: rgba(255,255,255,0.3); cursor: pointer;">
                    <div class="reel-progress-fill" style="width: 0%; height: 100%; background: #FF0000; transition: width 0.1s;"></div>
                </div>`,
                style: {
                    position: 'absolute',
                    top: '0',
                    left: '0',
                    right: '0',
                    zIndex: 5
                },
                click: function (event) {
                    const rect = this.element.getBoundingClientRect();
                    const percentage = (event.clientX - rect.left) / rect.width;
                    this.player.currentTime = percentage * this.player.duration;
                },
                updateProgress: function (currentTime, duration) {
                    const fill = this.element.querySelector('.reel-progress-fill');
                    if (fill && duration > 0) {
                        fill.style.width = `${(currentTime / duration) * 100}%`;
                    }
                },
                mounted: function (player) {
                    player.on('timeupdate', () => {
                        this.updateProgress(player.currentTime, player.duration);
                    });
                }
            }
        ],
        layers: [
            // Title overlay
            {
                name: 'reelTitle',
                html: `<div style="position: absolute; bottom: 80px; left: 20px; right: 80px; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; line-height: 1.3;">${title}</h3>
                </div>`,
                style: {
                    position: 'absolute',
                    bottom: '80px',
                    left: '20px',
                    right: '80px',
                    zIndex: 20
                }
            }
        ]
    };

    // Create the player
    const art = new Artplayer(reelOptions);

    // Store reference globally for this container
    window[`reelPlayer_${containerId}`] = art;

    // Event handlers
    if (onPlay) {
        art.on('play', onPlay);
    }
    if (onPause) {
        art.on('pause', onPause);
    }
    if (onEnded) {
        art.on('ended', onEnded);
    }
    if (onTimeUpdate) {
        art.on('timeupdate', onTimeUpdate);
    }

    // Handle quality change
    if (normalizedQualities.length > 0) {
        art.on('qualityStart', q => {
            const qData = normalizedQualities.find(qq => qq.html === q.html);
            if (qData?.url) {
                art.url = qData.url;
            }
        });
    }

    // Track events
    art.on('play', () => {
        try {
            fetch('/api/video-analytics', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_type: 'play', video_id: videoId, timestamp: new Date().toISOString() })
            }).catch(() => { });
        } catch (e) { }
    });

    art.on('pause', () => {
        try {
            fetch('/api/video-analytics', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_type: 'pause', video_id: videoId, timestamp: new Date().toISOString() })
            }).catch(() => { });
        } catch (e) { }
    });

    art.on('ended', () => {
        try {
            fetch('/api/video-analytics', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_type: 'ended', video_id: videoId, timestamp: new Date().toISOString() })
            }).catch(() => { });
        } catch (e) { }
    });

    return art;
}

// Helper function to update reel player data
export function updateReelPlayer(containerId, reelData = {}) {
    const art = window[`reelPlayer_${containerId}`];
    if (!art) {
        return initReelPlayer(containerId, reelData);
    }

    if (reelData.videoUrl) {
        art.url = reelData.videoUrl;
    }
    if (reelData.poster) {
        art.poster = reelData.poster;
    }
    if (reelData.autoplay !== undefined) {
        art.autoplay = reelData.autoplay;
    }
    if (reelData.muted !== undefined) {
        art.muted = reelData.muted;
    }
    if (reelData.loop !== undefined) {
        art.loop = reelData.loop;
    }

    return art;
}

// Helper function to destroy reel player
export function destroyReelPlayer(containerId) {
    const art = window[`reelPlayer_${containerId}`];
    if (art) {
        art.destroy();
        window[`reelPlayer_${containerId}`] = null;
    }
}
