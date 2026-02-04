import Artplayer from 'artplayer';
import artplayerPluginAds from 'artplayer-plugin-ads';
import artplayerPluginVast from 'artplayer-plugin-vast';
import artplayerPluginAmbilight from 'artplayer-plugin-ambilight';

const AUTOPLAY_KEY = 'autoplayNextEnabled';
window.autoplayNextEnabled = JSON.parse(localStorage.getItem(AUTOPLAY_KEY) || 'true');

function getAutoplaySettings() {
    const saved = localStorage.getItem('autoplayNextEnabled');
    if (saved !== null) {
        return JSON.parse(saved);
    }
    return true;
}

window.autoplayNextEnabled = getAutoplaySettings();

window.updateAutoplayNext = function(enabled) {
    window.autoplayNextEnabled = enabled;
    localStorage.setItem('autoplayNextEnabled', String(enabled));
};

export function initArtPlayer(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return null;

    const videoUrl = container.dataset.videoSrc;
    const poster = container.dataset.poster;
    const videoId = parseInt(container.dataset.videoId);
    const videoTitle = container.dataset.videoTitle;
    const playerColor = container.dataset.playerColor || '#FF0000';
    const language = container.dataset.language || 'it';
    
    // Playlist support
    const playlistId = container.dataset.playlistId || null;
    let playlistIndex = parseInt(container.dataset.playlistIndex || '-1');
    let playlistVideos = [];
    
    try {
        if (container.dataset.playlistVideos) {
            playlistVideos = JSON.parse(container.dataset.playlistVideos);
        }
    } catch (e) {
        playlistVideos = [];
    }
    
    const nextVideoUrl = container.dataset.nextVideoUrl || null;
    const nextVideoPoster = container.dataset.nextVideoPoster || null;
    const nextVideoTitle = container.dataset.nextVideoTitle || null;

    let qualities = [];
    try { if (container.dataset.qualities) qualities = JSON.parse(container.dataset.qualities); } catch { }

    const qualityOrder = { '2160p': 1, '1440p': 2, '1080p': 3, '720p': 4, '480p': 5, '360p': 6, 'original': 7 };
    const normalizedQualities = (qualities || [])
        .sort((a, b) => (qualityOrder[a.quality] || 99) - (qualityOrder[b.quality] || 99))
        .map((q, idx) => ({
            html: String(q.html),
            url: String(q.url),
            default: q.default ?? idx === 0
        }));

    let currentVideoUrl = normalizedQualities.find(q => q.default)?.url;
    if (!currentVideoUrl || currentVideoUrl === '') {
        currentVideoUrl = 'https://www.w3schools.com/html/mov_bbb.mp4';
    }

    // Get external glow container
    const ambientGlowContainer = document.getElementById('ambientLightGlow');
    const ambientContainer = document.getElementById('ambientLightContainer');

    const defaultOptions = {
        container: container,
        url: currentVideoUrl,
        poster: poster,
        volume: 0.7,
        muted: false,
        autoplay: true,
        pip: true,
        autoSize: true,
        autoMini: true,
        screenshot: true,
        setting: true,
        loop: false,
        flip: true,
        playbackRate: true,
        aspectRatio: true,
        fullscreen: true,
        fullscreenWeb: true,
        miniProgressBar: true,
        theme: playerColor,
        lang: language,
        moreVideoAttr: { crossOrigin: 'anonymous', playsInline: true, preload: 'auto' },
        quality: normalizedQualities,
        subtitle: { tracks: [] },
        plugins: [
            artplayerPluginAmbilight({
                blur: '50px',
                opacity: 0.8,
                frequency: 20,
                duration: 0.5,
                // Use external container for the glow effect
                container: ambientGlowContainer,
                // If external container is available, use it
                host: ambientContainer || document.body
            })
        ]
    };

    // Function to handle video ended - playlist support
    function handleVideoEnded() {
        const isEnabled = window.autoplayNextEnabled;
        if (!isEnabled) return;

        // Use playlist first
        if (playlistId && playlistIndex >= 0 && playlistVideos.length > 0) {
            playlistIndex++;
            
            if (playlistIndex < playlistVideos.length) {
                const nextPlaylistVideo = playlistVideos[playlistIndex];
                if (nextPlaylistVideo && nextPlaylistVideo.url) {
                    console.log('Playing next video in playlist:', nextPlaylistVideo.title);
                    setTimeout(() => {
                        window.location.href = nextPlaylistVideo.url;
                    }, 500);
                    return;
                }
            }
        }

        // Fallback to regular next video
        if (!nextVideoUrl || nextVideoUrl === 'null' || nextVideoUrl === '' || nextVideoUrl === 'undefined') return;

        setTimeout(() => {
            window.location.href = nextVideoUrl;
        }, 500);
    }

    const createPlayer = (plugins = []) => {
        if (window.artplayerInstance) {
            window.artplayerInstance.destroy();
            window.artplayerInstance = null;
        }

        const art = new Artplayer({ ...defaultOptions, url: currentVideoUrl });
        window.artplayerInstance = art;

        // Force autoplay on video start
        art.on('ready', () => {
            const playPromise = art.play();
            
            if (playPromise !== undefined) {
                playPromise.catch(() => {
                    art.muted = true;
                    art.play().then(() => {
                        setTimeout(() => {
                            art.muted = false;
                            art.volume = 0.7;
                        }, 1000);
                    }).catch(() => {
                        showMinimalPlayButton(art, container);
                    });
                });
            }
        });

        // Add autoplay control
        addAutoplayControl(art, container, playerColor);

        if (normalizedQualities.length > 0) {
            art.on('qualityStart', q => {
                const qData = normalizedQualities.find(qq => qq.html === q.html);
                if (qData?.url) art.url = qData.url;
            });
        }

        // Track video events
        art.on('play', () => trackVideoEvent('play', videoId));
        art.on('pause', () => trackVideoEvent('pause', videoId));

        // Handle video end
        art.on('ended', () => {
            trackVideoEvent('ended', videoId);
            handleVideoEnded();
        });

        art.on('complete', () => {
            handleVideoEnded();
        });

        // Polling backup for video end detection
        let pollInterval = null;
        let lastReportedTime = -1;
        
        art.on('play', () => {
            if (!pollInterval) {
                pollInterval = setInterval(() => {
                    if (!art) {
                        clearInterval(pollInterval);
                        pollInterval = null;
                        return;
                    }
                    
                    const currentTime = art.currentTime;
                    const duration = art.duration;
                    
                    if (!duration || duration <= 0) return;
                    
                    if (currentTime >= duration - 0.5 && lastReportedTime < duration - 0.5) {
                        handleVideoEnded();
                        lastReportedTime = currentTime;
                    }
                }, 250);
            }
        });
        
        art.on('ended', () => {
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        });

        return art;
    };

    fetch(`/api/video-ads/${videoId}?lang=${language}`)
        .then(r => r.json())
        .then(data => {
            const ads = data.success ? data.ads : [];
            const plugins = [];

            ads.filter(a => a.type === 'vast').forEach(vastAd => {
                const vastUrl = vastAd.video_url || vastAd.vast_url;
                if (!vastUrl) return;
                if (vastAd.position === 'post-roll') return;
                
                plugins.push(artplayerPluginVast({
                    url: vastUrl,
                    position: vastAd.position || 'pre-roll',
                    skipDelay: vastAd.skip_delay || 5,
                    skipText: language === 'it' ? 'Salta pubblicità' : 'Skip ads',
                    onAdStart: () => trackAdEvent('impression', videoId, 'vast', vastAd.id),
                    onAdSkip: () => trackAdEvent('skip', videoId, 'vast', vastAd.id),
                    onAdComplete: () => trackAdEvent('complete', videoId, 'vast', vastAd.id),
                    onAdError: () => trackAdEvent('error', videoId, 'vast', vastAd.id),
                }));
            });

            ads.filter(a => a.type === 'traditional').forEach(tradAd => {
                if (tradAd.position === 'post-roll') return;
                
                plugins.push(artplayerPluginAds({
                    ads: [{
                        url: tradAd.video_url,
                        type: 'video',
                        skip: tradAd.skip_delay || 5,
                        poster: tradAd.image_url,
                        name: tradAd.name,
                        position: tradAd.position || 'pre-roll',
                        duration: tradAd.duration || 15
                    }],
                    skipText: language === 'it' ? 'Salta pubblicità' : 'Skip ads'
                }));
            });

            createPlayer(plugins);
        })
        .catch(() => { createPlayer([]); });
}

