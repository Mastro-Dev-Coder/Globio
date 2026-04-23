<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function appSettings()
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'logo_url' => asset('logo.png'),
            'favicon_url' => asset('favicon.ico'),
            'primary_color' => Setting::getValue('primary_color', '#6366f1'),
            'secondary_color' => Setting::getValue('secondary_color', '#8b5cf6'),
            'accent_color' => Setting::getValue('accent_color', '#ec4899'),
            'dark_mode' => (bool) Setting::getValue('dark_mode', false),
            'watermark_enabled' => (bool) Setting::getValue('watermark_enabled', false),
            'watermark_text' => Setting::getValue('watermark_text', ''),
            'max_video_upload_mb' => (int) Setting::getValue('max_video_upload_mb', 500),
            'max_video_duration_seconds' => (int) Setting::getValue('max_video_duration_seconds', 3600),
            'enable_comments' => (bool) Setting::getValue('enable_comments', true),
            'enable_likes' => (bool) Setting::getValue('enable_likes', true),
            'enable_shares' => (bool) Setting::getValue('enable_shares', true),
            'enable_downloads' => (bool) Setting::getValue('enable_downloads', false),
            'require_approval' => (bool) Setting::getValue('require_approval', false),
            'enable_reels' => (bool) Setting::getValue('enable_reels', true),
            'enable_livestreams' => (bool) Setting::getValue('enable_livestreams', false),
            'enable_playlists' => (bool) Setting::getValue('enable_playlists', true),
            'enable_playlists_public' => (bool) Setting::getValue('enable_playlists_public', true),
            'default_language' => Setting::getValue('default_language', 'en'),
            'supported_languages' => explode(',', Setting::getValue('supported_languages', 'en,it,es,fr,de,pt,ru,zh,ja,ko')),
            'default_country' => Setting::getValue('default_country', 'US'),
            'contact_email' => Setting::getValue('contact_email', 'support@example.com'),
            'support_email' => Setting::getValue('support_email', 'support@example.com'),
            'privacy_policy_url' => Setting::getValue('privacy_policy_url', ''),
            'terms_of_service_url' => Setting::getValue('terms_of_service_url', ''),
            'about_page_content' => Setting::getValue('about_page_content', ''),
            'footer_text' => Setting::getValue('footer_text', ''),
            'facebook_url' => Setting::getValue('facebook_url', ''),
            'twitter_url' => Setting::getValue('twitter_url', ''),
            'instagram_url' => Setting::getValue('instagram_url', ''),
            'youtube_url' => Setting::getValue('youtube_url', ''),
            'tiktok_url' => Setting::getValue('tiktok_url', ''),
            'twitch_url' => Setting::getValue('twitch_url', ''),
            'discord_url' => Setting::getValue('discord_url', ''),
            'advertisements_enabled' => (bool) Setting::getValue('advertisements_enabled', true),
            'google_adsense_client_id' => Setting::getValue('google_adsense_client_id', ''),
            'google_adsense_slot_id' => Setting::getValue('google_adsense_slot_id', ''),
            'analytics_enabled' => (bool) Setting::getValue('analytics_enabled', false),
            'analytics_tracking_id' => Setting::getValue('analytics_tracking_id', ''),
            'maintenance_mode' => (bool) Setting::getValue('maintenance_mode', false),
            'maintenance_message' => Setting::getValue('maintenance_message', 'Site under maintenance'),
            'registration_enabled' => (bool) Setting::getValue('registration_enabled', true),
            'social_login_enabled' => (bool) Setting::getValue('social_login_enabled', false),
            'google_client_id' => Setting::getValue('google_client_id', ''),
            'facebook_client_id' => Setting::getValue('facebook_client_id', ''),
        ];

        return response()->json($settings);
    }

    public function featureFlags()
    {
        return response()->json([
            'auth' => true,
            'videos' => true,
            'reels' => true,
            'shorts' => true,
            'comments' => (bool) Setting::getValue('enable_comments', true),
            'likes' => (bool) Setting::getValue('enable_likes', true),
            'shares' => (bool) Setting::getValue('enable_shares', true),
            'downloads' => (bool) Setting::getValue('enable_downloads', false),
            'watch_later' => true,
            'playlists' => (bool) Setting::getValue('enable_playlists', true),
            'subscriptions' => true,
            'channels' => true,
            'notifications' => true,
            'livestreams' => (bool) Setting::getValue('enable_livestreams', false),
            'analytics' => (bool) Setting::getValue('analytics_enabled', false),
            'advertisements' => (bool) Setting::getValue('advertisements_enabled', true),
            'dark_mode' => (bool) Setting::getValue('dark_mode', false),
            'chat' => false,
            'stories' => false,
            'live_chat' => false,
        ]);
    }

    public function limits()
    {
        return response()->json([
            'max_video_upload_mb' => (int) Setting::getValue('max_video_upload_mb', 500),
            'max_video_duration_seconds' => (int) Setting::getValue('max_video_duration_seconds', 3600),
            'max_thumbnail_size_mb' => 5,
            'max_avatar_size_mb' => 5,
            'max_banner_size_mb' => 10,
            'max_description_length' => 2000,
            'max_tags_count' => 20,
            'max_playlist_videos' => 100,
            'max_comment_length' => 1000,
            'max_replies_depth' => 2,
            'video_cache_hours' => 24,
            'history_days' => 365,
        ]);
    }

    public function countries()
    {
        return response()->json([
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'FR' => 'France',
            'DE' => 'Germany',
            'PT' => 'Portugal',
            'RU' => 'Russia',
            'CN' => 'China',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'IN' => 'India',
            'BR' => 'Brazil',
            'MX' => 'Mexico',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'NL' => 'Netherlands',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'PL' => 'Poland',
            'UA' => 'Ukraine',
            'TR' => 'Turkey',
        ]);
    }

    public function languages()
    {
        return response()->json([
            'en' => 'English',
            'it' => 'Italian',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
            'hi' => 'Hindi',
            'tr' => 'Turkish',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'sv' => 'Swedish',
            'no' => 'Norwegian',
            'fi' => 'Finnish',
            'da' => 'Danish',
        ]);
    }
}