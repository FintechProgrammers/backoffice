<?php

use App\Http\Controllers\Api\Streamer\AuthenticateController;
use App\Http\Controllers\Api\Streamer\CategoryController;
use App\Http\Controllers\Api\Streamer\SignalController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthenticateController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);

    Route::controller(SignalController::class)->prefix('signals')->group(function () {
        Route::get('', 'index');
        Route::post('/create', 'store');
        Route::post('/update/{id}', 'update');
        Route::post('update-market-status', 'updateMarketStatus');
        Route::post('update-status', 'updateStatus');
    });
});
