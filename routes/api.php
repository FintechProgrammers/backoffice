<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SignalController as ApiSignalController;
use App\Http\Controllers\Api\StreamersController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/subscriptions', [SubscriptionController::class, 'index']);

    Route::get('banner', [BannerController::class, 'index']);

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('', 'index');
        Route::post('/update-push-token', 'updateToken');
    });

    Route::controller(PlanController::class)->prefix('products')->group(function () {
        Route::get('', 'index');
        Route::get('/show/{product}', 'show');
    });

    Route::controller(StreamersController::class)->prefix('streamers')->group(function () {
        Route::get('/{service}', 'index');
        Route::get('/show/{streamer}', 'details');
    });

    Route::controller(ApiSignalController::class)->prefix('signals')->group(function () {
        Route::get('', 'index');
        Route::get('show/{signal}', 'show');
    });

    Route::controller(ChatController::class)->prefix('chat')->group(function () {
        Route::get('messages/{streamer}', 'messages');
        Route::post('send/{streamer}', 'sendMessage');
    });

    Route::controller(ScheduleController::class)->prefix('schedules')->group(function () {
        Route::get('', 'index');
        Route::get('/{schedule}', 'show');
        Route::get('/join-live/{schedule}', 'setViewers');
        Route::post('/leave-live/{schedule}', 'leaveStream');
        Route::get('{educator}/educator-schedules', 'educatorSchedules');
        Route::get('/live/educators', 'educatorsOnLive');
        Route::get('/live/viewers/{schedule}', 'getViewers');
    });

    Route::controller(LiveChatController::class)->prefix('live')->group(function () {
        Route::get('messages/{educator}', 'messages');
        Route::post('send/{educator}', 'sendMessage');
    });
});

Route::prefix('digitalservices')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'index');
    });
});

Route::get('update-user', function (Request $request) {
    $oldUserId = $request->old_user_id;
    $newUserId = $request->new_user_id;

    // dd(generateUniqueId());
    $user = \App\Models\User::find($oldUserId);

    if (!$user) {
        return response()->json(['message' => 'Could not find user']);
    }

    // Update the current user record
    $user->update([
        'id' => $newUserId,
        'migrated' => true
    ]);

    // Update parent references
    \App\Models\User::where('parent_id', $oldUserId)->update(['parent_id' => $newUserId]);

    updateReferences($oldUserId, $newUserId);

    return response()->json(['message' => 'Could not find user']);
});
