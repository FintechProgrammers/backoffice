<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    function create(Request $request)
    {

        $provider = Provider::where('is_active', true)->where('can_payin', true)->where('is_crypto', false)->first();

        if ($provider->short_name == 'strip') {
            $stripController = new \App\Http\Controllers\StripeController();

            return $stripController->createPaymentMethod($request);
        }

        return $this->sendError(serviceDownMessage(), [], 500);
    }

    function makeDefault(PaymentMethod $paymentMethod)
    {
        // Begin a database transaction
        DB::transaction(function () use ($paymentMethod) {
            // Get the user associated with this payment method
            $user = $paymentMethod->user;

            // Set all other payment methods for this user to not be default
            PaymentMethod::where('user_id', $user->id)->update(['is_default' => false]);

            // Set the specified payment method to be the default
            $paymentMethod->is_default = true;

            $paymentMethod->save();
        });

        return $this->sendResponse([], "Default payment method updated successfully.");
    }

    function stripeSuccess(Request $request)
    {
        return redirect()->route('profile.edit')->with('success', 'Payment Method Added Successfully.');
    }
}