<?php

use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\PlaylistManagementController;
use App\Http\Controllers\Api\V1\PremiumSubscriptionController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\StudioController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VideoUploadController;
use App\Http\Controllers\Api\WatchLaterController;
use App\Models\Video;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Legacy API (kept for compatibility)
    |--------------------------------------------------------------------------
    */
    Route::get('/watch-later', [WatchLaterController::class, 'index']);
    Route::post('/watch-later/{video}', [WatchLaterController::class, 'store']);
    Route::delete('/watch-later/{video}', [WatchLaterController::class, 'destroy']);

    Route::get('/playlist/shuffle', [PlaylistController::class, 'shuffle']);
    Route::get('/search', [SearchController::class, 'globalSearch'])->name('api.global.search');

    Route::post('/video-analytics', function () {
        return response()->json(['success' => true]);
    });

    Route::post('/ad-analytics', function () {
        return response()->json(['success' => true]);
    });

    /*
    |--------------------------------------------------------------------------
    | Flutter-ready API v1
    |--------------------------------------------------------------------------
    */
    Route::prefix('v1')->group(function () {
        // Public endpoints - NO authentication required
        Route::get('/app-config', [ContentController::class, 'appConfig'])->withoutMiddleware('api.token');
        Route::get('/home', [ContentController::class, 'home']);
        Route::get('/videos', [ContentController::class, 'videos']);
        Route::get('/videos/{video}', [ContentController::class, 'showVideo']);
        Route::get('/videos/{video}/comments', [ContentController::class, 'videoComments']);
        Route::get('/creators', [ContentController::class, 'creators']);
        Route::get('/creators/{creator}', [ContentController::class, 'showCreator']);
        Route::get('/creators/{creator}/videos', [ContentController::class, 'creatorVideos']);
        Route::get('/search', [ContentController::class, 'search']);
        Route::get('/premium/plans', [PremiumSubscriptionController::class, 'plans']);

        // App Settings - Public
        Route::get('/settings', [SettingsController::class, 'appSettings']);
        Route::get('/settings/features', [SettingsController::class, 'featureFlags']);
        Route::get('/settings/limits', [SettingsController::class, 'limits']);
        Route::get('/settings/countries', [SettingsController::class, 'countries']);
        Route::get('/settings/languages', [SettingsController::class, 'languages']);

        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/login', [AuthController::class, 'login']);

        Route::middleware('api.token')->group(function () {
            // Authenticated - auth/session
            Route::post('/auth/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AccountController::class, 'me']);
            Route::put('/me', [AccountController::class, 'updateMe']);

            // Authenticated - content actions
            Route::post('/videos/{video}/comments', [ContentController::class, 'addComment']);
            Route::post('/videos/{video}/reaction', [ContentController::class, 'reactToVideo']);
            Route::post('/creators/{creator}/subscribe', [ContentController::class, 'toggleCreatorSubscription']);
            Route::get('/reports/reasons', [StudioController::class, 'reportReasons']);
            Route::post('/reports', [StudioController::class, 'createReport']);

            // Authenticated - user library
            Route::get('/me/watch-later', [AccountController::class, 'watchLater']);
            Route::post('/me/watch-later/{video}', [AccountController::class, 'addToWatchLater']);
            Route::delete('/me/watch-later/{video}', [AccountController::class, 'removeFromWatchLater']);

            Route::get('/me/history', [AccountController::class, 'watchHistory']);
            Route::post('/me/history/{video}', [AccountController::class, 'upsertWatchHistory']);

            Route::get('/me/playlists', [AccountController::class, 'playlists']);
            Route::post('/me/playlists', [PlaylistManagementController::class, 'store']);
            Route::get('/me/playlists/{playlist}', [PlaylistManagementController::class, 'show']);
            Route::put('/me/playlists/{playlist}', [PlaylistManagementController::class, 'update']);
            Route::delete('/me/playlists/{playlist}', [PlaylistManagementController::class, 'destroy']);
            Route::post('/me/playlists/{playlist}/videos', [PlaylistManagementController::class, 'addVideo']);
            Route::delete('/me/playlists/{playlist}/videos/{video}', [PlaylistManagementController::class, 'removeVideo']);

            // Premium
            Route::get('/me/premium', [PremiumSubscriptionController::class, 'status']);
            Route::post('/me/premium/checkout', [PremiumSubscriptionController::class, 'checkout']);
            Route::post('/me/premium/confirm', [PremiumSubscriptionController::class, 'confirm']);
            Route::post('/me/premium/portal', [PremiumSubscriptionController::class, 'portal']);

            // Authenticated - notifications
            Route::get('/me/notifications', [AccountController::class, 'notifications']);
            Route::post('/me/notifications/read-all', [AccountController::class, 'readAllNotifications']);
            Route::post('/me/notifications/{id}/read', [AccountController::class, 'readNotification']);

            // My Channel
            Route::get('/me/channel', [UserController::class, 'myChannel']);
            Route::put('/me/channel', [UserController::class, 'updateChannel']);
            Route::put('/me/avatar', [UserController::class, 'updateAvatar']);
            Route::put('/me/banner', [UserController::class, 'updateBanner']);

            // Subscriptions
            Route::get('/me/subscriptions', [UserController::class, 'mySubscriptions']);
            Route::get('/me/subscribers', [UserController::class, 'mySubscribers']);

            // My Videos
            Route::get('/me/videos', [UserController::class, 'myVideos']);
            Route::get('/me/liked', [UserController::class, 'myLikedVideos']);
            Route::get('/me/comments', [UserController::class, 'myComments']);

            // User Settings
            Route::get('/me/settings', [UserController::class, 'settings']);
            Route::put('/me/settings', [UserController::class, 'updateSettings']);

            // Studio
            Route::get('/me/studio/summary', [StudioController::class, 'summary']);
            Route::get('/me/studio/analytics', [StudioController::class, 'analytics']);
            Route::get('/me/studio/analytics/videos/{video}', [StudioController::class, 'videoAnalytics']);
            Route::get('/me/studio/community', [StudioController::class, 'community']);
            Route::post('/me/studio/community/comments/{comment}/approve', [StudioController::class, 'approveComment']);
            Route::post('/me/studio/community/comments/{comment}/reject', [StudioController::class, 'rejectComment']);
            Route::post('/me/studio/community/comments/{comment}/hide', [StudioController::class, 'hideComment']);
            Route::get('/me/studio/reports', [StudioController::class, 'reports']);
            Route::get('/me/studio/feedback', [StudioController::class, 'feedback']);
            Route::post('/me/studio/feedback/{feedback}/read', [StudioController::class, 'markFeedbackAsRead']);

            // Video Upload
            Route::post('/videos/upload', [VideoUploadController::class, 'uploadVideo']);
            Route::post('/reels/upload', [VideoUploadController::class, 'uploadReel']);
            Route::post('/posts/create', [VideoUploadController::class, 'createPost']);
            Route::put('/videos/{video}', [VideoUploadController::class, 'updateVideo']);
            Route::delete('/videos/{video}', [VideoUploadController::class, 'deleteVideo']);
            Route::get('/videos/{video}/status', [VideoUploadController::class, 'checkVideoStatus']);
            Route::post('/videos/bulk', [VideoUploadController::class, 'bulkUpdate']);

            // Comments
            Route::post('/comments/{comment}/like', [UserController::class, 'likeComment']);
            Route::delete('/comments/{comment}', [UserController::class, 'deleteComment']);

            // Share
            Route::post('/videos/{video}/share', function (string $video) {
                $video = Video::where('status', 'published')
                    ->where('is_public', true)
                    ->where(function ($query) use ($video) {
                        $query->where('video_url', $video);
                        if (is_numeric($video)) {
                            $query->orWhere('id', (int) $video);
                        }
                    })
                    ->firstOrFail();

                return response()->json([
                    'share_url' => route('videos.show', $video),
                    'share_title' => $video->title,
                    'share_description' => $video->description,
                    'share_thumbnail' => $video->thumbnail_url,
                ]);
            });
        });
    });
});
