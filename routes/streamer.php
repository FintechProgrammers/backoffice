<?php

use App\Http\Controllers\Api\Streamer\AcademyController;
use App\Http\Controllers\Api\Streamer\AcademyDocumentController;
use App\Http\Controllers\Api\Streamer\AcademyModuleController;
use App\Http\Controllers\Api\Streamer\AcademyVideoController;
use App\Http\Controllers\Api\Streamer\AuthenticateController;
use App\Http\Controllers\Api\Streamer\CategoryController;
use App\Http\Controllers\Api\Streamer\MessageController;
use App\Http\Controllers\Api\Streamer\ScheduleController;
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

    Route::controller(MessageController::class)->prefix('inbox')->group(function () {
        Route::get('messages', 'getMessages')->name('messages');
        Route::post('messages', 'sendMessage')->name('messages.send');
    });

    Route::controller(AcademyController::class)->prefix('academy')->name('academy.')->group((function () {
        Route::get('/', 'index');
        Route::get('/details/{academy}', 'details')->name('details');
        Route::get('/all', 'all')->name('all');
        Route::post('store', 'store')->name('store');
        Route::get('show/{academy}', 'show')->name('show');
        Route::put('update/{academy}', 'update')->name('update');
        Route::delete('delete/{academy}', 'delete')->name('delete');
    }));

    Route::controller(AcademyModuleController::class)->prefix('academy/modules')->name('academy.modules.')->group((function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{category}', 'categoryModule')->name('categoryModule'); // This endpoint list all module under a category
        Route::post('store', 'store')->name('store');
        Route::get('details/{module}', 'details')->name('details');
        Route::get('show/{module}', 'show')->name('show'); // This endpoint list all video under a module
        Route::put('update/{module}', 'update')->name('update');
        Route::put('make-video-favourite/{module}', 'makeFavourite')->name('makeFavourite');
        Route::post('sort-videos', 'sortVideos');
        Route::delete('delete/{module}', 'delete')->name('delete');
    }));

    Route::controller(AcademyVideoController::class)->prefix('academy/videos')->name('academy.videos.')->group((function () {
        Route::get('/{video}', 'index')->name('index');
        Route::post('validate', 'validateVideoFile')->name('validate');
        Route::post('store', 'store')->name('store');
        Route::post('sort', 'updateOrder')->name('sort');
        Route::put('update/{video}', 'update')->name('update');
        Route::delete('delete/{video}', 'delete')->name('delete');
    }));

    Route::controller(AcademyDocumentController::class)->prefix('academy/document')->name('academy.document.')->group(function () {
        Route::get('/{module}', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
    });

    Route::controller(ScheduleController::class)->prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', 'index');
        Route::get('/all', 'schedules')->name('all');
        Route::get('/videos/{schedule}', 'getVideos')->name('videos');
        Route::post('/videos/favourite/{video}', 'makeFavourite')->name('videos.favourite');
        Route::post('/sort/videos', 'sortVideos')->name('videos.sort');
        Route::delete('/videos/delete/{video}', 'deleteVideo')->name('videos.delete');
        Route::post('store', 'store')->name('store');
        Route::get('show/{schedule}', 'show')->name('show');
        Route::post('upload', 'uploadVideo')->name('upload');
        Route::put('update/{schedule}', 'update')->name('update');
        Route::post('live/start/{schedule}', 'startLive')->name('live.start');
        Route::post('live/stop/{schedule}', 'stopLive')->name('live.stop');
        Route::get('live/views/{schedule}', 'stopLive')->name('live.views');
        Route::get('live/message', 'messages')->name('live.message');
        Route::post('live/message', 'sendMessage')->name('live.message.send');
        Route::get('live/count/{schedule}', 'getCounts')->name('live.count');
        Route::delete('delete/{schedule}', 'destroy')->name('delete');
    });
});
