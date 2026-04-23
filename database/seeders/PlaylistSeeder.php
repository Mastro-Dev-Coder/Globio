<?php

namespace Database\Seeders;

use App\Models\Playlist;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

class PlaylistSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $videos = Video::published()->get();
        
        if ($users->isEmpty() || $videos->isEmpty()) {
            $this->command->info('Skipping PlaylistSeeder: No users or videos found.');
            return;
        }
        
        $playlistTitles = [
            'Top Gaming Videos',
            'Music Compilation',
            'Tech Tutorials',
            'Cooking Recipes',
            'Travel Vlogs',
            'Comedy Sketches',
            'Educational Content',
            'Fitness Tips'
        ];
        
        $playlistDescriptions = [
            'The best gaming videos of the month',
            'Top hits from various artists',
            'Learn programming and technology',
            'Delicious recipes for every occasion',
            'Amazing travel destinations around the world',
            'Hilarious comedy sketches and pranks',
            'Educational videos for all ages',
            'Fitness and workout routines'
        ];
        
        foreach ($playlistTitles as $index => $title) {
            $user = $users->random();
            $playlist = Playlist::create([
                'user_id' => $user->id,
                'title' => $title,
                'description' => $playlistDescriptions[$index],
                'is_public' => true,
                'video_count' => 0,
                'views_count' => rand(100, 5000)
            ]);
            
            $selectedVideos = $videos->random(rand(3, 10));
            $position = 1;
            
            foreach ($selectedVideos as $video) {
                $playlist->videos()->attach($video->id, ['position' => $position]);
                $position++;
            }
            
            $playlist->update(['video_count' => $selectedVideos->count()]);
            
            $this->command->info("Created playlist: {$playlist->title} with {$playlist->video_count} videos");
        }
        
        $this->command->info('PlaylistSeeder completed successfully');
    }
}
