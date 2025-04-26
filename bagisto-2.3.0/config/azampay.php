<?php

return [
    [
        'key'    => 'sales.payment_methods.azampay',
        'name'   => 'AzamPay',
        'info'   => 'Secure online payments using AzamPay’s API integration for seamless checkout.',
        'sort'   => 5,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'Payment Method Title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
                'info'          => 'This title will appear on the checkout page under payment methods.',
            ],
            [
                'name'          => 'description',
                'title'         => 'Payment Method Description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
                'info'          => 'Brief description to show at checkout.',
            ],
            [
                'name'       => 'client_id',
                'title'      => 'Client ID',
                'type'       => 'text',
                'validation' => 'required',
                'info'       => 'Your AzamPay-issued Client ID used to authenticate API requests.',
            ],
            [
                'name'       => 'client_secret',
                'title'      => 'Client Secret',
                'type'       => 'text',
                'validation' => 'required',
                'info'       => 'Secret key paired with the client ID. Keep this safe and secure.',
            ],
            [
                'name'       => 'logo',
                'title'      => 'Logo',
                'type'       => 'image',
                'info'       => 'Upload the AzamPay logo (recommended size: 55px X 45px).',
            ],
            [
                'name'          => 'sandbox',
                'title'         => 'Use Sandbox Mode',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
                'info'          => 'Enable this for testing in sandbox environment.',
            ],
            [
                'name'          => 'active',
                'title'         => 'Enable AzamPay',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
                'info'          => 'Activate to allow customers to use AzamPay during checkout.',
            ],
            [
                'name'    => 'sort',
                'title'   => 'Sort Order',
                'type'    => 'select',
                'options' => [
                    [ 'title' => '1', 'value' => 1 ],
                    [ 'title' => '2', 'value' => 2 ],
                    [ 'title' => '3', 'value' => 3 ],
                    [ 'title' => '4', 'value' => 4 ],
                    [ 'title' => '5', 'value' => 5 ],
                    [ 'title' => '6', 'value' => 6 ],
                ],
                'info' => 'Lower value means higher up in the list of payment methods.',
            ]
        ]
    ]
];
