<?php

use App\Http\Controllers\Admin\AdministrativeUserController;
use App\Http\Controllers\Admin\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Admin\Auth\AdminResetPasswordController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CommisionController;
use App\Http\Controllers\Admin\CommisionPlanController;
use App\Http\Controllers\Admin\CommissionsController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CycleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\RankController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\ServiceManagement;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubscriptionsController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WithdrawalController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::middleware('admin.guest')->group(function () {
    Route::get('', [LoginController::class, 'index'])->name('login');
    Route::post('', [LoginController::class, 'login'])->name('login.post');

    Route::controller(AdminForgotPasswordController::class)->prefix('forgot-password')->name('forgot.password.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
    });

    Route::controller(AdminResetPasswordController::class)->prefix('reset-password')->name('reset.password.')->group(function () {
        Route::get('/{token}', 'index')->name('index');
        Route::post('', 'reset')->name('store');
    });
});

Route::middleware('admin.auth')->group(function () {
    Route::controller(DashboardController::class)->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
    });

    Route::controller(UserManagementController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::get('/filter-referrals/{user}', 'filterReferrals')->name('filter.referrals');
        Route::post('', 'store')->name('store');
        Route::get('/create', 'create')->name('create');
        Route::get('/import', 'importView')->name('importView');
        Route::post('/import/store', 'importStore')->name('import.store');
        Route::get('/plan-form/{user}', 'activatePlanForm')->name('plan.form');
        Route::post('/plan-create/{user}', 'activatePlan')->name('plan.create');
        Route::get('/show/{user}', 'show')->name('show');
        Route::post('/update/{user}', 'update')->name('update');
        Route::post('/suspend/{user}', 'suspend')->name('suspend');
        Route::post('/activate/{user}', 'activate')->name('activate');
        Route::post('/delete/{user}', 'destroy')->name('delete');
        Route::get('/ambassador-form/{user}', 'ambassadorForm')->name('ambassador.form');
        Route::post('/make-ambassador/{user}', 'makeAmbassador')->name('mark.ambassador');
        Route::get('/change-username/{user}', 'usernameForm')->name('change-username');
        Route::post('/change-username/{user}', 'changeUsername')->name('change-username.post');
        Route::get('/change-email/{user}', 'emailForm')->name('change-email');
        Route::post('/change-email/{user}', 'changeEmail')->name('change-email.post');
        Route::get('setupNexio/{user}', 'setupNexio')->name('setupNexio');
        Route::post('/save-nexio-id/{user}', 'nexioSetUp')->name('nexioSetUp.post');
        Route::get('membership-form/{subscription}', 'membershipDetails')->name('membership.form');
        Route::post('membership/{subscription}', 'updateMembership')->name('membership.update');
    });

    Route::controller(CommisionController::class)->prefix('commission/pay')->name('commission.pay.')->group(function () {
        Route::get('/', 'index')->name('create');
        Route::post('/store', 'store')->name('store');
    });

    Route::controller(AdministrativeUserController::class)->prefix('admins')->name('admins.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::post('', 'store')->name('store');
        Route::get('/create', 'create')->name('create');
        Route::get('/show/{admin}', 'show')->name('show');
        Route::get('/edit/{admin}', 'edit')->name('edit');
        Route::post('/update/{admin}', 'update')->name('update');
        Route::post('/suspend/{admin}', 'suspend')->name('suspend');
        Route::post('/activate/{admin}', 'activate')->name('activate');
        Route::post('/delete/{admin}', 'destroy')->name('delete');
    });

    Route::controller(ServiceManagement::class)->prefix('package')->name('package.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::get('/create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::get('/show/{service}', 'show')->name('show');
        Route::get('/edit/{service}', 'edit')->name('edit');
        Route::patch('/update/{service}', 'update')->name('update');
        Route::post('/publish/{service}', 'publish')->name('publish');
        Route::post('/draft/{service}', 'draft')->name('draft');
        Route::post('/delete/{service}', 'destroy')->name('delete');
    });

    Route::controller(ProductController::class)->prefix('product')->name('product.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::get('/create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::get('/show/{product}', 'show')->name('show');
        Route::get('/edit/{product}', 'edit')->name('edit');
        Route::patch('/update/{product}', 'update')->name('update');
        Route::post('/publish/{product}', 'publish')->name('publish');
        Route::post('/draft/{product}', 'draft')->name('draft');
        Route::post('/delete/{product}', 'destroy')->name('delete');
    });

    Route::controller(RoleController::class)->prefix('roles')->name('roles.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::get('/edit/{role},', 'edit')->name('edit');
        Route::post('/update/{role},', 'update')->name('update');
        Route::delete('/{role}', 'destroy')->name('delete');
    });

    Route::controller(SupportController::class)->prefix('support')->name('support.')->group(function () {

        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('/filter', 'tickets')->name('filter');
            Route::post('', 'store')->name('store');
            Route::get('/create', 'create')->name('create');
            Route::get('/show/{ticket}', 'show')->name('show');
            Route::get('/replies/{ticket}', 'getReplies')->name('replies');
            Route::post('/replies/{ticket}', 'replyTicket')->name('replies.store');
            Route::post('/reply/{ticket}', 'replyTicket')->name('reply');
            Route::post('/update/{ticket}', 'update')->name('update');
            Route::post('/close/{ticket}', 'closeTicket')->name('close');
            Route::post('/delete/{ticket}', 'deleteTicket')->name('delete');
        });

        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('', 'subjects')->name('index');
            Route::get('/filter', 'subjectsTable')->name('filter');
            Route::post('', 'storeSubject')->name('store');
            Route::get('/create', 'createSubject')->name('create');
            Route::get('/show/{subject}', 'editSubject')->name('edit');
            Route::post('/update/{subject}', 'updateSubject')->name('update');
            Route::post('/delete/{subject}', 'deleteSubject')->name('delete');
        });
    });

    Route::controller(RankController::class)->prefix('rank')->name('rank.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::post('', 'store')->name('store');
        Route::get('/create', 'create')->name('create');
        Route::get('/show/{rank}', 'show')->name('show');
        Route::get('/edit/{rank}', 'edit')->name('edit');
        Route::put('/update/{rank}', 'update')->name('update');
        Route::post('/delete/{rank}', 'destroy')->name('delete');
    });

    Route::controller(CommisionPlanController::class)->prefix('commission')->name('commission.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::post('', 'store')->name('store');
        Route::get('/create', 'create')->name('create');
        Route::get('/show/{commission}', 'show')->name('show');
        Route::get('/edit/{commission}', 'edit')->name('edit');
        Route::put('/update/{commission}', 'update')->name('update');
        Route::post('/delete/{commission}', 'destroy')->name('delete');
    });

    Route::controller(SalesController::class)->prefix('sales')->name('sales.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('filter', 'filter')->name('filter');
    });

    Route::controller(SubscriptionsController::class)->prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('filter', 'filter')->name('filter');
    });

    Route::controller(WithdrawalController::class)->prefix('transactions')->name('transactions.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('filter', 'filter')->name('filter');
        Route::get('/details/{transaction}', 'details')->name('details');
    });

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('update-profile-image', 'updateProfile')->name('update.image');
        Route::post('update-password', 'updatePassword')->name('update.password');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller(SettingsController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
    });

    Route::controller(BannerController::class)->prefix('banner')->name('banner.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{banner}', 'edit')->name('edit');
        Route::patch('/update/{banner}', 'update')->name('update');
        Route::post('/publish/{banner}', 'publish')->name('publish');
        Route::post('/unpublish/{banner}', 'unpublish')->name('unpublish');
        Route::post('/delete/{banner}', 'destroy')->name('delete');
    });

    Route::get('update-countries', [CountryController::class, 'updateCountriesTableWithFlags']);

    Route::controller(ProviderController::class)->prefix('provider')->name('provider.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('filter', 'filter')->name('filter');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{provider}', 'edit')->name('edit');
        Route::put('/update/{provider}', 'update')->name('update');
        Route::post('/enable/{provider}', 'enable')->name('enable');
        Route::post('/disable/{provider}', 'disable')->name('disable');
        Route::post('/default/{provider}', 'default')->name('default');
    });

    Route::controller(CycleController::class)->prefix('cycle')->name('cycle.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/start-cycle', 'runCycle')->name('start');
    });

    Route::controller(KycController::class)->prefix('kyc')->name('kyc.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'filter')->name('filter');
        Route::get('/{user}', 'show')->name('show');
        Route::post('/approve/{kyc}', 'approve')->name('approve');
        Route::post('/decline/{kyc}', 'decline')->name('decline');
    });

    Route::controller(ImpersonateController::class)->prefix('impersonate')->name('impersonate.')->group(function () {
        Route::get('/{user}', 'index')->name('index');
    });

    Route::controller(CommissionsController::class)->prefix('commissions')->name('commissions.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/filter', 'commissionFilter')->name('filter');
        Route::get('/filter/total', 'calculateTotalCommission')->name('filter.total');
        Route::get('/settle', 'payview')->name('settle');
        Route::post('/settle', 'store')->name('settle.store');
    });
});
