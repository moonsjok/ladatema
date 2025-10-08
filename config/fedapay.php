<?php
return [
    'live_secret_key' => env('FEDAPAY_LIVE_API_SECRET_KEY'),
    'live_public_key' => env('FEDAPAY_LIVE_API_PUBLIC_KEY'),
    'test_secret_key' => env('FEDAPAY_SANDBOX_API_SECRET_KEY'),
    'test_public_key' => env('FEDAPAY_SANDBOX_API_PUBLIC_KEY'),


    'mode' => env('FEDAPAY_MODE', 'live'), // sandbox ou live
];
