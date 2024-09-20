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

            $providerId = null;

            $userPayload = null;

            $user = null;

            if ($request->payment_provider === 'commission_wallet') {
                $shortName = "commission_wallet";
            } else {
                // Get provider
                $provider = Provider::whereUuid($validated['payment_provider'])->first();

                $shortName = $provider->short_name;

                $validated['provider'] = $provider;

                $providerId = $provider->id;
            }

            if (!empty($validated['referral_id'])) {
                // get referral
                $referral = User::whereUuid($validated['referral_id'])->first();

                if (!$referral) {
                    sendToLog("parent uuid not found {$validated['referral_id']} on payment");
                    return $this->sendError(serviceDownMessage(), [], 404);
                }

                // check if email already exists
                $checkEmail = User::where('email', $validated['email'])->first();

                if ($checkEmail) {
                    return $this->sendError("An account with this email already exists. Please log in to complete your payment. If you don't remember your credentials, check your email for login details or reset your password.", [], 400);
                }

                $validated['email'] = $request->email;

                // create user account
                $userPayload = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'parent_id'  => $referral->id,
                    'country' => $request->country
                ];
            } else {
                $user = auth()->user();

                $validated['email'] = $user->email;

                $validated['payer'] = $user->id;
            }

            // create invoice
            $invoice = Invoice::create([
                'user_id' => !empty($user->id) ? $user->id : null,
                'service_id' => !empty($package->id) ? $package->id : null,
                'provider_id' => $providerId,
                'order_id'   => generateReference(),
                'is_paid' => false,
                'user_payload' => !empty($userPayload) ? json_encode($userPayload) : null
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
