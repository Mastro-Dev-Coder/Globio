<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Subscription;
use App\Observers\CommentObserver;
use App\Observers\SubscriptionObserver;
use Illuminate\Notifications\Events\NotificationSent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (DB::connection()->getDatabaseName() && $this->settingsTableExists()) {
            $this->ensureDefaultSettings();
        }
        
        View::composer('*', function ($view) {
            $colors = [
                'primary_color' => Setting::getValue('primary_color', '#dc2626'),
                'primary_color_light' => Setting::getValue('primary_color_light', '#ef4444'),
                'primary_color_dark' => Setting::getValue('primary_color_dark', '#b91c1c'),
                'accent_color' => Setting::getValue('accent_color', '#dc2626'),
                'accent_color_light' => Setting::getValue('accent_color_light', '#ef4444'),
                'accent_color_dark' => Setting::getValue('accent_color_dark', '#b91c1c'),
            ];
            
            $view->with('dynamic_colors', $colors);
        });

        \Illuminate\Support\Facades\Event::listen(NotificationSent::class, function (NotificationSent $event) {
            if ($event->channel === 'database') {
                try {
                    $data = method_exists($event->notification, 'toArray')
                        ? $event->notification->toArray($event->notifiable)
                        : [];
                    $url = $data['url'] ?? $data['action_url'] ?? null;
                    
                    Log::info('NotificationSent event', [
                        'channel' => $event->channel,
                        'notification_type' => get_class($event->notification),
                        'url_from_data' => $url,
                        'response' => $event->response
                    ]);
                    
                    if ($url && isset($event->response['id'])) {
                        $updated = DB::table('notifications')
                            ->where('id', $event->response['id'])
                            ->update(['url' => $url]);
                        
                        Log::info('Updated notification url', [
                            'notification_id' => $event->response['id'],
                            'url' => $url,
                            'rows_updated' => $updated
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error updating notification url: ' . $e->getMessage(), [
                        'exception' => $e->getTraceAsString()
                    ]);
                }
            }
        });
    }

    /**
     * Verifica se la tabella settings esiste
     */
    private function settingsTableExists(): bool
    {
        try {
            return DB::connection()->getSchemaBuilder()->hasTable('settings');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Assicura che i settings di default siano presenti nel database
     */
    private function ensureDefaultSettings(): void
    {
        $existingSettingsCount = Setting::count();
        
        if ($existingSettingsCount === 0) {
            $this->insertDefaultSettings();
        }
    }

    /**
     * Inserisce le impostazioni di default
     */
    private function insertDefaultSettings(): void
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
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value !== null ? (string) $value : null,
            ]);
        }
    }
}