function addAutoplayControl(art, container, playerColor) {
    art.controls.add({
        name: 'autoplayNext',
        position: 'left',
        html: `
        <div class="art-autoplay-container" style="display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-redo-alt"></i>
            <span class="autoplay-text">${window.autoplayNextEnabled ? 'ON' : 'OFF'}</span>
        </div>
        `,
        click: function (event, element) {
            window.autoplayNextEnabled = !window.autoplayNextEnabled;
            localStorage.setItem('autoplayNextEnabled', String(window.autoplayNextEnabled));

            const controlWrapper = element || this.element;
            if (!controlWrapper) return;

            const textSpan = controlWrapper.querySelector ? controlWrapper.querySelector('.autoplay-text') : null;
            const color = art.option.theme || playerColor;

            if (window.autoplayNextEnabled) {
                controlWrapper.style.color = color || '#FF0000';
                if (textSpan) {
                    textSpan.textContent = 'ON';
                    textSpan.style.fontWeight = 'bold';
                }
            } else {
                controlWrapper.style.color = 'white';
                if (textSpan) {
                    textSpan.textContent = 'OFF';
                    textSpan.style.fontWeight = 'normal';
                }
            }

            showAutoplayToast(art, window.autoplayNextEnabled, color, container);
        }
    });

    art.on('ready', () => {
        setTimeout(() => {
            const controlWrapper = container.querySelector('.art-autoplay-container');
            if (controlWrapper) {
                const color = art.option.theme || playerColor;
                if (window.autoplayNextEnabled) {
                    controlWrapper.style.color = color || '#FF0000';
                }
            }
        }, 200);
    });
}

