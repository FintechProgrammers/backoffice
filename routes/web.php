<?php

use App\Http\Controllers\NowpaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\User\AcademyController;
use App\Http\Controllers\User\AmbassedorController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\KycController;
use App\Http\Controllers\User\ProviderController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\SalesController;
use App\Http\Controllers\User\ServiceAdsCheckoutController;
use App\Http\Controllers\User\ServiceController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\SupportController;
use App\Http\Controllers\User\TeamController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('/week-clock', 'weekClock')->name('week.clock');
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

    Route::controller(ServiceController::class)->prefix('package')->name('package.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/{service}', 'show')->name('details');
        Route::post('/purchase/{service}', 'purchase')->name('purchase');
    });

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::post('update-profile-image', 'updateProfile')->name('update.image');
        Route::post('update-password', 'updatePassword')->name('update.password');
        Route::delete('/profile', 'destroy')->name('destroy');
    });

    Route::controller(AmbassedorController::class)->prefix('ambassedor')->name('ambassedor.')->group(function () {
        Route::get('payment/confirm', 'index')->name('payment.confirm');
        Route::post('payment/process', 'pay')->name('payment.process');
    });

    Route::controller(StripeController::class)->prefix('stripe')->name('stripe.')->group(function () {
        Route::get('abassador/payment/success', 'abassedorPaymentSuccess')->name('abassador.payment.success');
        Route::get('service/payment/success', 'subscriptionSuccess')->name('service.payment.Success');
    });

    Route::controller(SubscriptionController::class)->prefix('subscription')->name('subscription.')->group(function () {
        Route::get('', 'index')->name('index');
    });

    Route::controller(SalesController::class)->prefix('sales')->name('sales.')->group(function () {
        Route::get('', 'index')->name('index');
    });

    Route::controller(WithdrawalController::class)->prefix('withdrawal')->name('withdrawal.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/send-otp', 'sendOTP')->name('send.otp');
    });

    Route::controller(AcademyController::class)->prefix('academy')->name('academy.')->group(function () {
        Route::get('', 'index')->name('index');
    });

    Route::controller(ReportController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('bonuses', 'bonuses')->name('bonuses');
        Route::get('ranks', 'ranks')->name('ranks');
        Route::get('packages', 'packages')->name('packages');
    });

    Route::controller(TeamController::class)->prefix('team')->name('team.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('fileter');
        Route::get('/genealogy', 'genealogy')->name('genealogy');
        Route::get('/user/info/{user}', 'userInfo')->name('user.info');
        Route::get('/create/member', 'createCustomer')->name('create.customer');
    });

    Route::controller(ProviderController::class)->prefix('provider')->name('provider.')->group(function () {
        Route::get('payin/{serviceId?}', 'payinProvider')->name('payin');
        Route::get('payout', 'payoutProvider')->name('payout');
        Route::get('/card/initiate', 'getDefaultCardProvider')->name('card.initiate');
        Route::get('/crypto/initiate', 'getDefaultCryptoProvider')->name('crypto.initiate');
    });

    Route::controller(PayoutController::class)->prefix('payout')->name('payout.')->group(function () {
        Route::post('/crypto/{provider}', 'crypto')->name('crypto');
        Route::post('/bank-transfer/{provider}', 'bankTransfer')->name('bank.transfer');
    });

    Route::controller(KycController::class)->prefix('kyc')->name('kyc.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/load-data', 'loadContent')->name('load-data');
    });
});

Route::prefix('webhook')->name('webhook')->group(function () {
    Route::post('/stripe', [StripeController::class, 'webhook'])->name('stripe');
});

Route::prefix('ipn')->name('ipn.')->group(function () {
    Route::controller(NowpaymentController::class)->prefix('nowpayment')->name('nowpayment.')->group(function () {
        Route::post('service/payment', 'serviceIpn')->name('service');
        Route::post('ambassador/payment', 'ambassadorIpn')->name('ambassador');
        Route::post('payout', 'payoutIpn')->name('payout');
    });
});

Route::controller(ServiceAdsCheckoutController::class)->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('', 'index')->name('index');
    Route::post('/store', 'store')->name('store');
});

Route::get('providers', [ProviderController::class, 'providers'])->name('providers.active');

Route::controller(PaymentController::class)->prefix('payment')->name('payment.')->group(function () {
    Route::get('success', 'success')->name('success');
    Route::get('cancel', 'cancel')->name('cancel');
});

Route::get('text-con', function () {
    $service = new \App\Services\CommissionService();

    $sale = \App\Models\Sale::where('id', 3)->first();

    $service->distributeCommissions($sale);
});


require __DIR__ . '/auth.php';