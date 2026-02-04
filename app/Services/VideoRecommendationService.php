<?php

namespace App\Services;

use App\Models\Video;
use App\Models\User;
use App\Models\WatchHistory;
use App\Models\Subscription;
use App\Models\Like;
use App\Models\Comment;
use App\Models\UserPreference;
use App\Models\Playlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servizio per la generazione di raccomandazioni video personalizzate
 * basate su cronologia, canali sottoscritti, tag preferiti e engagement.
 *
 * Pesi utilizzati:
 * - Cronologia: 50%
 * - Canali: 20%
 * - Tag: 20%
 * - Engagement: 10%
 */
class VideoRecommendationService
{
    /**
     * Calcola la similarità Jaccard tra due insiemi di tag
     *
     * @param array $tags1 Primo insieme di tag
     * @param array $tags2 Secondo insieme di tag
     * @return float Similarità Jaccard (0-1)
     */
    public function calculateJaccardSimilarity(array $tags1, array $tags2): float
    {
        if (empty($tags1) || empty($tags2)) {
            return 0.0;
        }

        $intersection = array_intersect($tags1, $tags2);
        $union = array_unique(array_merge($tags1, $tags2));

        return count($intersection) / count($union);
    }

    /**
     * Recupera la cronologia di visione dell'utente
     *
     * @param int $userId ID dell'utente
     * @return Collection Collezione di WatchHistory con relazioni
     */
    public function getUserWatchHistory(int $userId): Collection
    {
        return WatchHistory::with(['video'])
            ->where('user_id', $userId)
            ->whereHas('video', function ($query) {
                $query->published();
            })
            ->orderBy('last_watched_at', 'desc')
            ->get();
    }

    /**
     * Recupera i canali sottoscritti dall'utente
     *
     * @param int $userId ID dell'utente
     * @return Collection Collezione di User (canali)
     */
    public function getUserSubscribedChannels(int $userId): Collection
    {
        return User::whereHas('subscribers', function ($query) use ($userId) {
            $query->where('subscriber_id', $userId);
        })->get();
    }

    /**
     * Recupera i tag preferiti dell'utente basati sulla cronologia
     *
     * @param int $userId ID dell'utente
     * @return array Array di tag con frequenza
     */
    public function getUserPreferredTags(int $userId): array
    {
        $watchHistory = $this->getUserWatchHistory($userId);

        $tagCounts = [];
        foreach ($watchHistory as $history) {
            $tags = $history->video->tags ?? [];
            foreach ($tags as $tag) {
                $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
            }
        }

        // Ordina per frequenza decrescente
        arsort($tagCounts);
        return $tagCounts;
    }

    /**
     * Recupera i dati di engagement dell'utente (like e commenti)
     *
     * @param int $userId ID dell'utente
     * @return Collection Collezione di video con engagement
     */
    public function getUserEngagement(int $userId): Collection
    {
        // Video piaciuti
        $likedVideos = Video::published()
            ->whereHas('likes', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('reaction', 'like');
            })
            ->get();

        // Video commentati
        $commentedVideos = Video::published()
            ->whereHas('comments', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        return $likedVideos->merge($commentedVideos)->unique('id');
    }

    /**
     * Genera raccomandazioni video personalizzate per l'utente
     *
     * @param int $userId ID dell'utente
     * @param int $limit Numero massimo di raccomandazioni
     * @return Collection Collezione di Video raccomandati
     */
    public function getRecommendations(int $userId, int $limit = 20): Collection
    {
        // Recupera dati utente in batch per ottimizzazione
        $watchHistory = $this->getUserWatchHistory($userId);
        $subscribedChannels = $this->getUserSubscribedChannels($userId);
        $preferredTags = $this->getUserPreferredTags($userId);
        $engagementVideos = $this->getUserEngagement($userId);

        // Se l'utente non ha dati, restituisci video popolari
        if ($watchHistory->isEmpty() && $subscribedChannels->isEmpty() && empty($preferredTags) && $engagementVideos->isEmpty()) {
            return $this->getPopularVideos($limit);
        }

        // Ottieni candidati video (tutti pubblicati esclusi quelli già visti)
        $watchedVideoIds = $watchHistory->pluck('video_id')->toArray();
        $candidates = Video::published()
            ->whereNotIn('id', $watchedVideoIds)
            ->with('user')
            ->get();

        // Calcola punteggi per ogni candidato
        $scoredVideos = $candidates->map(function ($video) use ($watchHistory, $subscribedChannels, $preferredTags, $engagementVideos) {
            $score = 0;

            // Punteggio cronologia (50%): basato su similarità con video guardati
            $historyScore = $this->calculateHistoryScore($video, $watchHistory);
            $score += $historyScore * 0.5;

            // Punteggio canali (20%): bonus se dal canale sottoscritto
            $channelScore = $this->calculateChannelScore($video, $subscribedChannels);
            $score += $channelScore * 0.2;

            // Punteggio tag (20%): similarità Jaccard con tag preferiti
            $tagScore = $this->calculateTagScore($video, $preferredTags);
            $score += $tagScore * 0.2;

            // Punteggio engagement (10%): bonus se simile a video piaciuti/commentati
            $engagementScore = $this->calculateEngagementScore($video, $engagementVideos);
            $score += $engagementScore * 0.1;

            return [
                'video' => $video,
                'score' => $score
            ];
        });

        // Ordina per punteggio decrescente e limita risultati
        $sortedVideos = $scoredVideos->sortByDesc('score')->take($limit);

        return $sortedVideos->pluck('video');
    }

