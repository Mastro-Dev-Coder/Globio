<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdvancedAnalyticsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DynamicStyleController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\AdminLegalController;
use App\Http\Controllers\CommentModerationController;
use App\Http\Controllers\ReportsManagementController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\VideoReportController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\Api\VideoAdsController;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

// Fortify Authentication Routes
if (Features::enabled(Features::registration())) {
    Route::get('/register', [RegisteredUserController::class, 'create'])->middleware(['guest'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware(['guest']);
}

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware(['guest'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware(['auth'])->name('logout');

// Route for generating dynamic CSS 
Route::get('/dynamic-styles.css', [DynamicStyleController::class, 'generateDynamicCSS'])->name('dynamic.styles');

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [HomeController::class, 'explore'])->name('explore');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/trending', [HomeController::class, 'trending'])->name('trending');
Route::get('/latest', [HomeController::class, 'latest'])->name('latest');

// AJAX Routes per scroll infinito e filtri
Route::get('/home/load-more', [HomeController::class, 'loadMore'])->name('home.load-more');
Route::get('/home/filter', [HomeController::class, 'filter'])->name('home.filter');

// Legal Pages Routes
Route::get('/contact', [LegalController::class, 'contact'])->name('contact');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::post('/contact/send', [LegalController::class, 'sendContact'])->name('send.contact');

// Video Routes
Route::get('/videos/{video:video_url}', [VideoController::class, 'show'])->name('videos.show');

// Reel Routes
Route::get('/reels', [VideoController::class, 'reelsIndex'])->name('reels.index');
Route::get('/reels/{video}', [VideoController::class, 'showReel'])->name('reels.show');

// API Routes per reels
Route::get('/api/video/{video}/adjacent-reel', [VideoController::class, 'getAdjacentReel'])->name('api.video.adjacent-reel');
Route::get('/api/video/{video}/random-reel', [VideoController::class, 'getRandomReel'])->name('api.video.random-reel');
Route::get('/videos/{video}/status', [VideoController::class, 'checkStatus'])->name('videos.status');
Route::post('/videos/{video}/like', [VideoController::class, 'like'])->name('videos.like');
Route::post('/videos/{video}/toggle-like', [VideoController::class, 'toggleLike'])->name('videos.toggle-like');
Route::get('/videos/{video:video_url}/download', [VideoController::class, 'download'])->name('videos.download');

// Public Channel Routes - ACCESSIBILI A TUTTI
Route::get('/channel/{channel_name}', [UserController::class, 'channel'])->name('channel.show');

// Protected Video Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/upload', [VideoController::class, 'store'])->name('videos.store');
    Route::get('/my-videos', [VideoController::class, 'myVideos'])->name('videos.my');
    Route::get('/videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
    Route::put('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::post('/videos/{video}/reprocess', [VideoController::class, 'reprocess'])->name('videos.reprocess');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::post('/videos/{video}/comments', [VideoController::class, 'storeComment'])->name('videos.comments.store');
    Route::post('/videos/{video}/comments/{comment}/like', [VideoController::class, 'likeComment'])->name('videos.comments.like');
    Route::delete('/videos/{video}/comments/{comment}', [VideoController::class, 'deleteComment'])->name('videos.comments.delete');
    Route::post('/videos/{video}/watch-progress', [VideoController::class, 'updateWatchProgress'])->name('videos.watch-progress');

    // Video Bulk Actions
    Route::post('/videos/bulk-update', [VideoController::class, 'bulkUpdate'])->name('videos.bulk-update');
    Route::delete('/videos/bulk-delete', [VideoController::class, 'bulkDelete'])->name('videos.bulk-delete');
    Route::post('/videos/{video}/toggle-privacy', [VideoController::class, 'togglePrivacy'])->name('videos.toggle-privacy');

    // Playlist Management Routes
    Route::get('/playlists-data', [PlaylistController::class, 'userPlaylists'])->name('playlists.data');
    Route::post('/playlists/create', [PlaylistController::class, 'store'])->name('playlists.store');
    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show'])->name('playlists.show');
    Route::put('/playlists/{playlist}', [PlaylistController::class, 'update'])->name('playlists.update');
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy'])->name('playlists.destroy');
    Route::post('/playlists/{playlist}/videos', [PlaylistController::class, 'addVideo'])->name('playlists.add-video');
    Route::delete('/playlists/{playlist}/videos/{video}', [PlaylistController::class, 'removeVideo'])->name('playlists.remove-video');
});

// User Routes
Route::middleware(['auth'])->group(function () {
    // Profile Management
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('/channel/edit/{channel_name?}', [UserController::class, 'channelEdit'])->name('channel.edit');
    Route::put('/channel/update', [UserController::class, 'updateChannel'])->name('channel.update-profile');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('users.update-profile');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::put('/profile/notifications', [UserController::class, 'updateNotifications'])->name('users.update-notifications');
    Route::put('/profile/preferences', [UserController::class, 'updatePreferences'])->name('users.update-preferences');
    Route::put('/profile/privacy', [UserController::class, 'updatePrivacy'])->name('users.update-privacy');
    Route::post('/channel/{channel_name}/subscribe', [UserController::class, 'subscribe'])->name('channel.subscribe');
    Route::post('/subscribe/{user}', [UserController::class, 'subscribe'])->name('subscribe');

    // User Library
    Route::get('/subscriptions', [UserController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/history', [UserController::class, 'watchHistory'])->name('history');
    Route::delete('/history/clear', [UserController::class, 'clearHistory'])->name('history.clear');
    Route::get('/liked-videos', [UserController::class, 'likedVideos'])->name('liked-videos');
    Route::get('/watch-later', [UserController::class, 'watchLater'])->name('watch-later');
    Route::post('/watch-later', [UserController::class, 'addToWatchLater'])->name('watch-later.add');
    Route::delete('/watch-later/{video}', [UserController::class, 'removeFromWatchLater'])->name('watch-later.remove');
    Route::get('/playlists', [UserController::class, 'playlists'])->name('playlists');

    // Comment Moderation Routes
    Route::post('/comments/{comment}/approve', [CommentModerationController::class, 'approve'])->name('comments.approve');
    Route::post('/comments/{comment}/reject', [CommentModerationController::class, 'reject'])->name('comments.reject');
    Route::post('/comments/{comment}/hide', [CommentModerationController::class, 'hide'])->name('comments.hide');
    Route::get('/videos/{video}/pending-comments', [CommentModerationController::class, 'pendingComments'])->name('videos.pending-comments');
    Route::post('/comments/bulk-approve', [CommentModerationController::class, 'bulkApprove'])->name('comments.bulk-approve');
    Route::post('/comments/bulk-reject', [CommentModerationController::class, 'bulkReject'])->name('comments.bulk-reject');

    // Analytics Routes
    Route::get('/advanced-analytics', [AdvancedAnalyticsController::class, 'index'])->name('advanced-analytics.index');
    Route::get('/analytics/video/{videoId}', [AdvancedAnalyticsController::class, 'videoAnalytics'])->name('analytics.video');
    Route::get('/analytics/compare', [AdvancedAnalyticsController::class, 'compare'])->name('analytics.compare');
    Route::get('/analytics/export', [AdvancedAnalyticsController::class, 'export'])->name('analytics.export');

    // Reports Routes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportsController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportsController::class, 'store'])->name('reports.store');
    Route::get('/reports/{id}', [ReportsController::class, 'show'])->name('reports.show');
    Route::delete('/reports/{id}/withdraw', [ReportsController::class, 'withdraw'])->name('reports.withdraw');

    // Creator Reports & Feedback Routes
    Route::get('/creator/reports', [ReportsController::class, 'creatorReports'])->name('creator.reports');
    Route::post('/creator/feedback/{id}/read', [ReportsController::class, 'markFeedbackAsRead'])->name('creator.feedback.read');
    Route::post('/creator/feedback/mark-all-read', [ReportsController::class, 'markAllFeedbackAsRead'])->name('creator.feedback.mark-all-read');

    // Notifications
    Route::get('/notifications', [UserController::class, 'notifications'])->name('user.notifications');
    Route::post('/notifications/mark-all-read', [UserController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
    Route::post('/api/notifications/{id}/read', [UserController::class, 'markNotificationAsRead'])->name('api.notifications.read');
    Route::get('/api/notifications/check-new', [UserController::class, 'checkNewNotifications'])->name('api.notifications.check-new');

    // Dashboard redirect
    Route::get('/dashboard', function () {
        return redirect()->route('users.profile');
    })->name('dashboard');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard (nuovo)
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/content', [AdminDashboardController::class, 'content'])->name('content');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');

    // User Management
    Route::get('/users-management', [AdminController::class, 'users'])->name('users-management');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Video Management
    Route::get('/videos-management', [AdminController::class, 'videos'])->name('videos-management');
    Route::post('/videos/{video}/moderate', [AdminController::class, 'moderateVideo'])->name('videos.moderate');
    Route::delete('/videos/{video}', [AdminController::class, 'deleteVideo'])->name('videos.delete');

    // Bulk Video Actions
    Route::post('/videos/bulk-approve', [AdminController::class, 'bulkApproveVideos'])->name('videos.bulk-approve');
    Route::post('/videos/bulk-reject', [AdminController::class, 'bulkRejectVideos'])->name('videos.bulk-reject');

    // Comment Management
    Route::get('/comments', [AdminController::class, 'comments'])->name('comments');
    Route::delete('/comments/{comment}', [AdminController::class, 'deleteComment'])->name('comments.delete');
    Route::post('/admin/comments/{comment}/approve', [CommentModerationController::class, 'approve'])->name('admin.comments.approve');
    Route::post('/admin/comments/{comment}/reject', [CommentModerationController::class, 'reject'])->name('admin.comments.reject');
    Route::post('/admin/comments/{comment}/hide', [CommentModerationController::class, 'hide'])->name('admin.comments.hide');

    // Statistics and Settings
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');

    // General Settings (unificato in AdminSettingsController)
    Route::get('/settings', [AdminSettingsController::class, 'showGeneralSettings'])->name('settings');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

    // FFmpeg Settings (nuovo menu)
    Route::get('/ffmpeg-settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/ffmpeg-settings/ffmpeg', [AdminSettingsController::class, 'updateFFmpeg'])->name('settings.ffmpeg');
    Route::put('/ffmpeg-settings/qualities', [AdminSettingsController::class, 'updateVideoQualities'])->name('settings.qualities');
    Route::put('/ffmpeg-settings/thumbnails', [AdminSettingsController::class, 'updateThumbnails'])->name('settings.thumbnails');
    Route::put('/ffmpeg-settings/transcoding', [AdminSettingsController::class, 'updateTranscoding'])->name('settings.transcoding');
    Route::post('/ffmpeg-settings/test', [AdminSettingsController::class, 'testFFmpeg'])->name('settings.test');
    Route::post('/ffmpeg-settings/migrate', [AdminSettingsController::class, 'migrateSettings'])->name('settings.migrate');

    // Reports Management (nuove viste admin)
    Route::get('/reports', [ReportsManagementController::class, 'index'])->name('reports');
    Route::get('/reports/{report}', [ReportsManagementController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/assign', [ReportsManagementController::class, 'assign'])->name('reports.assign');
    Route::post('/reports/{report}/resolve', [ReportsManagementController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/dismiss', [ReportsManagementController::class, 'dismiss'])->name('reports.dismiss');
    Route::post('/reports/{report}/escalate', [ReportsManagementController::class, 'escalate'])->name('reports.escalate');
    Route::post('/reports/bulk-action', [ReportsManagementController::class, 'bulkAction'])->name('reports.bulk-action');
    Route::get('/reports/export', [ReportsManagementController::class, 'export'])->name('reports.export');
    Route::get('/reports/statistics', [ReportsManagementController::class, 'statistics'])->name('reports.statistics');

    // Creator Notification Management
    Route::get('/reports/notifications/create', [ReportsManagementController::class, 'createCreatorNotification'])->name('reports.create-notification');
    Route::post('/reports/notifications/send', [ReportsManagementController::class, 'sendCreatorNotification'])->name('reports.send-creator-notification');

    // Legal Pages Management
    Route::get('/legal', [AdminLegalController::class, 'index'])->name('legal.index');
    Route::get('/legal/{slug}/edit', [AdminLegalController::class, 'edit'])->name('legal.edit');
    Route::put('/legal/{slug}', [AdminLegalController::class, 'update'])->name('legal.update');
    Route::get('/legal/{slug}/preview', [AdminLegalController::class, 'preview'])->name('legal.preview');

    // Advertisement Management
    Route::get('/advertisements', [AdminController::class, 'advertisements'])->name('advertisements');
    Route::get('/advertisements/create', [AdminController::class, 'createAdvertisement'])->name('advertisements.create');
    Route::post('/advertisements', [AdminController::class, 'storeAdvertisement'])->name('advertisements.store');
    Route::get('/advertisements/{advertisement}/edit', [AdminController::class, 'editAdvertisement'])->name('advertisements.edit');
    Route::put('/advertisements/{advertisement}', [AdminController::class, 'updateAdvertisement'])->name('advertisements.update');
    Route::delete('/advertisements/{advertisement}', [AdminController::class, 'deleteAdvertisement'])->name('advertisements.delete');
    Route::post('/advertisements/{advertisement}/toggle-status', [AdminController::class, 'toggleAdvertisementStatus'])->name('advertisements.toggle-status');
    Route::get('/advertisements/{advertisement}/stats', [AdminController::class, 'advertisementStats'])->name('advertisements.stats');

    // Advertisement Settings
    Route::get('/advertisements/settings', [AdminController::class, 'advertisementSettings'])->name('advertisements.settings');
    Route::post('/advertisements/settings', [AdminController::class, 'updateAdvertisementSettings'])->name('advertisements.settings.update');
});

// Public API Routes
Route::prefix('api')->group(function () {
    Route::get('/reels/all', [VideoController::class, 'getAllReels'])->name('api.reels.all');

    // Video Streaming API per precaricamento fluido
    Route::get('/videos/{videoId}/stream', [VideoStreamController::class, 'stream'])->name('api.videos.stream');
    Route::get('/videos/{videoId}/info', [VideoStreamController::class, 'info'])->name('api.videos.info');

    // Video Ads API
    Route::get('/video-ads/{videoId}', [VideoAdsController::class, 'getVideoAds'])->name('api.video-ads');
    Route::post('/ad-track', [VideoAdsController::class, 'trackAdInteraction'])->name('api.ad-track');

    // Test endpoints for ads
    Route::get('/test/vast-ads', [VideoAdsController::class, 'testVastAds'])->name('api.test.vast-ads');
    Route::get('/test/vmap-ads', [VideoAdsController::class, 'testVmapAds'])->name('api.test.vmap-ads');
    Route::get('/test/google-adsense', [VideoAdsController::class, 'testGoogleAdSense'])->name('api.test.google-adsense');

    // Ad Analytics API
    Route::post('/ad-analytics', [VideoAdsController::class, 'trackAdAnalytics'])->name('api.ad-analytics');
    Route::get('/ad-statistics/{videoId}', [VideoAdsController::class, 'getAdStatistics'])->name('api.ad-statistics');
});

// Test route for ads
Route::get('/test-ads', function () {
    return view('test-ads');
})->name('test-ads');

// Test route for miniplayer
Route::get('/test-miniplayer', [App\Http\Controllers\TestMiniPlayerController::class, 'index'])->name('test-miniplayer');

// Ping route for connection check
Route::get('/ping', function () {
    return response('ok');
})->name('ping');

// Protected API Routes
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Channel Search API
    Route::get('/search', [UserController::class, 'channelSearch'])->name('api.search');

    // Profile Data API
    Route::get('/user/profile-data', [UserController::class, 'getProfileData'])->name('api.user.profile-data');

    // Advertisement Click Tracking
    Route::post('/advertisement/{advertisement}/click', [AdvertisementController::class, 'trackClick'])->name('api.advertisement.click');

    // Advertisement Statistics (per admin)
    Route::get('/advertisement/statistics', [AdvertisementController::class, 'getStatistics'])->name('api.advertisement.statistics');

    // Video Report API
    Route::post('/video-report', [VideoReportController::class, 'report'])->name('api.video-report');
    Route::post('/toggle-ambient-mode', [VideoReportController::class, 'toggleAmbientMode'])->name('api.toggle-ambient-mode');
    Route::get('/user-preferences', [VideoReportController::class, 'getUserPreferences'])->name('api.user-preferences');
    Route::get('/video-stats/{video}', [VideoReportController::class, 'getVideoStats'])->name('api.video-stats');

    // Reel Navigation API
    Route::get('/video/{video}/adjacent-reel', [VideoController::class, 'getAdjacentReel'])->name('api.video.adjacent-reel');

    // Watch Later API
    Route::post('/watch-later/toggle', [\App\Http\Controllers\Api\WatchLaterController::class, 'toggle'])->name('api.watch-later.toggle');
    Route::get('/watch-later/status', [\App\Http\Controllers\Api\WatchLaterController::class, 'status'])->name('api.watch-later.status');
    Route::get('/watch-later/check', [\App\Http\Controllers\Api\WatchLaterController::class, 'check'])->name('api.watch-later.check');

    // Reports API (per segnalare video/reel)
    Route::post('/reports', [\App\Http\Controllers\ReportsController::class, 'apiStore'])->name('api.reports.store');
    Route::get('/api/reports/reasons', [\App\Http\Controllers\ReportsController::class, 'apiGetReasons'])->name('api.reports.reasons');

    // User Management API
    Route::post('/users/block', [\App\Http\Controllers\UserController::class, 'blockUser'])->name('api.users.block');
    Route::post('/users/unblock', [\App\Http\Controllers\UserController::class, 'unblockUser'])->name('api.users.unblock');
    Route::get('/users/blocked', [\App\Http\Controllers\UserController::class, 'getBlockedUsers'])->name('api.users.blocked');
});

// Error 404 page
Route::fallback(function () {
    return view('errors.404');
});
