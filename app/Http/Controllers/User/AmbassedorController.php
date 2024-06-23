<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class AmbassedorController extends Controller
{
    function index(Request $request)
    {
        $amount = !empty(systemSettings()->ambassador_fee) ? systemSettings()->ambassador_fee : 0;

        $data['title'] = 'Ambassedor Account Setup';
        $data['content'] = "To set up an ambassador account, please note that a payment of {$amount} USD is required.";

        return view('partials._confirm_paymemt', $data);
    }

    function pay(Request $request, StripeService $stripeService)
    {
        $user = $request->user();

        $data = [
            'currency' => 'usd',
            'amount' => systemSettings()->ambassador_fee * 100,
            'product_data' => [
                'name' => "Ambassedor Account Setup"
            ],
            'customer_email' => $user->email,
            'metadata' => ['user_id' => $user->id],
            'success_url' => route('stripe.abassador.payment.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel')
        ];

        $response = $stripeService->processCheckout($data);

        return $this->sendResponse(['route' => $response->url], "Success");
    }
}
