<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminRedirectIfAuthenticated;
use App\Http\Middleware\KycVerified;
use App\Http\Middleware\Verify2fa;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'admin.guest' => AdminRedirectIfAuthenticated::class,
            'admin.auth' => AdminAuthenticate::class,
            'kyc.verified' => KycVerified::class,
            '2faCheck' => Verify2fa::class,
            // '2fa' => Google2FA::class,
        ])->validateCsrfTokens(except: [
            'webhook/*',
            'ipn/*'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
