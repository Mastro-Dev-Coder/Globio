// Initialize global config from Blade
window.reelShowConfig = window.reelShowConfig || {
    currentIndex: 0,
    totalReels: 1,
    isMuted: false
};

// ReelScrollManager
class ReelScrollManager {
    constructor(config = {}) {
        this.currentIndex = config.currentIndex || window.reelShowConfig.currentIndex || 0;
        this.totalReels = config.totalReels || window.reelShowConfig.totalReels || 1;
        this.isScrolling = false;
        this.videoElements = new Map();
        this.isMuted = config.isMuted !== undefined ? config.isMuted : window.reelShowConfig.isMuted;
        this.observer = null;
        this.reelCurrentQualities = new Map();
        this.isLooping = false;
        this.init();
    }

    init() {
        this.cacheElements();
        this.setupIntersectionObserver();
        this.setupScrollEvents();
        this.bindEvents();
        this.setupKeyboardNavigation();
        this.initializeCurrentVideo();
        this.setupContextMenu();
    }

    cacheElements() {
        document.querySelectorAll('[id^="videoElement"]').forEach((video, index) => {
            this.videoElements.set(index, video);

            video.addEventListener('timeupdate', () => this.updateProgressBar(index));
            video.addEventListener('play', () => this.updatePlayButton(index, false));
            video.addEventListener('pause', () => this.updatePlayButton(index, true));

            video.addEventListener('ended', () => {
                if (this.isLooping) {
                    video.currentTime = 0;
                    video.play();
                } else if (index < this.totalReels - 1) {
                    this.switchToVideo(index + 1);
                }
            });
        });
    }

    bindEvents() {
        this.videoElements.forEach((video, index) => {
            video.addEventListener('click', (event) => {
                event.preventDefault();
                this.togglePlayPause(index);
            });
        });
    }

    togglePlayPause(index = this.currentIndex) {
        const video = this.videoElements.get(index);
        if (!video) return;

        if (video.paused) {
            const playPromise = video.play();
            if (playPromise !== undefined) {
                playPromise.catch(() => {});
            }
            this.updatePlayButton(index, false);
        } else {
            video.pause();
            this.updatePlayButton(index, true);
        }
    }

    toggleMute() {
        this.isMuted = !this.isMuted;
        this.videoElements.forEach(video => {
            video.muted = this.isMuted;
        });

        const muteIcon = document.getElementById('contextMuteIcon');
        const muteText = document.getElementById('contextMuteText');
        if (muteIcon) {
            muteIcon.className = this.isMuted ? 'fas fa-volume-mute text-gray-400 w-5 group-hover:text-white' :
                'fas fa-volume-up text-gray-400 w-5 group-hover:text-white';
        }
        if (muteText) {
            muteText.textContent = this.isMuted ? 'Attiva audio' : 'Disattiva audio';
        }
    }

