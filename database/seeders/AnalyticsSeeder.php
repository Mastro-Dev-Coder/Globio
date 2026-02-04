<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Video;
use App\Models\ChannelAnalytics;
use App\Models\Subscription;
use App\Models\Comment;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('Nessun utente trovato. Creare prima gli utenti.');
            return;
        }

        foreach ($users as $user) {
            $this->seedUserAnalytics($user);
        }
        
        $this->command->info('Dati analytics e community generati con successo!');
    }

    private function seedUserAnalytics($user)
    {
        $videos = $user->videos()->published()->get();
        
        if ($videos->isEmpty()) {
            return;
        }

        // Genera dati analytics per gli ultimi 30 giorni
        $startDate = now()->subDays(30);
        
        foreach ($videos as $video) {
            $this->seedVideoAnalytics($user, $video, $startDate);
        }

        // Genera iscritti finti
        $this->seedFakeSubscribers($user);
        
        // Genera commenti finti
        $this->seedFakeComments($user, $videos);
    }

    private function seedVideoAnalytics($user, $video, $startDate)
    {
        $currentDate = clone $startDate;
        
        // Lista delle fonti di traffico realistiche
        $trafficSources = ['youtube', 'google', 'direct', 'social', 'referral', 'unknown'];
        $countries = ['IT', 'US', 'GB', 'FR', 'DE', 'ES', 'BR', 'IN', 'JP', 'CA'];
        $devices = ['mobile', 'desktop', 'tablet'];
        
        while ($currentDate->lte(now())) {
            // Genera dati realistici per ogni giorno
            $views = rand(5, 100);
            $likes = rand(0, intval($views * 0.1));
            $comments = rand(0, intval($views * 0.05));
            $shares = rand(0, intval($views * 0.02));
            
            // Dividi le views tra diverse fonti, paesi e dispositivi
            $sourcesCount = rand(1, 3);
            $countriesCount = rand(1, 2);
            $devicesCount = rand(1, 2);
            
            for ($i = 0; $i < $sourcesCount; $i++) {
                ChannelAnalytics::create([
                    'user_id' => $user->id,
                    'video_id' => $video->id,
                    'date' => $currentDate->toDateString(),
                    'views' => intval($views / $sourcesCount),
                    'likes' => intval($likes / $sourcesCount),
                    'comments' => intval($comments / $sourcesCount),
                    'shares' => intval($shares / $sourcesCount),
                    'watch_time_minutes' => rand(10, 300),
                    'average_watch_duration' => rand(30, 600),
                    'traffic_source' => $trafficSources[array_rand($trafficSources)],
                    'country' => $countries[array_rand($countries)],
                    'device_type' => $devices[array_rand($devices)],
                    'referrer' => 'https://google.com',
                ]);
            }
            
            $currentDate->addDay();
        }
    }

    private function seedFakeSubscribers($user)
    {
        $existingSubscribers = $user->subscribers()->count();
        $targetSubscribers = rand(50, 500);
        
        // Se ci sono già abbastanza iscritti, non aggiungerne altri
        if ($existingSubscribers >= $targetSubscribers) {
            return;
        }
        
        $neededSubscribers = $targetSubscribers - $existingSubscribers;
        $otherUsers = User::where('id', '!=', $user->id)->get();
        
        if ($otherUsers->isEmpty()) {
            return;
        }
        
        for ($i = 0; $i < min($neededSubscribers, 20); $i++) {
            $subscriber = $otherUsers->random();
            
            // Evita duplicati
            if (!$user->subscribers()->where('subscriber_id', $subscriber->id)->exists()) {
                Subscription::create([
                    'subscriber_id' => $subscriber->id,
                    'channel_id' => $user->id,
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
            }
        }
    }

    private function seedFakeComments($user, $videos)
    {
        if ($videos->isEmpty()) {
            return;
        }
        
        $otherUsers = User::where('id', '!=', $user->id)->get();
        
        if ($otherUsers->isEmpty()) {
            return;
        }
        
        $comments = [
            "Ottimo video! Grazie per il contenuto.",
            "Molto interessante, continua così!",
            "Mi è piaciuto molto questo video.",
            "Video fantastico, commento sempre i tuoi contenuti.",
            "Grazie per aver condiviso questo!",
            "Bellissimo video, continua così!",
            "Contenuto di qualità come sempre!",
            "Mi hai aiutato molto con questo video.",
            "Ottima spiegazione, molto chiara!",
            "Video perfetto, ho imparato tanto!"
        ];
        
        $commentCount = rand(10, 50);
        
        for ($i = 0; $i < $commentCount; $i++) {
            $video = $videos->random();
            $commenter = $otherUsers->random();
            
            Comment::create([
                'user_id' => $commenter->id,
                'video_id' => $video->id,
                'content' => $comments[array_rand($comments)],
                'status' => 'approved',
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }
}