    /**
     * Calcola il punteggio basato sulla cronologia
     */
    private function calculateHistoryScore(Video $video, Collection $watchHistory): float
    {
        if ($watchHistory->isEmpty()) {
            return 0;
        }

        $totalScore = 0;
        foreach ($watchHistory as $history) {
            $similarity = $this->calculateJaccardSimilarity(
                $video->tags ?? [],
                $history->video->tags ?? []
            );

            // Peso basato su completamento e recency
            $completionWeight = $history->completed ? 1 : ($history->watched_duration / $history->total_duration);
            $recencyWeight = max(0, 1 - (now()->diffInDays($history->last_watched_at) / 30)); // Decadimento su 30 giorni

            $totalScore += $similarity * $completionWeight * $recencyWeight;
        }

        return $totalScore / $watchHistory->count();
    }

    /**
     * Calcola il punteggio basato sui canali sottoscritti
     */
    private function calculateChannelScore(Video $video, Collection $subscribedChannels): float
    {
        return $subscribedChannels->contains('id', $video->user_id) ? 1 : 0;
    }

    /**
     * Calcola il punteggio basato sui tag preferiti
     */
    private function calculateTagScore(Video $video, array $preferredTags): float
    {
        if (empty($preferredTags)) {
            return 0;
        }

        $videoTags = $video->tags ?? [];
        if (empty($videoTags)) {
            return 0;
        }

        // Calcola similarità pesata basata sulla frequenza dei tag preferiti
        $score = 0;
        $totalWeight = array_sum($preferredTags);

        foreach ($videoTags as $tag) {
            if (isset($preferredTags[$tag])) {
                $score += $preferredTags[$tag] / $totalWeight;
            }
        }

        return min($score, 1); // Normalizza a 0-1
    }

    /**
     * Calcola il punteggio basato sull'engagement
     */
    private function calculateEngagementScore(Video $video, Collection $engagementVideos): float
    {
        if ($engagementVideos->isEmpty()) {
            return 0;
        }

        $maxSimilarity = 0;
        foreach ($engagementVideos as $engagedVideo) {
            $similarity = $this->calculateJaccardSimilarity(
                $video->tags ?? [],
                $engagedVideo->tags ?? []
            );
            $maxSimilarity = max($maxSimilarity, $similarity);
        }

        return $maxSimilarity;
    }

    /**
     * Salva gli interessi dell'utente nelle preferenze
     *
     * @param int $userId ID dell'utente
     */
    public function updateUserInterests(int $userId): void
    {
        $preferredTags = $this->getUserPreferredTags($userId);
        $watchHistory = $this->getUserWatchHistory($userId);

        // Salva tag preferiti
        UserPreference::setValue($userId, 'recommendations', 'preferred_tags', $preferredTags);

        // Salva categorie preferite basate sui tag
        $preferredCategories = $this->extractCategoriesFromTags($preferredTags);
        UserPreference::setValue($userId, 'recommendations', 'preferred_categories', $preferredCategories);

        // Salva timestamp dell'ultimo aggiornamento
        UserPreference::setValue($userId, 'recommendations', 'last_updated', now()->toISOString());

        // Salva statistiche di visione
        $stats = [
            'total_videos_watched' => $watchHistory->count(),
            'avg_watch_completion' => $watchHistory->avg(function ($item) {
                return $item->completed ? 100 : ($item->watched_duration / $item->total_duration * 100);
            }),
            'most_watched_category' => $this->getMostWatchedCategory($watchHistory)
        ];
        UserPreference::setValue($userId, 'recommendations', 'watch_stats', $stats);
    }