    setupIntersectionObserver() {
        const options = {
            root: document.getElementById('reelsScrollContainer'),
            rootMargin: '0px',
            threshold: 0.7
        };
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const videoIndex = parseInt(entry.target.closest('[data-reel-index]')
                        .dataset.reelIndex);
                    if (videoIndex !== this.currentIndex) {
                        this.switchToVideo(videoIndex);
                    }
                }
            });
        }, options);

        document.querySelectorAll('[data-reel-index]').forEach(container => this.observer.observe(container));
    }

    setupScrollEvents() {
        const container = document.getElementById('reelsScrollContainer');
        if (!container) return;
        let scrollTimeout;
        container.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => this.handleScrollEnd(), 100);
        });
    }

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            const tagName = document.activeElement?.tagName || '';
            if (tagName === 'INPUT' || tagName === 'TEXTAREA' || tagName === 'SELECT') return;

            switch (e.key) {
                case 'ArrowUp':
                    e.preventDefault();
                    this.switchToVideo(this.currentIndex - 1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.switchToVideo(this.currentIndex + 1);
                    break;
                case ' ':
                    e.preventDefault();
                    this.togglePlayPause();
                    break;
                case 'm':
                case 'M':
                    this.toggleMute();
                    break;
                default:
                    break;
            }
        });
    }

    handleScrollEnd() {
        const container = document.getElementById('reelsScrollContainer');
        if (!container || this.isScrolling) return;
        const containerRect = container.getBoundingClientRect();
        const containerCenter = containerRect.top + containerRect.height / 2;
        let closestIndex = this.currentIndex;
        let minDistance = Infinity;

        document.querySelectorAll('[data-reel-index]').forEach(reel => {
            const reelRect = reel.getBoundingClientRect();
            const distance = Math.abs((reelRect.top + reelRect.height / 2) - containerCenter);
            if (distance < minDistance) {
                minDistance = distance;
                closestIndex = parseInt(reel.dataset.reelIndex);
            }
        });

        if (closestIndex !== this.currentIndex) {
            this.switchToVideo(closestIndex);
        }
    }

    switchToVideo(newIndex) {
        if (newIndex < 0 || newIndex >= this.totalReels || newIndex === this.currentIndex) return;

        const currentVideo = this.videoElements.get(this.currentIndex);
        if (currentVideo) {
            currentVideo.pause();
            this.updatePlayButton(this.currentIndex, true);
        }

        this.currentIndex = newIndex;

        const newVideo = this.videoElements.get(newIndex);
        if (newVideo) {
            newVideo.muted = this.isMuted;
            const playPromise = newVideo.play();
            if (playPromise !== undefined) {
                playPromise.catch(e => {
                    newVideo.muted = true;
                    newVideo.play().catch(e2 => {});
                });
            }
            this.updatePlayButton(newIndex, false);
        }

        this.updateInfoPanels();

        if (window.Livewire) {
            window.Livewire.dispatch('reelChanged', {
                index: newIndex,
                videoId: document.querySelector(`[data-reel-index="${newIndex}"]`).dataset.videoId
            });
        }
    }

    updateInfoPanels() {
        document.querySelectorAll('.reel-info-panel').forEach((panel, index) => {
            panel.classList.toggle('hidden', index !== this.currentIndex);
            panel.classList.toggle('block', index === this.currentIndex);
        });
    }

    initializeCurrentVideo() {
        const currentVideo = this.videoElements.get(this.currentIndex);
        if (currentVideo) {
            currentVideo.muted = this.isMuted;
            const playPromise = currentVideo.play();
            if (playPromise !== undefined) {
                playPromise.catch(e => {
                    currentVideo.muted = true;
                    currentVideo.play().catch(e2 => {});
                });
            }
            this.updatePlayButton(this.currentIndex, false);
        }
    }

    updatePlayButton(index, showPlay) {
        const btn = document.getElementById(`playPauseBtnCenter${index}`);
        const icon = document.getElementById(`playPauseIconCenter${index}`);
        if (btn && icon) {
            icon.className = showPlay ? 'fas fa-play text-white text-2xl ml-1' :
                'fas fa-pause text-white text-2xl';
            btn.classList.toggle('opacity-0', !showPlay);
        }
    }

    updateProgressBar(index) {
        const video = this.videoElements.get(index);
        const progressBar = document.getElementById(`progressBar${index}`);
        if (video && progressBar && video.duration) {
            progressBar.style.width = `${(video.currentTime / video.duration) * 100}%`;
        }
    }

    setupContextMenu() {
        document.querySelectorAll('[data-reel-index]').forEach(container => {
            container.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.showContextMenu(e.clientX, e.clientY);
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.hideContextMenu();
        });

        document.addEventListener('click', (e) => {
            const contextMenu = document.getElementById('contextMenu');
            if (contextMenu && !contextMenu.contains(e.target)) {
                this.hideContextMenu();
            }
        });
    }

    showContextMenu(x, y) {
        const contextMenu = document.getElementById('contextMenu');
        const backdrop = document.getElementById('contextMenuBackdrop');
        const content = document.getElementById('contextMenuContent');

        if (contextMenu && backdrop && content) {
            // Calculate position to avoid going off screen
            const menuWidth = 256;
            const menuHeight = 480;
            const padding = 10;

            let posX = x;
            let posY = y;

            if (posX + menuWidth + padding > window.innerWidth) {
                posX = window.innerWidth - menuWidth - padding;
            }

            if (posY + menuHeight + padding > window.innerHeight) {
                posY = window.innerHeight - menuHeight - padding;
            }

            if (posX < padding) {
                posX = padding;
            }

            if (posY < padding) {
                posY = padding;
            }

            content.style.left = posX + 'px';
            content.style.top = posY + 'px';

            contextMenu.classList.remove('hidden');
            backdrop.classList.remove('hidden');

            requestAnimationFrame(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            });

            const watchLaterText = document.getElementById('contextWatchLaterText');
            const watchLaterIcon = document.getElementById('contextWatchLaterIcon');
            if (watchLaterText) {
                const isInWatchLater = this.isCurrentInWatchLater();
                watchLaterText.textContent = isInWatchLater ? 'Rimuovi da Guarda più tardi' :
                    'Aggiungi a Guarda più tardi';
                if (watchLaterIcon) {
                    watchLaterIcon.className = isInWatchLater ?
                        'fas fa-bookmark text-green-400 w-5 transition-colors' :
                        'fas fa-bookmark text-gray-400 w-5 transition-colors';
                }
            }

            const muteText = document.getElementById('contextMuteText');
            const muteIcon = document.getElementById('contextMuteIcon');
            if (muteText) {
                muteText.textContent = this.isMuted ? 'Attiva audio' : 'Disattiva audio';
            }
            if (muteIcon) {
                muteIcon.className = this.isMuted ?
                    'fas fa-volume-mute text-gray-400 w-5 group-hover:text-white' :
                    'fas fa-volume-up text-gray-400 w-5 group-hover:text-white';
            }

            const loopCheck = document.getElementById('loopCheck');
            if (loopCheck) {
                loopCheck.classList.toggle('hidden', !this.isLooping);
            }

            const speedDisplay = document.getElementById('currentSpeedDisplay');
            const video = this.videoElements.get(this.currentIndex);
            if (speedDisplay && video) {
                speedDisplay.textContent = video.playbackRate + 'x';
            }
        }
    }

    hideContextMenu() {
        const contextMenu = document.getElementById('contextMenu');
        const backdrop = document.getElementById('contextMenuBackdrop');
        const content = document.getElementById('contextMenuContent');

        if (contextMenu && backdrop && content) {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                contextMenu.classList.add('hidden');
                backdrop.classList.add('hidden');
            }, 200);
        }
    }

    isCurrentInWatchLater() {
        const saveIcon = document.querySelector('[wire\\:click="toggleWatchLater"] i');
        return saveIcon && saveIcon.classList.contains('text-green-400');
    }

    getCurrentReelData() {
        const currentContainer = document.querySelector(`[data-reel-index="${this.currentIndex}"]`);
        if (!currentContainer) return null;
        const videoId = currentContainer.dataset.videoId;
        const videoElement = currentContainer.querySelector('video');
        const sourceElement = videoElement?.querySelector('source');
        return {
            videoId,
            videoSrc: sourceElement?.src || '',
            index: this.currentIndex
        };
    }

    toggleLoop() {
        this.isLooping = !this.isLooping;
        const loopCheck = document.getElementById('loopCheck');
        if (loopCheck) {
            loopCheck.classList.toggle('hidden', !this.isLooping);
        }
        this.hideContextMenu();
        window.showToast(this.isLooping ? 'Loop attivato' : 'Loop disattivato', 'info');
    }
}

