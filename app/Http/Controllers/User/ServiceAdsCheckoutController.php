<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Invoice;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
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

        return view('user.checkout.index', $data);
    }

    function store(CheckoutRequest $request)
    {

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // check package
            $package = Service::whereUuid($validated['package_id'])->first();

            // Get provider
            $provider = Provider::whereUuid($validated['payment_provider'])->first();

            if (auth()->check()) {
                $user = auth()->user();
            } else {
                // get referral
                $referral = User::whereUuid($validated['referral_id'])->first();

                // create user account
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'parent_id'  => $referral->id
                ]);

                $validated['referral'] = $referral;
            }

            // create invoice
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'service_id' => $package->id,
                'order_id'   => generateReference(),
                'is_paid' => false
            ]);

            $validated['invoice'] = $invoice;
            $validated['user'] = $user;
            $validated['provider'] = $provider;
            $validated['package'] = $package;

            if ($provider->short_name == 'nowpayment') {
                $nowpaymentController = new \App\Http\Controllers\NowpaymentController();

                $route = $nowpaymentController->payment($validated);
            } else if ($provider->short_name == 'strip') {
                $stripController = new \App\Http\Controllers\StripeController();

                $route = $stripController->payment($validated);
            } else {
                return $this->sendError("Unable to complete your request at the moment", [], 400);
            }

            DB::commit();

            return $this->sendResponse(['route' => $route]);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError(serviceDownMessage(), [], 500);
        }
    }
}
