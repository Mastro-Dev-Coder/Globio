<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Playlist;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Video;
use App\Models\WatchHistory;
use App\Models\WatchLater;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding rich demo users and content...');

        $mastroUser = $this->findMastroUser();
        $users = $this->seedUsersAndProfiles($mastroUser);

        $this->cleanupCreatorsContent($users);

        $videos = $this->seedVideosAndReels($users);
        $this->seedSubscriptions($users);
        $this->seedPlaylists($users, $videos);
        $this->seedComments($users, $videos);
        $this->seedLikes($users, $videos);
        $this->seedWatchData($users, $videos);
        $this->refreshCounters($users, $videos);
        $this->applyRealDemoMedia();

        $this->command->info('Rich demo content seeded successfully.');
    }

    private function findMastroUser(): ?User
    {
        return User::query()
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) like ?', ['%mastro%'])
                    ->orWhereRaw('LOWER(email) like ?', ['%mastro%'])
                    ->orWhereHas('userProfile', function ($profileQuery) {
                        $profileQuery
                            ->whereRaw('LOWER(username) like ?', ['%mastro%'])
                            ->orWhereRaw('LOWER(channel_name) like ?', ['%mastro%']);
                    });
            })
            ->first();
    }

    private function seedUsersAndProfiles(?User $mastroUser): array
    {
        $definitions = [
            ['name' => 'Luna Motion', 'email' => 'demo.luna@globio.local', 'username' => 'luna-motion', 'channel_name' => 'Luna Motion', 'description' => 'Vertical edits, trend e short dinamici.', 'country' => 'IT', 'verified' => true],
            ['name' => 'Alex CodeCafe', 'email' => 'demo.alex@globio.local', 'username' => 'alex-codecafe', 'channel_name' => 'Alex CodeCafe', 'description' => 'Code, AI e strumenti creator.', 'country' => 'US', 'verified' => true],
            ['name' => 'Nora Travel', 'email' => 'demo.nora@globio.local', 'username' => 'nora-travel', 'channel_name' => 'Nora Travel', 'description' => 'Travel stories e itinerari smart.', 'country' => 'ES', 'verified' => false],
            ['name' => 'Diego Fitness', 'email' => 'demo.diego@globio.local', 'username' => 'diego-fitness', 'channel_name' => 'Diego Fitness', 'description' => 'Fitness routines e benessere.', 'country' => 'IT', 'verified' => false],
            ['name' => 'Sara Kitchen', 'email' => 'demo.sara@globio.local', 'username' => 'sara-kitchen', 'channel_name' => 'Sara Kitchen', 'description' => 'Cucina veloce e ricette social.', 'country' => 'FR', 'verified' => false],
            ['name' => 'Marco Visione', 'email' => 'demo.marco@globio.local', 'username' => 'marco-visione', 'channel_name' => 'Marco Visione', 'description' => 'Strategie crescita canale e format.', 'country' => 'IT', 'verified' => true],
            ['name' => 'Vera Design', 'email' => 'demo.vera@globio.local', 'username' => 'vera-design', 'channel_name' => 'Vera Design', 'description' => 'Branding, UI e visual identity.', 'country' => 'GB', 'verified' => false],
            ['name' => 'Gio Beats', 'email' => 'demo.gio@globio.local', 'username' => 'gio-beats', 'channel_name' => 'Gio Beats', 'description' => 'Music lab, loop e produzione.', 'country' => 'IT', 'verified' => false],
            ['name' => 'Elia Garage', 'email' => 'demo.elia@globio.local', 'username' => 'elia-garage', 'channel_name' => 'Elia Garage', 'description' => 'Auto test e recensioni tecniche.', 'country' => 'DE', 'verified' => false],
        ];

        $users = [];

        if ($mastroUser) {
            $profile = $mastroUser->userProfile ?? UserProfile::create(['user_id' => $mastroUser->id]);
            $profile->update([
                'username' => $profile->username ?: 'mastro',
                'channel_name' => $profile->channel_name ?: 'Mastro',
                'channel_description' => $profile->channel_description ?: 'Creator principale per demo avanzata.',
                'is_channel_enabled' => true,
                'is_verified' => true,
                'country' => $profile->country ?: 'IT',
                'channel_created_at' => $profile->channel_created_at ?: now()->subMonths(9),
            ]);

            $users['mastro'] = $mastroUser;
        }

        foreach ($definitions as $definition) {
            $user = User::updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'role' => 'demo',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $definition['username'],
                    'channel_name' => $definition['channel_name'],
                    'channel_description' => $definition['description'],
                    'avatar_url' => null,
                    'banner_url' => null,
                    'is_verified' => $definition['verified'],
                    'is_channel_enabled' => true,
                    'social_links' => [
                        'instagram' => 'https://instagram.com/' . str_replace('-', '', $definition['username']),
                        'youtube' => 'https://youtube.com/@' . $definition['username'],
                    ],
                    'country' => $definition['country'],
                    'channel_created_at' => now()->subDays(random_int(30, 260)),
                ]
            );

            $users[$definition['username']] = $user;
        }

        return $users;
    }

    private function cleanupCreatorsContent(array $users): void
    {
        $creatorIds = collect($users)->pluck('id')->values();
        $videoIds = Video::whereIn('user_id', $creatorIds)->pluck('id');
        $playlistIds = Playlist::whereIn('user_id', $creatorIds)->pluck('id');

        if ($videoIds->isNotEmpty()) {
            Like::where('likeable_type', Video::class)->whereIn('likeable_id', $videoIds)->delete();
            Comment::whereIn('video_id', $videoIds)->delete();
            WatchHistory::whereIn('video_id', $videoIds)->delete();
            WatchLater::whereIn('video_id', $videoIds)->delete();
            DB::table('playlist_videos')->whereIn('video_id', $videoIds)->delete();
            Video::whereIn('id', $videoIds)->delete();
        }

        if ($playlistIds->isNotEmpty()) {
            DB::table('playlist_videos')->whereIn('playlist_id', $playlistIds)->delete();
            Playlist::whereIn('id', $playlistIds)->delete();
        }

        Subscription::whereIn('subscriber_id', $creatorIds)
            ->orWhereIn('channel_id', $creatorIds)
            ->delete();
    }

    private function seedVideosAndReels(array $users)
    {
        $landscapeMedia = [
            ['video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4', 'thumb' => 'https://picsum.photos/id/1011/1280/720', 'duration' => 596],
            ['video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4', 'thumb' => 'https://picsum.photos/id/1015/1280/720', 'duration' => 653],
            ['video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4', 'thumb' => 'https://picsum.photos/id/1016/1280/720', 'duration' => 888],
            ['video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4', 'thumb' => 'https://picsum.photos/id/1025/1280/720', 'duration' => 734],
            ['video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4', 'thumb' => 'https://picsum.photos/id/1035/1280/720', 'duration' => 15],
            ['video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4', 'thumb' => 'https://picsum.photos/id/1043/1280/720', 'duration' => 195],
        ];

        $verticalMedia = [
            ['video' => 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4', 'thumb' => 'https://picsum.photos/id/1003/720/1280', 'duration' => 5],
            ['video' => 'https://samplelib.com/lib/preview/mp4/sample-10s.mp4', 'thumb' => 'https://picsum.photos/id/1005/720/1280', 'duration' => 10],
            ['video' => 'https://samplelib.com/lib/preview/mp4/sample-15s.mp4', 'thumb' => 'https://picsum.photos/id/1006/720/1280', 'duration' => 15],
            ['video' => 'https://samplelib.com/lib/preview/mp4/sample-20s.mp4', 'thumb' => 'https://picsum.photos/id/1012/720/1280', 'duration' => 20],
            ['video' => 'https://samplelib.com/lib/preview/mp4/sample-30s.mp4', 'thumb' => 'https://picsum.photos/id/1027/720/1280', 'duration' => 30],
        ];

        $topicByCreator = [
            'mastro' => ['editing', 'creator workflow', 'studio setup', 'channel growth', 'live format', 'storytelling'],
            'luna-motion' => ['transition', 'vertical edit', 'viral hook', 'beat sync', 'caption style', 'trend remix'],
            'alex-codecafe' => ['automation', 'AI tools', 'prompt craft', 'dev setup', 'scripting', 'creator stack'],
            'nora-travel' => ['city guide', 'weekend trip', 'budget route', 'travel tips', 'hidden spots', 'packing'],
            'diego-fitness' => ['home workout', 'mobility', 'desk fitness', 'core routine', 'breathwork', 'recovery'],
            'sara-kitchen' => ['quick recipe', 'meal prep', 'one pot', 'street food', 'healthy dish', 'dessert'],
            'marco-visione' => ['format strategy', 'audience retention', 'analytics read', 'thumbnail ideas', 'upload plan', 'series planning'],
            'vera-design' => ['branding kit', 'color system', 'typography', 'landing UI', 'visual hierarchy', 'product mockup'],
            'gio-beats' => ['beat session', 'mix tips', 'sample pack', 'studio jam', 'bassline', 'vocal chain'],
            'elia-garage' => ['car review', 'engine check', 'road test', 'maintenance', 'garage tools', 'detail tips'],
        ];

        $videoTitleTemplates = [
            'Masterclass su %s: guida completa',
            'Come migliorare %s in modo pratico',
            '5 strategie reali per %s',
            'Case study: risultati concreti con %s',
            'Errori comuni su %s e come evitarli',
            'Setup professionale per %s',
        ];

        $reelTitleTemplates = [
            'Tip rapido su %s',
            'Hack in 20 secondi: %s',
            'Prima/Dopo: %s',
            '3 idee veloci per %s',
            'Mini workflow: %s',
        ];

        $videos = collect();
        $landscapeIndex = 0;
        $verticalIndex = 0;

        foreach ($users as $creatorKey => $creator) {
            $topics = $topicByCreator[$creatorKey] ?? ['content', 'creator'];
            $longCount = $creatorKey === 'mastro' ? 10 : 6;
            $reelCount = $creatorKey === 'mastro' ? 8 : 4;

            for ($i = 0; $i < $longCount; $i++) {
                $topic = $topics[$i % count($topics)];
                $template = $videoTitleTemplates[$i % count($videoTitleTemplates)];
                $title = sprintf($template, $topic);
                $slug = Str::slug($title) . '-' . $creatorKey . '-video-' . ($i + 1);
                $media = $landscapeMedia[$landscapeIndex++ % count($landscapeMedia)];

                $videos->push($this->upsertVideo(
                    creator: $creator,
                    slug: $slug,
                    title: $title,
                    description: "Contenuto demo creator su {$topic}.",
                    media: $media,
                    isReel: false,
                    isFeatured: $i < 2,
                    tags: [Str::slug($topic), 'creator', 'demo', 'globio']
                ));
            }

            for ($i = 0; $i < $reelCount; $i++) {
                $topic = $topics[$i % count($topics)];
                $template = $reelTitleTemplates[$i % count($reelTitleTemplates)];
                $title = sprintf($template, $topic);
                $slug = Str::slug($title) . '-' . $creatorKey . '-reel-' . ($i + 1);
                $media = $verticalMedia[$verticalIndex++ % count($verticalMedia)];

                $videos->push($this->upsertVideo(
                    creator: $creator,
                    slug: $slug,
                    title: $title,
                    description: "Reel demo verticale su {$topic}.",
                    media: $media,
                    isReel: true,
                    isFeatured: false,
                    tags: [Str::slug($topic), 'reel', 'shorts', 'demo']
                ));
            }
        }

        return $videos->keyBy('video_url');
    }

    private function upsertVideo(
        User $creator,
        string $slug,
        string $title,
        string $description,
        array $media,
        bool $isReel,
        bool $isFeatured,
        array $tags
    ): Video {
        return Video::updateOrCreate(
            ['video_url' => $slug],
            [
                'user_id' => $creator->id,
                'title' => $title,
                'description' => $description,
                'thumbnail_path' => $media['thumb'],
                'video_path' => $media['video'],
                'duration' => (int) $media['duration'],
                'views_count' => random_int(1200, 98000),
                'likes_count' => random_int(80, 12000),
                'dislikes_count' => random_int(1, 400),
                'comments_count' => 0,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => $isFeatured,
                'is_reel' => $isReel,
                'comments_enabled' => true,
                'likes_enabled' => true,
                'comments_require_approval' => false,
                'video_quality' => '1080p',
                'video_format' => 'mp4',
                'file_size' => null,
                'tags' => $tags,
                'language' => 'it',
                'published_at' => now()->subDays(random_int(1, 90)),
            ]
        );
    }

    private function seedSubscriptions(array $users): void
    {
        $userKeys = array_keys($users);

        foreach ($userKeys as $subscriberKey) {
            $others = array_values(array_filter($userKeys, fn ($k) => $k !== $subscriberKey));
            shuffle($others);

            foreach (array_slice($others, 0, 4) as $channelKey) {
                Subscription::updateOrCreate(
                    [
                        'subscriber_id' => $users[$subscriberKey]->id,
                        'channel_id' => $users[$channelKey]->id,
                    ],
                    []
                );
            }
        }
    }

    private function seedPlaylists(array $users, $videos): void
    {
        $publishedVideos = Video::published()->get();

        foreach ($users as $creatorKey => $creator) {
            $playlistCount = $creatorKey === 'mastro' ? 4 : 2;

            for ($i = 1; $i <= $playlistCount; $i++) {
                $title = $creatorKey === 'mastro'
                    ? "Mastro Mix #{$i}"
                    : ucfirst(str_replace('-', ' ', $creatorKey)) . " Collection #{$i}";

                $playlist = Playlist::updateOrCreate(
                    [
                        'user_id' => $creator->id,
                        'title' => $title,
                    ],
                    [
                        'description' => 'Playlist demo con mix di video e creators.',
                        'is_public' => true,
                        'thumbnail_path' => null,
                        'views_count' => random_int(600, 22000),
                    ]
                );

                $selection = $publishedVideos->shuffle()->unique('id')->take(random_int(6, 12))->values();
                $syncPayload = [];

                foreach ($selection as $position => $video) {
                    $syncPayload[$video->id] = ['position' => $position + 1];
                }

                DB::table('playlist_videos')->where('playlist_id', $playlist->id)->delete();

                foreach ($syncPayload as $videoId => $pivot) {
                    DB::table('playlist_videos')->insert([
                        'playlist_id' => $playlist->id,
                        'video_id' => $videoId,
                        'position' => $pivot['position'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $playlist->update(['video_count' => count($syncPayload)]);
            }
        }
    }

    private function seedComments(array $users, $videos): void
    {
        $messages = [
            'Video top, ritmo ottimo e spiegazione super chiara.',
            'Questo format funziona tantissimo, continua cosi.',
            'Grande qualità, ho preso subito appunti.',
            'Uno dei migliori contenuti usciti oggi.',
            'Molto utile, specialmente la parte finale.',
            'Editing pulito e contenuto davvero concreto.',
            'Questo reel merita più visibilità.',
            'Finalmente un tutorial fatto bene e senza filler.',
        ];

        $videoList = Video::published()->get();
        $userList = collect($users)->values();

        for ($i = 0; $i < 180; $i++) {
            $video = $videoList->random();
            $author = $userList->random();

            Comment::create([
                'video_id' => $video->id,
                'user_id' => $author->id,
                'content' => $messages[array_rand($messages)],
                'status' => Comment::STATUS_APPROVED,
                'created_at' => now()->subDays(random_int(0, 45)),
                'updated_at' => now()->subDays(random_int(0, 45)),
            ]);
        }
    }

    private function seedLikes(array $users, $videos): void
    {
        $videoList = Video::published()->get();
        $userList = collect($users)->values();

        foreach ($videoList as $video) {
            $reactors = $userList->shuffle()->take(random_int(3, min(8, $userList->count())));

            foreach ($reactors as $reactor) {
                $reaction = random_int(1, 10) <= 9 ? 'like' : 'dislike';

                Like::updateOrCreate(
                    [
                        'likeable_type' => Video::class,
                        'likeable_id' => $video->id,
                        'user_id' => $reactor->id,
                    ],
                    [
                        'reaction' => $reaction,
                        'type' => $reaction,
                    ]
                );
            }
        }
    }

    private function seedWatchData(array $users, $videos): void
    {
        $videoList = Video::published()->get();
        $userList = collect($users)->values();

        foreach ($userList as $user) {
            $watched = $videoList->shuffle()->take(random_int(8, 18));

            foreach ($watched as $video) {
                $watchedDuration = random_int((int) floor($video->duration * 0.3), max(1, $video->duration));
                $completed = $watchedDuration >= $video->duration;

                WatchHistory::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'video_id' => $video->id,
                    ],
                    [
                        'watched_duration' => $watchedDuration,
                        'total_duration' => $video->duration,
                        'completed' => $completed,
                        'last_watched_at' => now()->subDays(random_int(0, 30)),
                    ]
                );
            }

            $watchLater = $videoList->shuffle()->take(random_int(3, 7));
            foreach ($watchLater as $video) {
                WatchLater::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'video_id' => $video->id,
                    ],
                    []
                );
            }
        }
    }

    private function refreshCounters(array $users, $videos): void
    {
        $videoIds = Video::published()->pluck('id');

        Video::whereIn('id', $videoIds)->get()->each(function (Video $video) {
            $likes = $video->likes()->where('reaction', 'like')->count();
            $dislikes = $video->likes()->where('reaction', 'dislike')->count();
            $comments = $video->comments()->where('status', Comment::STATUS_APPROVED)->count();

            $video->update([
                'likes_count' => $likes,
                'dislikes_count' => $dislikes,
                'comments_count' => $comments,
            ]);
        });

        foreach ($users as $user) {
            DB::table('user_profiles')
                ->where('user_id', $user->id)
                ->update([
                    'video_count' => $user->videos()->published()->count(),
                    'total_views' => (int) $user->videos()->sum('views_count'),
                    'subscriber_count' => Subscription::where('channel_id', $user->id)->count(),
                ]);
        }
    }

    private function applyRealDemoMedia(): void
    {
        $disk = Storage::disk('public');

        // Priorita' ai video reali presenti in storage/app/public/demo (root).
        $videoPaths = collect($disk->files('demo'))
            ->filter(fn ($path) => str_ends_with(strtolower($path), '.mp4'))
            ->sort()
            ->values()
            ->all();

        // Thumbnails scaricate in storage/app/public/demo/thumbs.
        $thumbPaths = collect($disk->files('demo/thumbs'))
            ->filter(function ($path) {
                $lower = strtolower($path);
                return str_ends_with($lower, '.jpg') || str_ends_with($lower, '.jpeg') || str_ends_with($lower, '.png');
            })
            ->sort()
            ->values()
            ->all();

        // Fallback legacy, nel caso non siano presenti i nuovi asset.
        if (empty($videoPaths)) {
            $videoPaths = [
                'demo/real/videos/video-01.mp4',
                'demo/real/videos/video-02.mp4',
                'demo/real/videos/video-03.mp4',
                'demo/real/videos/video-04.mp4',
                'demo/real/videos/video-05.mp4',
                'demo/real/videos/video-06.mp4',
                'demo/real/videos/video-07.mp4',
            ];
        }

        if (empty($thumbPaths)) {
            $thumbPaths = [
                'demo/real/thumbs/thumb-01.jpg',
                'demo/real/thumbs/thumb-02.jpg',
                'demo/real/thumbs/thumb-03.jpg',
                'demo/real/thumbs/thumb-04.jpg',
                'demo/real/thumbs/thumb-05.jpg',
                'demo/real/thumbs/thumb-06.jpg',
                'demo/real/thumbs/thumb-07.jpg',
                'demo/real/thumbs/thumb-08.jpg',
                'demo/real/thumbs/thumb-09.jpg',
                'demo/real/thumbs/thumb-10.jpg',
            ];
        }

        $avatarPaths = [
            'demo/real/avatars/avatar-01.jpg',
            'demo/real/avatars/avatar-02.jpg',
            'demo/real/avatars/avatar-03.jpg',
            'demo/real/avatars/avatar-04.jpg',
            'demo/real/avatars/avatar-05.jpg',
            'demo/real/avatars/avatar-06.jpg',
            'demo/real/avatars/avatar-07.jpg',
            'demo/real/avatars/avatar-08.jpg',
            'demo/real/avatars/avatar-09.jpg',
            'demo/real/avatars/avatar-10.jpg',
        ];

        $bannerPaths = [
            'demo/real/banners/banner-01.jpg',
            'demo/real/banners/banner-02.jpg',
            'demo/real/banners/banner-03.jpg',
            'demo/real/banners/banner-04.jpg',
            'demo/real/banners/banner-05.jpg',
            'demo/real/banners/banner-06.jpg',
        ];

        $videoPaths = array_values(array_filter($videoPaths, fn ($path) => $disk->exists($path)));
        $thumbPaths = array_values(array_filter($thumbPaths, fn ($path) => $disk->exists($path)));
        $avatarPaths = array_values(array_filter($avatarPaths, fn ($path) => $disk->exists($path)));
        $bannerPaths = array_values(array_filter($bannerPaths, fn ($path) => $disk->exists($path)));

        $videos = Video::published()->orderBy('id')->get();
        foreach ($videos as $index => $video) {
            if (!empty($videoPaths)) {
                $mediaPath = $videoPaths[$index % count($videoPaths)];
                $video->video_path = $mediaPath;
                $video->original_file_path = $mediaPath;
                $video->video_format = 'mp4';

                $fullPath = storage_path('app/public/' . $mediaPath);
                if (file_exists($fullPath)) {
                    $video->file_size = filesize($fullPath);
                }
            }

            if (!empty($thumbPaths)) {
                $video->thumbnail_path = $thumbPaths[$index % count($thumbPaths)];
            }

            $video->save();
        }

        $profiles = UserProfile::orderBy('id')->get();
        foreach ($profiles as $index => $profile) {
            if (!empty($avatarPaths)) {
                $profile->avatar_url = $avatarPaths[$index % count($avatarPaths)];
            }

            if (!empty($bannerPaths)) {
                $profile->banner_url = $bannerPaths[$index % count($bannerPaths)];
            }

            $profile->save();
        }

        Playlist::with('videos')->get()->each(function (Playlist $playlist) {
            if ($playlist->videos->isNotEmpty()) {
                $playlist->thumbnail_path = $playlist->videos->first()->thumbnail_path;
                $playlist->save();
            }
        });

        $this->command->info('Applied local real media set to demo content.');
    }
}
