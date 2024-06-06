<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    public $stripe;

    function __construct()
    {
        $this->stripe = new StripeClient(config('contstants.stripe.secret'));
    }

    function  processCheckout($data)
    {
        $response = $this->stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => $data['currency'],
                    'product_data' => $data['product_data'],
                    'unit_amount' => $data['amount'],
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $data['customer_email'],
            'metadata' => $data['metadata'],
            'mode' => 'payment',
            'success_url' => $data['success_url'],
            'cancel_url' => $data['cancel_url'],
        ]);


        return $response;
    }

    function retrieveSession($sessionId)
    {
        $response = $this->stripe->checkout->sessions->retrieve(
            $sessionId,
            []
        );

        return $response;
    }

    function getWebhookEvent($data)
    {
        $event = \Stripe\Webhook::constructEvent(
            $data['payload'],
            $data['sig_header'],
            config('contstants.stripe.webhook_secret')
        );

        return $event;
    }
}
