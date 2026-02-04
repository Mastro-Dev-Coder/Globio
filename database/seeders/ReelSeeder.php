<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Str;

class ReelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assicurati che ci sia almeno un utente
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
            ]);
            
            // Crea profilo utente
            UserProfile::create([
                'user_id' => $user->id,
                'channel_name' => 'demo-channel',
                'channel_description' => 'Canal demo per testare i reel',
            ]);
        }

        // Crea alcuni reel di esempio
        $reels = [
            [
                'title' => 'Tutorial Rapido: Come fare i reel perfetti! 📱',
                'description' => 'In questo reel ti insegno come creare dei reel fantastici in pochi minuti!',
                'duration' => 45, // 45 secondi
                'views_count' => rand(100, 1000),
                'likes_count' => rand(10, 100),
                'tags' => ['tutorial', 'reel', 'social', 'guide'],
            ],
            [
                'title' => 'Tips & Tricks per content creator 💡',
                'description' => 'I miei migliori consigli per chi vuole iniziare a creare contenuti',
                'duration' => 60,
                'views_count' => rand(200, 1500),
                'likes_count' => rand(20, 150),
                'tags' => ['tips', 'content', 'creator', 'consigli'],
            ],
            [
                'title' => 'Day in my life - Creator Edition ✨',
                'description' => 'Seguimi in una giornata tipo come content creator',
                'duration' => 90,
                'views_count' => rand(500, 2000),
                'likes_count' => rand(50, 300),
                'tags' => ['vlog', 'day', 'creator', 'life'],
            ],
            [
                'title' => '5 Errori da evitare nei reel ❌',
                'description' => 'Gli errori più comuni che fanno fallire i tuoi reel',
                'duration' => 75,
                'views_count' => rand(300, 1200),
                'likes_count' => rand(25, 200),
                'tags' => ['errori', 'reel', 'tips', 'beginner'],
            ],
            [
                'title' => 'Reazione ai viral reel del momento 🔥',
                'description' => 'Le mie reaction ai reel più virali del momento',
                'duration' => 120,
                'views_count' => rand(1000, 5000),
                'likes_count' => rand(100, 500),
                'tags' => ['reaction', 'viral', 'funny', 'trending'],
            ],
        ];

        foreach ($reels as $reelData) {
            $video = Video::create([
                'user_id' => $user->id,
                'title' => $reelData['title'],
                'description' => $reelData['description'],
                'video_url' => Str::slug($reelData['title']),
                'video_path' => 'videos/demo_reel_' . rand(1, 100) . '.mp4',
                'thumbnail_path' => 'thumbnails/demo_thumb_' . rand(1, 100) . '.jpg',
                'duration' => $reelData['duration'],
                'views_count' => $reelData['views_count'],
                'likes_count' => $reelData['likes_count'],
                'status' => 'published',
                'is_public' => true,
                'is_reel' => true, // Questo è il campo più importante!
                'is_featured' => false,
                'tags' => $reelData['tags'],
                'published_at' => now()->subDays(rand(1, 30)),
            ]);

            echo "✅ Reel creato: {$video->title}\n";
        }

        // Crea anche alcuni video normali per confronto
        $normalVideos = [
            [
                'title' => 'Guida completa ai social media per creator',
                'description' => 'Una guida dettagliata su come gestire i social media',
                'duration' => 600, // 10 minuti
                'views_count' => rand(1000, 10000),
                'likes_count' => rand(100, 1000),
                'tags' => ['guide', 'social', 'media', 'creator'],
            ],
            [
                'title' => 'Intervista: I segreti del successo online',
                'description' => 'Intervista con un top creator che ci svela i suoi segreti',
                'duration' => 1800, // 30 minuti
                'views_count' => rand(2000, 15000),
                'likes_count' => rand(200, 1500),
                'tags' => ['intervista', 'successo', 'creator', 'segreto'],
            ],
        ];

        foreach ($normalVideos as $videoData) {
            $video = Video::create([
                'user_id' => $user->id,
                'title' => $videoData['title'],
                'description' => $videoData['description'],
                'video_url' => Str::slug($videoData['title']),
                'video_path' => 'videos/demo_video_' . rand(1, 100) . '.mp4',
                'thumbnail_path' => 'thumbnails/demo_thumb_' . rand(1, 100) . '.jpg',
                'duration' => $videoData['duration'],
                'views_count' => $videoData['views_count'],
                'likes_count' => $videoData['likes_count'],
                'status' => 'published',
                'is_public' => true,
                'is_reel' => false, // Video normale
                'is_featured' => true,
                'tags' => $videoData['tags'],
                'published_at' => now()->subDays(rand(1, 15)),
            ]);

            echo "✅ Video normale creato: {$video->title}\n";
        }

        $this->command->info('🎬 Seeder completato! Creati 5 reel e 2 video normali per i test.');
    }
}
