<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::get('/subscriptions', [SubscriptionController::class, 'index']);

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('', 'index');
        Route::post('/update-push-token', 'updateToken');
    });
});
