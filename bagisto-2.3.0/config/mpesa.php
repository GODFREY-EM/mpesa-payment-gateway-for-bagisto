<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the environment to use for the M-Pesa API.
    | Set to true for sandbox/testing, false for production.
    |
    */
    'sandbox' => env('MPESA_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | M-Pesa Credentials
    |--------------------------------------------------------------------------
    |
    | These are the credentials required to connect to the M-Pesa API.
    | You can get these from the Safaricom Developer Portal.
    |
    */
    'BusinessShortCode' => env('MPESA_BUSINESS_SHORTCODE', ''),
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    'passkey' => env('MPESA_PASSKEY', ''),
    'InitiatorName' => env('MPESA_INITIATOR_NAME', ''),
    'InitiatorPassword' => env('MPESA_INITIATOR_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    |
    | These are the URLs that M-Pesa will use to send callbacks.
    | These should be publicly accessible URLs.
    |
    */
    'callback_url' => env('MPESA_CALLBACK_URL', ''),
    'timeout_url' => env('MPESA_TIMEOUT_URL', ''),
    'result_url' => env('MPESA_RESULT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Transaction Type
    |--------------------------------------------------------------------------
    |
    | This is the transaction type to use for the STK Push.
    | CustomerPayBillOnline is the most common type.
    |
    */
    'transaction_type' => env('MPESA_TRANSACTION_TYPE', 'CustomerPayBillOnline'),
];