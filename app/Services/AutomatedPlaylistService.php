<?php

namespace App\Services;

use App\Models\Video;
use App\Models\User;
use App\Models\Playlist;
use App\Models\WatchHistory;
use App\Models\Like;
use App\Models\Subscription;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servizio per la generazione automatica di playlist personalizzate
 * basato sulle preferenze e comportamenti di visione dell'utente.
 * 
 * Crea playlist automaticamente come fa YouTube per la home e sidebar.
 */
class AutomatedPlaylistService
{
    /**
     * Tipi di playlist automatiche
     */
    public const PLAYLIST_TYPE_CONTINIUA_VISIONE = 'continua_visione';
    public const PLAYLIST_TYPE_PER_CATEGORIA = 'per_categoria';
    public const PLAYLIST_TYPE_SIMILI = 'video_similari';
    public const PLAYLIST_TYPE_DA_CANALE = 'da_canale';
    public const PLAYLIST_TYPE_TENDENZE = 'trending';
    public const PLAYLIST_TYPE_RICERCHE = 'basato_ricerche';

    /**
     * Ottiene il nome del sito per le playlist
     */
    protected function getSiteName(): string
    {
        return Setting::getValue('site_name', 'Globio');
    }

    /**
     * Genera playlist automatiche per un utente
     *
     * @param int $userId ID dell'utente
     * @param int $limit Numero massimo di playlist da creare
     * @return Collection Playlist create
     */
    public function generateAutomaticPlaylists(int $userId, int $limit = 5): Collection
    {
        $user = User::find($userId);
        if (!$user) {
            return collect([]);
        }

        $siteName = $this->getSiteName();
        $createdPlaylists = collect([]);
        $watchHistory = $this->getUserWatchHistory($userId);
        $preferredTags = $this->getUserPreferredTags($userId);
        $subscribedChannels = $this->getUserSubscribedChannels($userId);

        $continueWatching = $this->createContinueWatchingPlaylist($userId, $watchHistory, $siteName);
        if ($continueWatching) {
            $createdPlaylists->push($continueWatching);
        }

        if (!empty($preferredTags)) {
            $categoryPlaylist = $this->createCategoryPlaylist($userId, $preferredTags, $siteName);
            if ($categoryPlaylist) {
                $createdPlaylists->push($categoryPlaylist);
            }
        }

        $forYouPlaylist = $this->createForYouPlaylist($userId, $preferredTags, $watchHistory, $siteName);
        if ($forYouPlaylist) {
            $createdPlaylists->push($forYouPlaylist);
        }

        if ($subscribedChannels->isNotEmpty()) {
            $subscribedPlaylist = $this->createSubscribedChannelsPlaylist($userId, $subscribedChannels, $siteName);
            if ($subscribedPlaylist) {
                $createdPlaylists->push($subscribedPlaylist);
            }
        }

        $trendingPlaylist = $this->createTrendingPlaylist($userId, $preferredTags, $siteName);
        if ($trendingPlaylist) {
            $createdPlaylists->push($trendingPlaylist);
        }

        return $createdPlaylists->take($limit);
    }

    /**
     * Crea playlist "Continua a guardare" con video incompleti
     */
    public function createContinueWatchingPlaylist(int $userId, ?Collection $watchHistory = null, ?string $siteName = null): ?Playlist
    {
        $watchHistory = $watchHistory ?? $this->getUserWatchHistory($userId);
        $siteName = $siteName ?? $this->getSiteName();
        
        $incompleteVideos = $watchHistory
            ->where('completed', false)
            ->sortByDesc('last_watched_at')
            ->take(20)
            ->pluck('video')
            ->filter();

        if ($incompleteVideos->isEmpty()) {
            return null;
        }

        return $this->createOrUpdatePlaylist(
            $userId,
            "Mix di {$siteName}: Continua a guardare",
            'I tuoi video incompleti',
            self::PLAYLIST_TYPE_CONTINIUA_VISIONE,
            $incompleteVideos
        );
    }