// Global functions
window.togglePlayPause = function() {
    if (window.reelManager) window.reelManager.togglePlayPause();
};

window.toggleMute = function() {
    if (window.reelManager) window.reelManager.toggleMute();
};

window.toggleReelMenu = function() {
    if (!window.reelManager) return;
    const button = document.querySelector('[onclick="window.toggleReelMenu()"]');
    if (button) {
        const rect = button.getBoundingClientRect();
        window.reelManager.showContextMenu(rect.left + rect.width / 2, rect.top + rect.height / 2);
    } else {
        window.reelManager.showContextMenu(window.innerWidth / 2, window.innerHeight / 2);
    }
};

window.navigateReel = function(direction) {
    if (window.reelManager) {
        const newIndex = window.reelManager.currentIndex + direction;
        if (newIndex >= 0 && newIndex < window.reelManager.totalReels) {
            window.reelManager.switchToVideo(newIndex);
        }
    }
};

window.toggleReelQualityMenu = function(videoIndex) {
    const menu = document.getElementById(`reelQualityMenu${videoIndex}`);
    if (menu) {
        menu.classList.toggle('hidden');
        menu.classList.toggle('flex', !menu.classList.contains('hidden'));
    }
};

window.selectReelQuality = function(quality, videoIndex) {
    if (window.reelManager) {
        window.reelManager.reelCurrentQualities.set(videoIndex, quality);
    }
    const menu = document.getElementById(`reelQualityMenu${videoIndex}`);
    if (menu) {
        menu.classList.add('hidden');
        menu.classList.remove('flex');
    }
    window.showToast(`Qualità: ${quality}`, 'success');
};

