<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VideoAdsController;
use App\Http\Controllers\Api\WatchLaterController;
use App\Http\Controllers\Api\PlaylistController;

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
    // Watch Later API
    Route::get('/watch-later', [WatchLaterController::class, 'index']);
    Route::post('/watch-later/{video}', [WatchLaterController::class, 'store']);
    Route::delete('/watch-later/{video}', [WatchLaterController::class, 'destroy']);
    
    // Playlist API
    Route::get('/playlist/shuffle', [PlaylistController::class, 'shuffle']);

    // Video Analytics API
    Route::post('/video-analytics', function () {
        return response()->json(['success' => true]);
    });

    // Ad Analytics API
    Route::post('/ad-analytics', function () {
        return response()->json(['success' => true]);
    });
});