    /**
     * Recupera gli interessi salvati dell'utente
     *
     * @param int $userId ID dell'utente
     * @return array Interessi dell'utente
     */
    public function getUserInterests(int $userId): array
    {
        $lastUpdated = UserPreference::getValue($userId, 'recommendations', 'last_updated');

        // Se non aggiornato da più di 24 ore, aggiorna
        if (!$lastUpdated || now()->diffInHours($lastUpdated) > 24) {
            $this->updateUserInterests($userId);
        }

        return [
            'preferred_tags' => UserPreference::getValue($userId, 'recommendations', 'preferred_tags', []),
            'preferred_categories' => UserPreference::getValue($userId, 'recommendations', 'preferred_categories', []),
            'watch_stats' => UserPreference::getValue($userId, 'recommendations', 'watch_stats', []),
            'last_updated' => UserPreference::getValue($userId, 'recommendations', 'last_updated')
        ];
    }

    /**
     * Estrae categorie dai tag preferiti
     *
     * @param array $preferredTags Tag con frequenze
     * @return array Categorie con punteggi
     */
    private function extractCategoriesFromTags(array $preferredTags): array
    {
        $categories = [];
        $categoryMapping = [
            'tech' => ['programmazione', 'coding', 'javascript', 'php', 'laravel', 'vue', 'react', 'python', 'java', 'web', 'software'],
            'gaming' => ['gaming', 'game', 'videogame', 'streamer', 'twitch', 'esports'],
            'music' => ['music', 'musica', 'song', 'canzone', 'artist', 'artista', 'concert', 'live'],
            'sports' => ['sports', 'sport', 'football', 'calcio', 'basketball', 'tennis', 'olympics'],
            'education' => ['education', 'tutorial', 'howto', 'learn', 'course', 'lesson', 'study'],
            'entertainment' => ['entertainment', 'fun', 'comedy', 'movie', 'film', 'tv', 'series', 'show'],
            'lifestyle' => ['lifestyle', 'fashion', 'beauty', 'food', 'cooking', 'travel', 'vlog'],
            'news' => ['news', 'notizie', 'politics', 'politica', 'current', 'events']
        ];

        foreach ($preferredTags as $tag => $frequency) {
            $tagLower = strtolower($tag);
            foreach ($categoryMapping as $category => $keywords) {
                if (in_array($tagLower, $keywords)) {
                    $categories[$category] = ($categories[$category] ?? 0) + $frequency;
                }
            }
        }

        arsort($categories);
        return $categories;
    }

    /**
     * Ottiene la categoria più vista dall'utente
     *
     * @param Collection $watchHistory Cronologia di visione
     * @return string|null Categoria più vista
     */
    private function getMostWatchedCategory(Collection $watchHistory): ?string
    {
        $categoryCounts = [];
        foreach ($watchHistory as $history) {
            $tags = $history->video->tags ?? [];
            foreach ($tags as $tag) {
                $tagLower = strtolower($tag);
                // Mappa tag a categorie
                $category = $this->mapTagToCategory($tagLower);
                if ($category) {
                    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
                }
            }
        }

        if (empty($categoryCounts)) {
            return null;
        }

        arsort($categoryCounts);
        return array_key_first($categoryCounts);
    }

