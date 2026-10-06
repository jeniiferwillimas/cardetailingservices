<?php

return [
    'merchant_wallet' => env('ALCHEMY_PAY_MERCHANT_WALLET'),
    'alchemy_url' => env('ALCHEMY_PAY_RAMP_URL', 'https://ramp.alchemypay.org'),
    'moonpay_url' => env('MOONPAY_RAMP_URL', 'https://buy.moonpay.com'),
    'transak_url' => env('TRANSAK_RAMP_URL', 'https://transak.com/buy'),
    'min_withdrawal' => env('MIN_WITHDRAWAL_USD', 200),
];