window.hideContextMenu = function() {
    if (window.reelManager) window.reelManager.hideContextMenu();
};

window.toggleWatchLaterFromContext = function() {
    const btn = document.querySelector('[wire\\:click="toggleWatchLater"]');
    if (btn) btn.click();
    window.hideContextMenu();
};

window.shareCurrentReel = function() {
    if (navigator.share && window.reelManager) {
        const data = window.reelManager.getCurrentReelData();
        if (data) {
            navigator.share({
                title: 'Guarda questo reel',
                url: window.location.origin + '/reel/show/' + data.videoId
            });
        }
    } else {
        window.copyVideoLink();
    }
    window.hideContextMenu();
};

window.copyVideoLink = function() {
    if (window.reelManager) {
        const data = window.reelManager.getCurrentReelData();
        if (data) {
            navigator.clipboard.writeText(window.location.origin + '/reel/show/' + data.videoId)
                .then(() => window.showToast('Link copiato!', 'success'))
                .catch(() => window.showToast('Errore', 'error'));
        }
    }
    window.hideContextMenu();
};

window.downloadVideo = function() {
    if (window.reelManager) {
        const video = window.reelManager.videoElements.get(window.reelManager.currentIndex);
        const src = video?.querySelector('source')?.src;
        if (src) {
            const link = document.createElement('a');
            link.href = src;
            link.download = 'reel.mp4';
            link.click();
            window.showToast('Download iniziato', 'success');
        }
    }
    window.hideContextMenu();
};

window.openInNewTab = function() {
    if (window.reelManager) {
        const data = window.reelManager.getCurrentReelData();
        if (data) window.open('/reel/show/' + data.videoId, '_blank');
    }
    window.hideContextMenu();
};

window.openQualityMenu = function() {
    if (window.reelManager) {
        window.toggleReelQualityMenu(window.reelManager.currentIndex);
    }
    window.hideContextMenu();
};

