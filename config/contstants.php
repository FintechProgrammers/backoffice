<?php

return  [
    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
    'nowpayment' => [
        'base_url' => env('NOWPAYMENT_BASE_URL'),
        'api_key' => env('NOWPAYMENT_API_KEY'),
        'ipn'     => env('NOWPAYMENT_IPN_KEY'),
        'email'   => env('NOWPAYMENT_EMAIL'),
        'password' => env('NOWPAYMENT_PASSWORD'),
        'ipn_base_url' => env('NOWPAYMENT_IPN_BASE_URL')
    ],
    'masspay' => [
        'base_url' => env('MASSPAY_BASE_URL'),
        'api_key' => env('MASSPAY_API_KEY'),
    ]
];
