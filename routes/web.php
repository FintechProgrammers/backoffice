<?php

use App\Http\Controllers\NexioController;
use App\Http\Controllers\NowpaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
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
use App\Models\CommissionTransaction;
use App\Models\Sale;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\NexioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/service', function () {
    return view('service');
})->name('service');

Route::get('/owntheFuture', function () {
    return view('owntheFuture');
})->name('owntheFuture');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::middleware(['auth'])->group(function () {
    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('/week-clock', 'weekClock')->name('week.clock');
        Route::get('/sales-data', 'getSalesData')->name('sales.data');
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
        Route::get('/filter', 'filter')->name('filter');
    });

    Route::controller(WithdrawalController::class)->prefix('wallet')->name('wallet.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store')->middleware('kyc.verified');
        Route::get('/send-otp', 'sendOTP')->name('send.otp');
    });

    Route::controller(AcademyController::class)->prefix('academy')->name('academy.')->group(function () {
        Route::get('', 'index')->name('index');
    });

    Route::controller(ReportController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('/commissions', 'commission')->name('commissions');
        Route::get('/commissions/filter', 'commissionFilter')->name('commissions.filter');
        Route::get('/commissions/total', 'calculateTotalCommission')->name('commissions.total');
        Route::get('bonuses', 'bonuses')->name('bonuses');
        Route::get('ranks', 'ranks')->name('ranks');
        Route::get('packages', 'packages')->name('packages');
    });

    Route::controller(TeamController::class)->group(function () {
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('/filter', 'filter')->name('filter');
            Route::get('/genealogy', 'genealogy')->name('genealogy');
            Route::get('/user/info/{user}', 'userInfo')->name('user.info');
            Route::get('/create/member', 'createCustomer')->name('create.customer');
        });

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', 'customers')->name('index');
            Route::get('/filter', 'customersFilter')->name('filter');
        });
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

    Route::controller(PaymentMethodController::class)->prefix('payment-method')->name('payment-method.')->group(function () {
        // Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/stripe-success', 'stripeSuccess')->name('stripe.success');
        Route::post('/mark-default/{paymentMethod}', 'makeDefault')->name('mark.default');
    });
});

Route::prefix('webhook')->name('webhook')->group(function () {
    Route::post('/stripe', [StripeController::class, 'webhook'])->name('stripe');
    Route::post('/nexio', [NexioController::class, 'webhook'])->name('nexio');
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

Route::get('queue-work', function () {
    return Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
})->name('queue.work');

Route::get('cron', function () {
    return Illuminate\Support\Facades\Artisan::call('schedule:run');
})->name('cron');

Route::get('test', function () {

    // $commission = new \App\Console\Commands\ReleaseCommissions();

    // $commission->handle();

    // $nexio = new \App\Services\NexioService();

    // $payoutPayload = [
    //     'amount' => 0.05,
    //     'narration' => 'External withdrawal',
    //     'reference' => generateReference(),
    //     'recipientId' => 137724
    // ];

    // $response = $nexio->payouts($payoutPayload);

    // dD($response);


    // if ($response['error'] === 427 && $response['message'] === "Duplicate email for nexio") {
    //     dD("hello");
    // }



    // dd($response);

    // ini_set('max_execution_time', '0');

    // $chunkSize = 100; // Adjust the chunk size as needed

    // \App\Models\User::chunk($chunkSize, function ($users) {
    //     logger($users->count());
    //     // Dispatch each chunk to the job queue
    //     \App\Jobs\MigrateUserJob::dispatch($users);
    // });

    // return response()->json(["success"]);

    $date = "2024-09-14";

    // CommissionTransaction::whereDate('updated_at', $date)->update([
    //     'is_converted' => false,
    //     'transaction_id' => null,
    // ]);

    // $transaction = Transaction::whereDate('created_at', $date)->get();

    // foreach ($transaction as $transaction) {
    //     // get user wallet
    //     $wallet = Wallet::where('user_id', $transaction->user_id)->first();

    //     $newBalance = $wallet->balance - $transaction->amount;

    //     $wallet->update([
    //         'balance' => $newBalance
    //     ]);

    //     $transaction->delete();
    // }

    dd("done");


    // dd($commission);
});

require __DIR__ . '/auth.php';
