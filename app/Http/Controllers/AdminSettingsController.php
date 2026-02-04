<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => Setting::getValue('site_name', env('APP_NAME', 'Globio')),
            'max_upload_size' => Setting::getValue('max_upload_size', 512),
            'require_approval' => Setting::getValue('require_approval', false),
            'enable_comments' => Setting::getValue('enable_comments', true),
            'enable_likes' => Setting::getValue('enable_likes', true),

            'primary_color' => Setting::getValue('primary_color', '#dc2626'),
            'primary_color_light' => Setting::getValue('primary_color_light', '#ef4444'),
            'primary_color_dark' => Setting::getValue('primary_color_dark', '#b91c1c'),
            'accent_color' => Setting::getValue('accent_color', '#dc2626'),
            'accent_color_light' => Setting::getValue('accent_color_light', '#ef4444'),
            'accent_color_dark' => Setting::getValue('accent_color_dark', '#b91c1c'),

            'ffmpeg_enabled' => Setting::getValue('ffmpeg_enabled', true),
            'ffmpeg_path' => Setting::getValue('ffmpeg_path'),
            'ffprobe_path' => Setting::getValue('ffprobe_path'),
            'ffmpeg_timeout' => Setting::getValue('ffmpeg_timeout', 3600),
            'ffmpeg_threads' => Setting::getValue('ffmpeg_threads', 4),

            'video_qualities' => Setting::getValue('video_qualities', '720p,1080p,original'),
            'default_video_quality' => Setting::getValue('default_video_quality', '720p'),
            'max_video_duration' => Setting::getValue('max_video_duration', 3600),
            'max_file_size' => Setting::getValue('max_file_size', 1000000000),
            'max_video_upload_mb' => Setting::getValue('max_video_upload_mb', 500),

            'thumbnail_quality' => Setting::getValue('thumbnail_quality', 85),
            'thumbnail_width' => Setting::getValue('thumbnail_width', 1280),
            'thumbnail_height' => Setting::getValue('thumbnail_height', 720),

            'enable_transcoding' => Setting::getValue('enable_transcoding', true),
            'auto_publish' => Setting::getValue('auto_publish', true),
            'delete_original_after_transcoding' => Setting::getValue('delete_original_after_transcoding', false),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function showGeneralSettings()
    {
        $settings = [
            'site_name' => Setting::getValue('site_name', env('APP_NAME', 'Globio')),
            'max_upload_size' => Setting::getValue('max_upload_size', 512),
            'require_approval' => Setting::getValue('require_approval', false),
            'enable_comments' => Setting::getValue('enable_comments', true),
            'enable_likes' => Setting::getValue('enable_likes', true),

            'primary_color' => Setting::getValue('primary_color', '#dc2626'),
            'primary_color_light' => Setting::getValue('primary_color_light', '#ef4444'),
            'primary_color_dark' => Setting::getValue('primary_color_dark', '#b91c1c'),
            'accent_color' => Setting::getValue('accent_color', '#dc2626'),
            'accent_color_light' => Setting::getValue('accent_color_light', '#ef4444'),
            'accent_color_dark' => Setting::getValue('accent_color_dark', '#b91c1c'),

            'smtp_host' => Setting::getValue('smtp_host', env('MAIL_HOST')),
            'smtp_port' => Setting::getValue('smtp_port', env('MAIL_PORT', 587)),
            'smtp_username' => Setting::getValue('smtp_username', env('MAIL_USERNAME')),
            'smtp_password' => Setting::getValue('smtp_password', env('MAIL_PASSWORD')),
            'smtp_encryption' => Setting::getValue('smtp_encryption', env('MAIL_ENCRYPTION', 'tls')),
            'from_address' => Setting::getValue('from_address', env('MAIL_FROM_ADDRESS')),

            'logo' => Setting::getValue('logo'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'max_upload_size' => 'required|integer|min:50|max:2000',
            'require_approval' => 'boolean',
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'primary_color_light' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'primary_color_dark' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color_light' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color_dark' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'custom_logo' => 'sometimes|file|mimes:jpg,jpeg,png,svg|max:2048',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,null',
            'from_address' => 'nullable|email',
        ]);

        $generalSettings = [
            'site_name' => $request->site_name,
            'max_upload_size' => $request->max_upload_size,
            'require_approval' => $request->boolean('require_approval'),
        ];

        $colorSettings = [
            'primary_color' => $request->primary_color,
            'primary_color_light' => $request->primary_color_light,
            'primary_color_dark' => $request->primary_color_dark,
            'accent_color' => $request->accent_color,
            'accent_color_light' => $request->accent_color_light,
            'accent_color_dark' => $request->accent_color_dark,
        ];

        $logoSettings = [];
        if ($request->has('remove_logo') && $request->remove_logo) {
            $existingLogoPath = Setting::getValue('logo');
            if ($existingLogoPath && Storage::exists($existingLogoPath)) {
                Storage::delete($existingLogoPath);
            }
            $logoSettings['logo'] = null;
        } elseif ($request->hasFile('custom_logo') && $request->file('custom_logo')->isValid()) {
            $logo = $request->file('custom_logo');
            $logoFileName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('logos', $logoFileName, 'public');

            $existingLogoPath = Setting::getValue('logo');
            if ($existingLogoPath && Storage::exists($existingLogoPath)) {
                Storage::delete($existingLogoPath);
            }

            $logoSettings['logo'] = $logoPath;
        }

        $allSettings = array_merge($generalSettings, $colorSettings, $logoSettings);

        foreach ($allSettings as $key => $value) {
            if (in_array($key, ['require_approval']) || $request->filled($key) || $key === 'logo') {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        if ($request->filled('site_name') && $request->site_name !== config('app.name')) {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);
                if (strpos($envContent, 'APP_NAME=') !== false) {
                    $envContent = preg_replace('/APP_NAME=.*/', 'APP_NAME=' . $request->site_name, $envContent);
                } else {
                    $envContent .= "\nAPP_NAME=" . $request->site_name;
                }
                file_put_contents($envPath, $envContent);
            }
        }

        if ($request->hasAny(['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'from_address'])) {
            $this->updateSmtpSettings($request);
        }

        Cache::forget('general_settings');
        Cache::forget('color_settings');

        return redirect()->route('admin.settings')
            ->with('success', 'Impostazioni aggiornate con successo!');
    }

    private function updateSmtpSettings(Request $request)
    {
        $smtpSettings = [
            'smtp_host' => $request->smtp_host,
            'smtp_port' => $request->smtp_port,
            'smtp_username' => $request->smtp_username,
            'smtp_password' => $request->smtp_password,
            'smtp_encryption' => $request->smtp_encryption,
            'from_address' => $request->from_address,
        ];

        foreach ($smtpSettings as $key => $value) {
            if ($request->filled($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }
    }

    public function updateFFmpeg(Request $request)
    {
        $request->validate([
            'ffmpeg_enabled' => 'boolean',
            'ffmpeg_path' => 'nullable|string|max:255',
            'ffprobe_path' => 'nullable|string|max:255',
            'ffmpeg_timeout' => 'integer|min:60|max:7200',
            'ffmpeg_threads' => 'integer|min:1|max:16',
        ]);

        $settings = [
            'ffmpeg_enabled' => $request->boolean('ffmpeg_enabled'),
            'ffmpeg_path' => $request->ffmpeg_path,
            'ffprobe_path' => $request->ffprobe_path,
            'ffmpeg_timeout' => $request->ffmpeg_timeout,
            'ffmpeg_threads' => $request->ffmpeg_threads,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('ffmpeg_settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Impostazioni FFmpeg aggiornate con successo!');
    }

    public function updateVideoQualities(Request $request)
    {
        $request->validate([
            'video_qualities' => 'required|string',
            'default_video_quality' => 'required|string',
            'max_video_duration' => 'required|integer|min:60',
            'max_file_size' => 'required|integer|min:1024',
            'max_video_upload_mb' => 'required|integer|min:10|max:5000',
        ]);

        $settings = [
            'video_qualities' => $request->video_qualities,
            'default_video_quality' => $request->default_video_quality,
            'max_video_duration' => $request->max_video_duration,
            'max_file_size' => $request->max_file_size,
            'max_video_upload_mb' => $request->max_video_upload_mb,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Impostazioni qualità video aggiornate con successo!');
    }

    public function updateThumbnails(Request $request)
    {
        $request->validate([
            'thumbnail_quality' => 'required|integer|min:50|max:100',
            'thumbnail_width' => 'required|integer|min:320|max:1920',
            'thumbnail_height' => 'required|integer|min:240|max:1080',
        ]);

        $settings = [
            'thumbnail_quality' => $request->thumbnail_quality,
            'thumbnail_width' => $request->thumbnail_width,
            'thumbnail_height' => $request->thumbnail_height,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Impostazioni thumbnail aggiornate con successo!');
    }

    public function updateTranscoding(Request $request)
    {
        $request->validate([
            'enable_transcoding' => 'boolean',
            'auto_publish' => 'boolean',
            'delete_original_after_transcoding' => 'boolean',
        ]);

        $settings = [
            'enable_transcoding' => $request->boolean('enable_transcoding'),
            'auto_publish' => $request->boolean('auto_publish'),
            'delete_original_after_transcoding' => $request->boolean('delete_original_after_transcoding'),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Impostazioni transcodifica aggiornate con successo!');
    }

    public function testFFmpeg()
    {
        $ffmpegPath = Setting::getValue('ffmpeg_path');
        $ffprobePath = Setting::getValue('ffprobe_path');

        $results = [
            'ffmpeg' => [
                'available' => file_exists($ffmpegPath),
                'path' => $ffmpegPath,
                'version' => null,
            ],
            'ffprobe' => [
                'available' => file_exists($ffprobePath),
                'path' => $ffprobePath,
                'version' => null,
            ],
        ];

        try {
            if ($results['ffmpeg']['available']) {
                $versionOutput = shell_exec($ffmpegPath . ' -version 2>&1');
                if (preg_match('/ffmpeg version (\S+)/', $versionOutput, $matches)) {
                    $results['ffmpeg']['version'] = $matches[1];
                }
            }

            if ($results['ffprobe']['available']) {
                $versionOutput = shell_exec($ffprobePath . ' -version 2>&1');
                if (preg_match('/ffprobe version (\S+)/', $versionOutput, $matches)) {
                    $results['ffprobe']['version'] = $matches[1];
                }
            }
        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
        }

        return response()->json($results);
    }

    public function migrateSettings()
    {
        try {
            $envSettings = [
                'FFMPEG_PATH' => 'ffmpeg_path',
                'FFPROBE_PATH' => 'ffprobe_path',
            ];

            foreach ($envSettings as $envKey => $settingKey) {
                $envValue = env($envKey);
                if ($envValue && !Setting::where('key', $settingKey)->exists()) {
                    Setting::create([
                        'key' => $settingKey,
                        'value' => $envValue,
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Impostazioni migrate con successo']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
