<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Wallet;
use App\Notifications\NewAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceAdsCheckoutController extends Controller
{
    function index(Request $request)
    {
        $data['package'] = Service::whereUuid($request->service)->first();
        $data['ambassador'] = User::whereUuid($request->amb)->first();
        $data['providers'] = Provider::where('is_active', true)->where('can_payin', true)->get();
        $data['countries'] = Country::get();

        return view('user.checkout.index', $data);
    }

    function store(CheckoutRequest $request)
    {

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // check package
            $package = Service::whereUuid($validated['package_id'])->first();

            if ($request->payment_provider === 'commission_wallet') {
                $shortName = "commission_wallet";
            } else {
                // Get provider
                $provider = Provider::whereUuid($validated['payment_provider'])->first();

                $shortName = $provider->short_name;

                $validated['provider'] = $provider;
            }

            if (!empty($validated['referral_id'])) {
                // get referral
                $referral = User::whereUuid($validated['referral_id'])->first();

                if (!$referral) {
                    sendToLog("parent uuid not found {$validated['referral_id']} on payment");
                    return $this->sendError(serviceDownMessage(), [], 404);
                }

                $password = \Illuminate\Support\Str::random(5);

                // create user account
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($password),
                    'parent_id'  => $referral->id
                ]);

                UserInfo::where('user_id', $user->id)->update([
                    'country_code' => $request->country,
                ]);

                $validated['referral'] = $referral;

                $validated['payer'] = $referral->id;

                $mailData = [
                    'username' => $request->username,
                    'name' => $request->first_name,
                    'password' => $password
                ];

                $user->notify(new NewAccountCreated($mailData));
            } else {
                $user = auth()->user();

                $validated['payer'] = $user->id;
            }

            // create invoice
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'service_id' => !empty($package->id) ? $package->id : null,
                'order_id'   => generateReference(),
                'is_paid' => false
            ]);

            $validated['invoice'] = $invoice;
            $validated['user'] = $user;
            $validated['package'] = $package;

            if ($shortName == 'nowpayment') {
                $nowpaymentController = new \App\Http\Controllers\NowpaymentController();

                $route = $nowpaymentController->payment($validated);
            } else if ($shortName == 'strip') {
                $stripController = new \App\Http\Controllers\StripeController();

                $route = $stripController->payment($validated);
            } else if ($shortName == 'commission_wallet') {
                $walletController = new \App\Http\Controllers\WalletController();

                return $walletController->payment($validated);
            } else {
                return $this->sendError(serviceDownMessage(), [], 400);
            }

            DB::commit();

            return $this->sendResponse(['route' => $route]);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError(serviceDownMessage(), [], 500);
        }
    }
}