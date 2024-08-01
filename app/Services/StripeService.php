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

    function createCustomer(array $data)
    {
        $response = $this->stripe->customers->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
        ]);

        return $response;
    }

    function createSession(array $data)
    {
        $response = $this->stripe->checkout->sessions->create([
            'mode'                  => 'setup',
            'currency'              => 'usd',
            'payment_method_types'  => ['card'],
            'customer'              => $data['customer_id'],
            // 'metadata'      => $data['metadata'],
            'success_url'           => $data['success_url'],
            'cancel_url'            => $data['cancel_url'],
        ]);

        return $response;
    }

    function  processCheckout($data)
    {
        $response = $this->stripe->checkout->sessions->create([
            'payment_intent_data' => ['setup_future_usage' => 'off_session'],
            // 'customer_creation' => 'always',
            'customer' => $data['customer_id'],
            'line_items' => [[
                'price_data' => [
                    'currency' => "usd",
                    'product_data' => $data['product_data'],
                    'unit_amount' => $data['amount'],
                ],
                'quantity' => 1,
            ]],
            'saved_payment_method_options' => ['payment_method_save' => 'enabled'],
            // 'customer_email' => $data['customer_email'],
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
            [
                'expand' => ['line_items'],
            ]
        );

        return $response;
    }

    function paymentIntent(array $data)
    {
        $response = $this->stripe->paymentIntents->create([
            'amount' => $data['amount'],
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
            'customer' => $data['customer'],
            'payment_method' => $data['payment_method'],
            'return_url' => $data['success_url'],
            'metadata' => $data['metadata'],
            'off_session' => true,
            'confirm' => true,
        ]);

        return $response;
    }

    function createPaymentMethod($data)
    {
        $response = $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => $data['card_number'],
                'exp_month' => $data['exp_month'],
                'exp_year' => $data['exp_year'],
                'cvc' => $data['cvc'],
            ],
        ]);

        return $response;
    }

    function attachePaymentMethod($data)
    {
        $response = $this->stripe->paymentMethods->attach(
            $data['payment_method'],
            ['customer' => $data['customer']]
        );

        return $response;
    }

    function retrievePaymentMethod($data)
    {
        $response = $this->stripe->customers->retrievePaymentMethod(
            $data['customer_id'],
            $data['payment_method'],
            []
        );

        return $response;
    }

    function createWebhook()
    {
        $response = $this->stripe->webhookEndpoints->create([
            'enabled_events' => ['charge.succeeded', 'charge.failed'],
            'url' => 'https://example.com/my/webhook/endpoint',
        ]);

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