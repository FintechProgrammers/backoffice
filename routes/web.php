<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ServiceController;
use App\Http\Controllers\User\SupportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('/', 'index')->name('dashboard');
    });

    Route::controller(SupportController::class)->prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/filter', 'tickets')->name('filter');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/show/{ticket}', 'show')->name('show');
        Route::get('/replies/{ticket}', 'getReplies')->name('replies');
        Route::post('/reply/{ticket}', 'replyTicket')->name('reply');
        Route::post('/delete/{ticket}', 'destroy')->name('delete');
    });

    Route::controller(ServiceController::class)->prefix('service')->name('service.')->group(function () {
        Route::get('', 'index')->name('index');
    });

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::post('update-profile-image', 'updateProfile')->name('update.image');
        Route::post('update-password', 'updatePassword')->name('update.password');
        Route::delete('/profile', 'destroy')->name('destroy');
    });
});


require __DIR__ . '/auth.php';
