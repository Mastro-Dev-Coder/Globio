<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeder per inserire le impostazioni di default.
     */
    public function run(): void
    {
        $defaultSettings = [
            'site_name' => env('APP_NAME', 'Globio'),
            'max_upload_size' => '512',
            'require_approval' => 'false',
            'enable_comments' => 'true',
            'enable_likes' => 'true',

            'primary_color' => '#dc2626',
            'primary_color_light' => '#ef4444',
            'primary_color_dark' => '#b91c1c',
            'accent_color' => '#dc2626',
            'accent_color_light' => '#ef4444',
            'accent_color_dark' => '#b91c1c',

            'ffmpeg_enabled' => 'true',
            'ffmpeg_path' => null,
            'ffprobe_path' => null,
            'ffmpeg_timeout' => '3600',
            'ffmpeg_threads' => '4',

            'video_qualities' => '720p,1080p,original',
            'default_video_quality' => '720p',
            'max_video_duration' => '3600',
            'max_file_size' => '1000000000',

            'thumbnail_quality' => '85',
            'thumbnail_width' => '1280',
            'thumbnail_height' => '720',

            'enable_transcoding' => 'true',
            'auto_publish' => 'true',
            'delete_original_after_transcoding' => 'false',

            'smtp_host' => env('MAIL_HOST'),
            'smtp_port' => env('MAIL_PORT', '587'),
            'smtp_username' => env('MAIL_USERNAME'),
            'smtp_password' => env('MAIL_PASSWORD'),
            'smtp_encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'from_address' => env('MAIL_FROM_ADDRESS'),

            // Impostazioni Ads e Monetizzazione
            'ads.global_enabled' => '1',
            'ads.pre_roll_enabled' => '1',
            'ads.mid_roll_enabled' => '1',
            'ads.post_roll_enabled' => '1',
            'ads.pre_roll_duration' => '15',
            'ads.mid_roll_interval' => '300',
            'ads.skip_after' => '5',
            'ads.min_creator_subscribers' => '1000',
            'ads.platform_revenue_share' => '45',
            
            // Impostazioni VAST e VMAP
            'ads_vast_source_type' => 'url',
            'ads_vmap_source_type' => 'url',
            'ads_vast_enabled' => '1',
            'ads_vmap_enabled' => '0',
            'ads_google_adsense_enabled' => '0',
            'ads_skip_delay' => '5',
            'ads_mid_roll_positions' => '25,50,75',
            'ads_max_per_video' => '3',
            'ads_frequency_cap' => '1',
            'ads_vast_clickthrough_url' => '',
            'ads_vast_custom_text' => '',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value !== null ? (string) $value : null]
            );
        }

        $this->command->info('Impostazioni di default inserite con successo!');
    }
}