<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Advertising Services
    |--------------------------------------------------------------------------
    |
    | Configuration for video advertising services including VAST, VMAP,
    | Google AdSense and other video ad platforms.
    |
    */

    'google_adsense' => [
        'ad_client' => env('GOOGLE_ADSENSE_AD_CLIENT', ''),
        'ad_slot' => env('GOOGLE_ADSENSE_AD_SLOT', ''),
        'ad_format' => env('GOOGLE_ADSENSE_AD_FORMAT', 'video'),
        'ad_theme' => env('GOOGLE_ADSENSE_AD_THEME', 'dark'),
        'ad_language' => env('GOOGLE_ADSENSE_AD_LANGUAGE', 'it'),
        'ad_platform' => env('GOOGLE_ADSENSE_AD_PLATFORM', 'mobile'),
        'test_mode' => env('GOOGLE_ADSENSE_TEST_MODE', false),
    ],

    'vast' => [
        'user_agent' => env('VAST_USER_AGENT', 'Globio-Video-Player/1.0'),
        'timeout' => env('VAST_TIMEOUT', 10),
        'cache_ttl' => env('VAST_CACHE_TTL', 300), // 5 minuti
    ],

    'vmap' => [
        'user_agent' => env('VMAP_USER_AGENT', 'Globio-Video-Player/1.0'),
        'timeout' => env('VMAP_TIMEOUT', 10),
        'cache_ttl' => env('VMAP_CACHE_TTL', 300), // 5 minuti
    ],

    'video_ads' => [
        'cache_enabled' => env('VIDEO_ADS_CACHE_ENABLED', true),
        'cache_ttl' => env('VIDEO_ADS_CACHE_TTL', 300), // 5 minuti
        'max_ads_per_video' => env('VIDEO_ADS_MAX_PER_VIDEO', 3),
        'skip_delay' => env('VIDEO_ADS_SKIP_DELAY', 5),
        'frequency_cap' => env('VIDEO_ADS_FREQUENCY_CAP', 1),
    ],

];
