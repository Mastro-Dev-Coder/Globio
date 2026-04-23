<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SettingSeeder::class);
        $this->call(DemoContentSeeder::class);
        $this->call(AnalyticsSeeder::class);
        $this->call(ReelSeeder::class);
        $this->call(PlaylistSeeder::class);
    }
}
