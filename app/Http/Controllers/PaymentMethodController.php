<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

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

    function stripeSuccess(Request $request)
    {
        return redirect()->route('profile.edit')->with('success', 'Payment Method Added Successfully.');
    }
}