window.openSpeedMenu = function() {
    const speeds = [0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2];
    const currentVideo = window.reelManager?.videoElements.get(window.reelManager.currentIndex);
    const currentSpeed = currentVideo ? currentVideo.playbackRate : 1;

    let menuHtml = `
        <div id="speedMenu" class="fixed inset-0 z-50" onclick="window.closeSpeedMenu(event)">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute bg-gray-900/95 backdrop-blur-xl border border-gray-700/50 rounded-xl shadow-2xl min-w-48 overflow-hidden" 
                 style="left: 50%; top: 50%; transform: translate(-50%, -50%);">
                <div class="py-2">
                    <div class="px-4 py-2 border-b border-gray-700/50">
                        <p class="text-white font-medium text-sm">Velocità riproduzione</p>
                    </div>
    `;

    speeds.forEach(speed => {
        const isActive = speed === currentSpeed;
        menuHtml += `
            <button onclick="window.setPlaybackSpeed(${speed})" 
                class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-all duration-150 flex items-center gap-4 group">
                <span class="text-sm ${isActive ? 'text-white font-medium' : 'text-gray-400'}">${speed}x</span>
                ${isActive ? '<i class="fas fa-check ml-auto text-green-400"></i>' : '<i class="fas fa-chevron-right ml-auto text-gray-600 text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>'}
            </button>
        `;
    });

    menuHtml += `
                <div class="border-t border-gray-700/50 mt-2"></div>
                <button onclick="window.setPlaybackSpeed('normal')" class="w-full px-4 py-3 text-left text-white hover:bg-gray-800/80 transition-all duration-150 flex items-center gap-4 group">
                    <span class="text-sm text-gray-400">Normale</span>
                    ${currentSpeed === 1 ? '<i class="fas fa-check ml-auto text-green-400"></i>' : ''}
                </button>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', menuHtml);
    window.hideContextMenu();
};

window.closeSpeedMenu = function(event) {
    if (event.target.id === 'speedMenu') {
        const menu = document.getElementById('speedMenu');
        if (menu) menu.remove();
    }
};

window.setPlaybackSpeed = function(speed) {
    const video = window.reelManager?.videoElements.get(window.reelManager.currentIndex);
    if (video) {
        video.playbackRate = speed;
        window.showToast(`Velocità: ${speed}x`, 'info');
    }
    const menu = document.getElementById('speedMenu');
    if (menu) menu.remove();
};

window.toggleAudioTrack = function() {
    window.showToast('Traccia audio: solo originale', 'info');
    window.hideContextMenu();
};

window.copyVideoTitle = function() {
    if (window.reelManager) {
        const currentContainer = document.querySelector(
            `[data-reel-index="${window.reelManager.currentIndex}"]`);
        if (currentContainer) {
            const titleElement = currentContainer.querySelector('.line-clamp-2') ||
                document.querySelector('.reel-info-panel:not(.hidden) h2');
            const title = titleElement ? titleElement.textContent.trim() : '';
            if (title) {
                navigator.clipboard.writeText(title)
                    .then(() => window.showToast('Titolo copiato!', 'success'))
                    .catch(() => window.showToast('Errore', 'error'));
            }
        }
    }
    window.hideContextMenu();
};

window.goToChannel = function() {
    if (window.reelManager) {
        const currentContainer = document.querySelector(
            `[data-reel-index="${window.reelManager.currentIndex}"]`);
        if (currentContainer) {
            const channelLink = currentContainer.querySelector(
                '.reel-info-panel:not(.hidden) a[href*="/channel/"]');
            if (channelLink) {
                window.location.href = channelLink.href;
            }
        }
    }
    window.hideContextMenu();
};

window.showVideoInfo = function() {
    if (window.reelManager) {
        const currentContainer = document.querySelector(
            `[data-reel-index="${window.reelManager.currentIndex}"]`);
        if (currentContainer) {
            const videoId = currentContainer.dataset.videoId;
            window.location.href = `/video/${videoId}`;
        }
    }
    window.hideContextMenu();
};

window.toggleLoop = function() {
    if (window.reelManager) {
        window.reelManager.toggleLoop();
    }
};

window.notInterested = function() {
    const btn = document.querySelector('[wire\\:click="notInterested"]');
    if (btn) btn.click();
    window.hideContextMenu();
};

window.reportVideo = function() {
    const btn = document.querySelector('[wire\\:click="reportVideo"]');
    if (btn) btn.click();
    window.hideContextMenu();
};

window.showToast = function(message, type) {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    toast.className =
        `fixed bottom-24 left-1/2 transform -translate-x-1/2 ${colors[type]} text-white px-6 py-3 rounded-full shadow-xl z-50 flex items-center gap-3`;
    toast.innerHTML = `<span class="font-medium">${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const init = () => {
        if (window.Livewire) {
            window.reelManager = new ReelScrollManager(window.reelShowConfig);
        } else {
            setTimeout(init, 100);
        }
    };
    init();
});

// Add CSS
const style = document.createElement('style');
style.textContent = `
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .snap-y { scroll-snap-type: y mandatory; }
    .snap-start { scroll-snap-align: start; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    @keyframes slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
    .animate-slide-up { animation: slide-up 0.3s ease-out; }
    .scale-95 { transform: scale(0.95); }
    .scale-100 { transform: scale(1); }
    .opacity-0 { opacity: 0; }
    .opacity-100 { opacity: 1; }
    .pointer-events-none { pointer-events: none; }
    .pointer-events-auto { pointer-events: auto; }
`;
document.head.appendChild(style);
