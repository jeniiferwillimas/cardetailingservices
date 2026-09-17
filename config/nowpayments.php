<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NOWPayments API
    |--------------------------------------------------------------------------
    |
    | api_key is required to actually create invoices — leave it unset to
    | keep the site running with checkout returning a clear "not configured"
    | error instead of crashing. Banxa (card/fiat on-ramp) is enabled on the
    | NOWPayments merchant dashboard, not via an API parameter here — once
    | enabled, it just appears as a payment option on the hosted invoice page.
    |
    */

    'api_key' => env('NOWPAYMENTS_API_KEY'),

    'ipn_secret' => env('NOWPAYMENTS_IPN_SECRET'),

    'base_url' => env('NOWPAYMENTS_BASE_URL', 'https://api.nowpayments.io/v1'),

    'price_currency' => env('NOWPAYMENTS_PRICE_CURRENCY', 'usd'),

    'success_url' => env('NOWPAYMENTS_SUCCESS_URL', env('FRONTEND_URL', 'http://localhost:3000').'/booking/success'),

    'cancel_url' => env('NOWPAYMENTS_CANCEL_URL', env('FRONTEND_URL', 'http://localhost:3000').'/booking/cancelled'),

    'ipn_callback_url' => env('NOWPAYMENTS_IPN_CALLBACK_URL', env('APP_URL').'/api/payments/ipn'),
];