function trackAdEvent(type, videoId, adType = 'vast', adId = null) {
    fetch('/api/ad-analytics', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_type: type, video_id: videoId, ad_type: adType, ad_id: adId, timestamp: new Date().toISOString() })
    }).catch(() => { });
}

function trackVideoEvent(type, videoId) {
    fetch('/api/video-analytics', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_type: type, video_id: videoId, timestamp: new Date().toISOString() })
    }).catch(() => { });
}

function showAutoplayToast(art, isEnabled, color, container) {
    if (!container) return;
    const existing = container.querySelector('.artplayer-autoplay-toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = 'artplayer-autoplay-toast';
    const lang = art.option.lang || 'it';
    toast.innerHTML = `<div style="padding:8px 14px;border-radius:6px;background: rgba(0,0,0,0.85);color:${isEnabled ? color : '#fff'};">
        ${isEnabled ? (lang === 'it' ? 'Autoplay prossimo video: ON' : 'Autoplay next video: ON')
            : (lang === 'it' ? 'Autoplay prossimo video: OFF' : 'Autoplay next video: OFF')}
    </div>`;
    toast.style.cssText = 'position:absolute;bottom:80px;left:50%;transform:translateX(-50%);z-index:300;';
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

function showMinimalPlayButton(art, container) {
    if (!container) return;
    
    const existing = container.querySelector('.minimal-play-btn');
    if (existing) existing.remove();
    
    const btn = document.createElement('button');
    btn.className = 'minimal-play-btn';
    btn.innerHTML = '<i class="fas fa-play"></i>';
    btn.style.cssText = `
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: 2px solid white;
        cursor: pointer;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s ease;
    `;
    
    btn.onclick = (e) => {
        e.stopPropagation();
        art.muted = false;
        art.volume = 0.7;
        art.play().then(() => {
            btn.remove();
        }).catch(() => {
            art.muted = true;
            art.play().then(() => {
                btn.remove();
                setTimeout(() => {
                    art.muted = false;
                    art.volume = 0.7;
                }, 2000);
            });
        });
    };
    
    btn.onmouseenter = () => {
        btn.style.background = 'rgba(220, 38, 38, 0.8)';
        btn.style.transform = 'translate(-50%, -50%) scale(1.1)';
    };
    btn.onmouseleave = () => {
        btn.style.background = 'rgba(0, 0, 0, 0.7)';
        btn.style.transform = 'translate(-50%, -50%)';
    };
    
    container.appendChild(btn);
}