    /**
     * Crea playlist per categoria preferita
     */
    public function createCategoryPlaylist(int $userId, array $preferredTags, ?string $siteName = null): ?Playlist
    {
        if (empty($preferredTags)) {
            return null;
        }

        $siteName = $siteName ?? $this->getSiteName();
        $topTag = array_key_first($preferredTags);
        $categoryName = ucfirst($topTag);

        $watchHistory = $this->getUserWatchHistory($userId);
        $watchedVideoIds = $watchHistory->pluck('video_id')->toArray();

        $relatedVideos = Video::published()
            ->whereNotIn('id', $watchedVideoIds)
            ->whereJsonContains('tags', $topTag)
            ->orderBy('views_count', 'desc')
            ->limit(30)
            ->get();

        if ($relatedVideos->isEmpty()) {
            return null;
        }

        return $this->createOrUpdatePlaylist(
            $userId,
            "Mix di {$siteName}: {$categoryName}",
            "Video {$categoryName} che potrebbero piacerti",
            self::PLAYLIST_TYPE_PER_CATEGORIA,
            $relatedVideos
        );
    }

    /**
     * Crea playlist "Video per te" - raccomandazioni personalizzate
     */
    public function createForYouPlaylist(int $userId, array $preferredTags, ?Collection $watchHistory = null, ?string $siteName = null): ?Playlist
    {
        $watchHistory = $watchHistory ?? $this->getUserWatchHistory($userId);
        $siteName = $siteName ?? $this->getSiteName();
        $watchedVideoIds = $watchHistory->pluck('video_id')->toArray();

        $recommendationService = app(VideoRecommendationService::class);
        $recommendedVideos = $recommendationService->getRecommendations($userId, 30);

        if ($recommendedVideos->isEmpty()) {
            return null;
        }

        return $this->createOrUpdatePlaylist(
            $userId,
            "Mix di {$siteName}: Per te",
            'Raccomandazioni personalizzate basate sui tuoi interessi',
            self::PLAYLIST_TYPE_SIMILI,
            $recommendedVideos
        );
    }

    /**
     * Crea playlist dai canali sottoscritti
     */
    public function createSubscribedChannelsPlaylist(int $userId, ?Collection $subscribedChannels = null, ?string $siteName = null): ?Playlist
    {
        $subscribedChannels = $subscribedChannels ?? $this->getUserSubscribedChannels($userId);
        $siteName = $siteName ?? $this->getSiteName();
        
        if ($subscribedChannels->isEmpty()) {
            return null;
        }

        $channelIds = $subscribedChannels->pluck('id')->toArray();

        $recentVideos = Video::published()
            ->whereIn('user_id', $channelIds)
            ->where('published_at', '>=', now()->subDays(7))
            ->orderBy('published_at', 'desc')
            ->limit(30)
            ->get();

        if ($recentVideos->isEmpty()) {
            return null;
        }

        return $this->createOrUpdatePlaylist(
            $userId,
            "Mix di {$siteName}: Dai canali che segui",
            'Video recenti dai canali che hai sottoscritto',
            self::PLAYLIST_TYPE_DA_CANALE,
            $recentVideos
        );
    }

    /**
     * Crea playlist trending
     */
    public function createTrendingPlaylist(int $userId, array $preferredTags = [], ?string $siteName = null): ?Playlist
    {
        $siteName = $siteName ?? $this->getSiteName();
        $trendingVideos = Video::published()
            ->where('published_at', '>=', now()->subDays(7))
            ->orderBy('views_count', 'desc')
            ->limit(30)
            ->get();

        if ($trendingVideos->isEmpty()) {
            return null;
        }

        $description = 'I video più popolari di questa settimana';
        if (!empty($preferredTags)) {
            $topTag = ucfirst(array_key_first($preferredTags));
            $description = "I video {$topTag} più popolari";
        }

        return $this->createOrUpdatePlaylist(
            $userId,
            "Mix di {$siteName}: Tendenze",
            $description,
            self::PLAYLIST_TYPE_TENDENZE,
            $trendingVideos
        );
    }

    /**
     * Crea o aggiorna una playlist automatica
     */
    protected function createOrUpdatePlaylist(
        int $userId,
        string $title,
        string $description,
        string $type,
        Collection $videos
    ): Playlist {
        $existingPlaylist = Playlist::where('user_id', $userId)
            ->where('is_automatic', true)
            ->where('auto_playlist_type', $type)
            ->first();

        if ($existingPlaylist) {
            $existingPlaylist->videos()->sync(
                $videos->pluck('id')->mapWithKeys(function ($id) use ($videos) {
                    return [$id => ['position' => $videos->search(function ($v) use ($id) {
                        return $v->id === $id;
                    })]];
                })->toArray()
            );
            
            $existingPlaylist->video_count = $videos->count();
            $existingPlaylist->save();

            return $existingPlaylist;
        }

        $playlist = Playlist::create([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'is_public' => false,
            'video_count' => $videos->count(),
            'views_count' => 0,
            'is_automatic' => true,
            'auto_playlist_type' => $type,
        ]);

        $playlist->videos()->attach(
            $videos->pluck('id')->mapWithKeys(function ($id) use ($videos) {
                return [$id => ['position' => $videos->search(function ($v) use ($id) {
                    return $v->id === $id;
                })]];
            })->toArray()
        );

        return $playlist;
    }

