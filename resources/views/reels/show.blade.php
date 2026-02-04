<x-layout>
    @php
        // Passa le variabili del miniplayer al layout
        $layoutData = [
            'video' => $video ?? null,
            'restoreMiniPlayerState' => $restoreMiniPlayerState ?? false,
            'miniPlayerVideoId' => $miniPlayerVideoId ?? null,
            'miniPlayerStartTime' => $miniPlayerStartTime ?? 0,
            'miniPlayerLastVideo' => $miniPlayerLastVideo ?? null
        ];
    @endphp
    
    <!-- MiniPlayer Component -->
    <livewire:mini-player />
    
    <livewire:reel-show :video="$video" />

    <script>
        function highlightComment() {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#comment-')) {
                const commentId = hash.substring(9);
                const commentElement = document.getElementById('comment-' + commentId);
                if (commentElement) {
                    const commentsPanel = document.querySelector('.fixed.inset-y-0.right-0.w-full.md\\:w-96');
                    if (commentsPanel && commentsPanel.classList.contains('hidden')) {
                        const commentButtons = document.querySelectorAll('[wire\\:click*="toggleComments"]');
                        if (commentButtons.length > 0) {
                            commentButtons[0].click();
                            setTimeout(() => {
                                commentElement.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                highlightElement(commentElement);
                            }, 500);
                        }
                    } else {
                        commentElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        highlightElement(commentElement);
                    }
                } else {
                    setTimeout(highlightComment, 500);
                }
            }
        }

        function highlightElement(element) {
            element.style.transition = 'background-color 0.3s ease, box-shadow 0.3s ease';
            element.style.backgroundColor = 'rgba(239, 68, 68, 0.2)'; // Light red background
            element.style.boxShadow = '0 0 0 2px rgba(239, 68, 68, 0.5)'; // Red border
            element.style.borderRadius = '8px';
            setTimeout(function() {
                element.style.backgroundColor = '';
                element.style.boxShadow = '';
                element.style.borderRadius = '';
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            highlightComment();
        });

        document.addEventListener('livewire:updated', highlightComment);
    </script>
</x-layout>