    /**
     * Mappa un tag a una categoria
     *
     * @param string $tag Tag da mappare
     * @return string|null Categoria corrispondente
     */
    private function mapTagToCategory(string $tag): ?string
    {
        $mapping = [
            'tech' => ['programmazione', 'coding', 'javascript', 'php', 'laravel', 'vue', 'react', 'python', 'java', 'web', 'software'],
            'gaming' => ['gaming', 'game', 'videogame', 'streamer', 'twitch', 'esports'],
            'music' => ['music', 'musica', 'song', 'canzone', 'artist', 'artista', 'concert', 'live'],
            'sports' => ['sports', 'sport', 'football', 'calcio', 'basketball', 'tennis', 'olympics'],
            'education' => ['education', 'tutorial', 'howto', 'learn', 'course', 'lesson', 'study'],
            'entertainment' => ['entertainment', 'fun', 'comedy', 'movie', 'film', 'tv', 'series', 'show'],
            'lifestyle' => ['lifestyle', 'fashion', 'beauty', 'food', 'cooking', 'travel', 'vlog'],
            'news' => ['news', 'notizie', 'politics', 'politica', 'current', 'events']
        ];

        foreach ($mapping as $category => $keywords) {
            if (in_array($tag, $keywords)) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Genera raccomandazioni usando interessi salvati
     *
     * @param int $userId ID dell'utente
     * @param int $limit Numero massimo di raccomandazioni
     * @param array $filters Filtri aggiuntivi (tags, duration, is_reel)
     * @return Collection Collezione di Video raccomandati
     */
    public function getRecommendationsFromInterests(int $userId, int $limit = 20, array $filters = []): Collection
    {
        $interests = $this->getUserInterests($userId);
        $watchHistory = $this->getUserWatchHistory($userId);
        $watchedVideoIds = $watchHistory->pluck('video_id')->toArray();

        // Se non ha interessi, usa popolari
        if (empty($interests['preferred_tags']) && empty($interests['preferred_categories'])) {
            return $this->getPopularVideos($limit);
        }

        // Query base per candidati
        $query = Video::published()
            ->whereNotIn('id', $watchedVideoIds)
            ->with('user');

        // Applica filtri
        if (isset($filters['is_reel'])) {
            $query->where('is_reel', $filters['is_reel']);
        }

        if (isset($filters['tags'])) {
            $query->whereJsonContains('tags', $filters['tags']);
        }

        if (isset($filters['duration'])) {
            switch ($filters['duration']) {
                case 'short':
                    $query->where('duration', '<', 300);
                    break;
                case 'medium':
                    $query->whereBetween('duration', [300, 1200]);
                    break;
                case 'long':
                    $query->where('duration', '>', 1200);
                    break;
            }
        }

        // Aggiungi condizioni basate su tag preferiti se non ci sono filtri specifici
        if (!empty($interests['preferred_tags']) && !isset($filters['tags'])) {
            $preferredTagKeys = array_keys($interests['preferred_tags']);
            $query->where(function ($q) use ($preferredTagKeys) {
                foreach ($preferredTagKeys as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        $candidates = $query->get();

        // Se non abbastanza candidati, aggiungi video popolari
        if ($candidates->count() < $limit) {
            $popularVideos = $this->getPopularVideos($limit - $candidates->count());
            $candidates = $candidates->merge($popularVideos);
        }

        // Ordina per rilevanza
        $scoredVideos = $candidates->map(function ($video) use ($interests) {
            $score = 0;

            // Punteggio basato su tag preferiti
            if (!empty($interests['preferred_tags'])) {
                $videoTags = $video->tags ?? [];
                $tagScore = 0;
                foreach ($videoTags as $tag) {
                    if (isset($interests['preferred_tags'][$tag])) {
                        $tagScore += $interests['preferred_tags'][$tag];
                    }
                }
                $score += min($tagScore / 10, 1) * 0.7; // Normalizza e pesa 70%
            }

            // Punteggio basato su categorie preferite
            if (!empty($interests['preferred_categories'])) {
                $videoCategory = $this->mapTagToCategory(strtolower($video->tags[0] ?? ''));
                if ($videoCategory && isset($interests['preferred_categories'][$videoCategory])) {
                    $score += ($interests['preferred_categories'][$videoCategory] / max($interests['preferred_categories'])) * 0.3; // Pesa 30%
                }
            }

            return [
                'video' => $video,
                'score' => $score
            ];
        });

        return $scoredVideos->sortByDesc('score')->take($limit)->pluck('video');
    }

    /**
     * Restituisce video popolari come fallback
     */
    private function getPopularVideos(int $limit): Collection
    {
        return Video::published()
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Recupera le playlist più popolari
     */
    private function getPopularPlaylists(int $limit): Collection
    {
        return Playlist::public()
            ->with(['user', 'videos'])
            ->whereHas('videos', function ($query) {
                $query->published();
            })
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Estrae i tag da una playlist basandosi sui suoi video
     */
    private function getPlaylistTags(Playlist $playlist): array
    {
        $tags = [];
        
        foreach ($playlist->videos as $video) {
            $videoTags = $video->tags ?? [];
            $tags = array_merge($tags, $videoTags);
        }
        
        return array_unique($tags);
    }

    /**
     * Calcola il punteggio di similarità tra una playlist e la cronologia dell'utente
     */
    private function calculatePlaylistHistoryScore(Playlist $playlist, Collection $watchHistory): float
    {
        if ($watchHistory->isEmpty()) {
            return 0;
        }

        $playlistTags = $this->getPlaylistTags($playlist);
        if (empty($playlistTags)) {
            return 0;
        }

        $totalScore = 0;
        foreach ($watchHistory as $history) {
            $similarity = $this->calculateJaccardSimilarity(
                $playlistTags,
                $history->video->tags ?? []
            );

            // Peso basato su completamento e recency
            $completionWeight = $history->completed ? 1 : ($history->watched_duration / $history->total_duration);
            $recencyWeight = max(0, 1 - (now()->diffInDays($history->last_watched_at) / 30));

            $totalScore += $similarity * $completionWeight * $recencyWeight;
        }

        return $totalScore / $watchHistory->count();
    }

    /**
     * Calcola il punteggio di similarità tra una playlist e i tag preferiti dell'utente
     */
    private function calculatePlaylistTagScore(Playlist $playlist, array $preferredTags): float
    {
        if (empty($preferredTags)) {
            return 0;
        }

        $playlistTags = $this->getPlaylistTags($playlist);
        if (empty($playlistTags)) {
            return 0;
        }

        // Calcola similarità pesata basata sulla frequenza dei tag preferiti
        $score = 0;
        $totalWeight = array_sum($preferredTags);

        foreach ($playlistTags as $tag) {
            if (isset($preferredTags[$tag])) {
                $score += $preferredTags[$tag] / $totalWeight;
            }
        }

        return min($score, 1);
    }

    /**
     * Calcola il punteggio di qualità della playlist
     */
    private function calculatePlaylistQualityScore(Playlist $playlist): float
    {
        $score = 0;

        // Peso per numero di video (1-10)
        $videoCount = $playlist->videos->count();
        $score += min($videoCount / 10, 1) * 0.4;

        // Peso per popolarità (1-1000 views)
        $views = $playlist->views_count;
        $score += min($views / 1000, 1) * 0.3;

        // Peso per età della playlist (più recente = meglio)
        $recency = max(0, 1 - (now()->diffInDays($playlist->created_at) / 90));
        $score += $recency * 0.3;

        return $score;
    }

    /**
     * Genera raccomandazioni di playlist personalizzate per l'utente
     */
    public function getPlaylistRecommendations(int $userId, int $limit = 10): Collection
    {
        $watchHistory = $this->getUserWatchHistory($userId);
        $preferredTags = $this->getUserPreferredTags($userId);
        $subscribedChannels = $this->getUserSubscribedChannels($userId);

        // Se l'utente non ha dati, restituisci playlist popolari
        if ($watchHistory->isEmpty() && $subscribedChannels->isEmpty() && empty($preferredTags)) {
            return $this->getPopularPlaylists($limit);
        }

        // Ottieni candidati playlist (pubbliche con almeno 1 video)
        $candidates = Playlist::public()
            ->with(['user', 'videos' => function ($query) {
                $query->published();
            }])
            ->whereHas('videos', function ($query) {
                $query->published();
            })
            ->get()
            ->filter(function ($playlist) {
                return $playlist->videos->count() > 0;
            });

        // Calcola punteggi per ogni candidato
        $scoredPlaylists = $candidates->map(function ($playlist) use ($watchHistory, $preferredTags, $subscribedChannels) {
            $score = 0;

            // Punteggio cronologia (40%): similarità con video guardati
            $historyScore = $this->calculatePlaylistHistoryScore($playlist, $watchHistory);
            $score += $historyScore * 0.4;

            // Punteggio tag (30%): similarità con tag preferiti
            $tagScore = $this->calculatePlaylistTagScore($playlist, $preferredTags);
            $score += $tagScore * 0.3;

            // Punteggio canali (20%): bonus se dal canale sottoscritto
            $channelScore = $subscribedChannels->contains('id', $playlist->user_id) ? 1 : 0;
            $score += $channelScore * 0.2;

            // Punteggio qualità (10%): basa su numero di video, popolarità e recency
            $qualityScore = $this->calculatePlaylistQualityScore($playlist);
            $score += $qualityScore * 0.1;

            return [
                'playlist' => $playlist,
                'score' => $score
            ];
        });

        // Ordina per punteggio decrescente e limita risultati
        $sortedPlaylists = $scoredPlaylists->sortByDesc('score')->take($limit);

        return $sortedPlaylists->pluck('playlist');
    }
}