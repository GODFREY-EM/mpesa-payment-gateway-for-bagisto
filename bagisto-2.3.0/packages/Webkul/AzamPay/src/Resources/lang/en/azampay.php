<?php

return [
    'azampay' => [
        'info'        => 'AzamPay payment method: fast and secure payments.',
        'name'        => 'AzamPay',
        'payment'     => 'AzamPay Payment Gateway',
        'title'       => 'Pay with AzamPay',
        'description' => 'Secure and easy payments via AzamPay',

        'system' => [
            'title'               => 'Display Title',
            'description'         => 'Payment Description',
            'image'               => 'Logo',
            'status'              => 'Enable AzamPay',
            'client-id'           => 'Client ID',
            'client-id-info'      => 'Use "sb" for testing.',
            'client-secret'       => 'Client Secret',
            'client-secret-info'  => 'Add your AzamPay client secret key here',
            'accepted-currencies' => 'Accepted Currencies',
            'sandbox'             => 'Sandbox Mode',
            'sort_order'          => 'Sort Order',
        ],
    ],

    'resources' => [
        'title' => 'Pay with AzamPay',
    ],
];
