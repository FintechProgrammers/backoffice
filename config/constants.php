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
        'ipn_key'     => env('NOWPAYMENT_IPN_KEY'),
        'email'   => env('NOWPAYMENT_EMAIL'),
        'password' => env('NOWPAYMENT_PASSWORD'),
        'ipn_base_url' => env('NOWPAYMENT_IPN_BASE_URL')
    ],
    'masspay' => [
        'base_url' => env('MASSPAY_BASE_URL'),
        'api_key' => env('MASSPAY_API_KEY'),
    ],
    'nexio' => [
        'base_url' => env('NEXIO_BASE_URL'),
        'username' => env('NEXIO_USERNAME'),
        'password' => env('NEXIO_PASSWORD'),
        'merchant_id' => env('NEXIO_MERCHANT_ID'),
        'payout_account_id' => env('NEXIO_PAYOUT_ACCOUNT_ID')
    ]
];
