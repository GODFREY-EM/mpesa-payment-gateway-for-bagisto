<?php

return [
    [
        'key'    => 'sales.payment_methods.mpesa',
        'name'   => 'Mpesa',
        'info'   => 'MPesa payment configuration',
        'sort'   => 5,
        'fields' => [
            // MPesa Title
            [
                'name'          => 'title',
                'title'         => 'MPesa Title',
                'type'          => 'text',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // MPesa Description
            [
                'name'          => 'description',
                'title'         => 'MPesa Description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
                'depend'        => 'active:1',
            ],

            // MPesa Logo
            [
                'name'          => 'image',
                'title'         => 'MPesa Logo',
                'info'          => 'Upload the logo for MPesa payment method.',
                'type'          => 'image',
                'channel_based' => false,
                'locale_based'  => true,
                'depend'        => 'active:1',
            ],

            // Paybill Number (Short Code)
            [
                'name'          => 'short_code',
                'title'         => 'Paybill Number',
                'type'          => 'text',
                'validation'    => 'required_if:active,1|numeric',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Consumer Key
            [
                'name'          => 'mpesa_consumer_key',
                'title'         => 'Consumer Key',
                'info'          => 'Enter the MPesa consumer key from your developer account.',
                'type'          => 'text',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Consumer Secret
            [
                'name'          => 'mpesa_consumer_secret',
                'title'         => 'Consumer Secret',
                'info'          => 'Enter the MPesa consumer secret from your developer account.',
                'type'          => 'password',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Initiator Name
            [
                'name'          => 'initiator_name',
                'title'         => 'Initiator Name',
                'type'          => 'text',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Initiator Password
            [
                'name'          => 'initiator_password',
                'title'         => 'Initiator Password',
                'type'          => 'password',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Passkey
            [
                'name'          => 'passkey',
                'title'         => 'Passkey',
                'type'          => 'password',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // HMAC Key
            [
                'name'          => 'hmac_key',
                'title'         => 'HMAC Key',
                'info'          => 'Used for callback signature validation.',
                'type'          => 'password',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Allowed IPs
            [
                'name'          => 'allowed_ips',
                'title'         => 'Allowed IPs',
                'info'          => 'Comma-separated list of allowed Vodacom/Safaricom IPs.',
                'type'          => 'text',
                'default'       => '196.11.240.227',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Accepted Currencies
            [
                'name'          => 'mpesa_currencies',
                'title'         => 'Accepted Currencies',
                'type'          => 'text',
                'info'          => 'Add currency codes comma separated, e.g., TZS, KES',
                'channel_based' => false,
                'locale_based'  => true,
                'depend'        => 'active:1',
            ],

            // Enable MPesa
            [
                'name'          => 'active',
                'title'         => 'Enable MPesa',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Sandbox Mode
            [
                'name'          => 'sandbox',
                'title'         => 'Sandbox Mode',
                'type'          => 'boolean',
                'info'          => 'Enable sandbox mode for testing transactions',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ],

            // Sort Order
            [
                'name'          => 'sort',
                'title'         => 'Sort Order',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];