    /**
     * Ottiene playlist suggerite da mostrare nella sidebar
     * basate sul video corrente
     */
    public function getSuggestedPlaylistsForSidebar(int $userId, ?Video $currentVideo = null, int $limit = 3): Collection
    {
        $popularPlaylists = Playlist::public()
            ->with(['user', 'videos'])
            ->where('is_automatic', false)
            ->where('video_count', '>=', 3)
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();

        if ($currentVideo && $currentVideo->tags) {
            $relatedTagPlaylists = Playlist::public()
                ->with(['user', 'videos' => function ($query) {
                    $query->published()->limit(5);
                }])
                ->where('is_automatic', false)
                ->whereHas('videos', function ($query) use ($currentVideo) {
                    $query->published()->where(function ($q) use ($currentVideo) {
                        foreach ($currentVideo->tags as $tag) {
                            $q->orWhereJsonContains('tags', $tag);
                        }
                    });
                })
                ->limit($limit)
                ->get();

            return $popularPlaylists->merge($relatedTagPlaylists)->unique('id')->take($limit);
        }

        return $popularPlaylists;
    }

    /**
     * Ottiene playlist personalizzate dell'utente
     */
    public function getUserAutoPlaylists(int $userId): Collection
    {
        return Playlist::where('user_id', $userId)
            ->where('is_automatic', true)
            ->with(['videos' => function ($query) {
                $query->published()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Recupera la cronologia di visione dell'utente
     */
    protected function getUserWatchHistory(int $userId): Collection
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
     * Recupera i tag preferiti dell'utente
     */
    protected function getUserPreferredTags(int $userId): array
    {
        $watchHistory = $this->getUserWatchHistory($userId);
        
        $tagCounts = [];
        foreach ($watchHistory as $history) {
            $tags = $history->video->tags ?? [];
            foreach ($tags as $tag) {
                $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
            }
        }

        arsort($tagCounts);
        return $tagCounts;
    }

    /**
     * Recupera i canali sottoscritti
     */
    protected function getUserSubscribedChannels(int $userId): Collection
    {
        return User::whereHas('subscribers', function ($query) use ($userId) {
            $query->where('subscriber_id', $userId);
        })->get();
    }

    /**
     * Aggiorna playlist automatiche esistenti
     */
    public function refreshAutoPlaylists(int $userId): void
    {
        $autoPlaylists = $this->getUserAutoPlaylists($userId);

        foreach ($autoPlaylists as $playlist) {
            switch ($playlist->auto_playlist_type) {
                case self::PLAYLIST_TYPE_CONTINIUA_VISIONE:
                    $this->createContinueWatchingPlaylist($userId);
                    break;
                case self::PLAYLIST_TYPE_PER_CATEGORIA:
                    $tags = $this->getUserPreferredTags($userId);
                    if (!empty($tags)) {
                        $this->createCategoryPlaylist($userId, $tags);
                    }
                    break;
                case self::PLAYLIST_TYPE_SIMILI:
                    $tags = $this->getUserPreferredTags($userId);
                    $history = $this->getUserWatchHistory($userId);
                    $this->createForYouPlaylist($userId, $tags, $history);
                    break;
                case self::PLAYLIST_TYPE_DA_CANALE:
                    $channels = $this->getUserSubscribedChannels($userId);
                    $this->createSubscribedChannelsPlaylist($userId, $channels);
                    break;
                case self::PLAYLIST_TYPE_TENDENZE:
                    $tags = $this->getUserPreferredTags($userId);
                    $this->createTrendingPlaylist($userId, $tags);
                    break;
            }
        }
    }

    /**
     * Elimina playlist automatiche vecchie
     */
    public function cleanupOldAutoPlaylists(int $userId, int $daysOld = 30): int
    {
        return Playlist::where('user_id', $userId)
            ->where('is_automatic', true)
            ->where('updated_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
