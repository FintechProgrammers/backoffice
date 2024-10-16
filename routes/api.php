<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/subscriptions', [SubscriptionController::class, 'index']);

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('', 'index');
        Route::post('/update-push-token', 'updateToken');
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
