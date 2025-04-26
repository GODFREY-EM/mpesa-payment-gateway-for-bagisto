<?php

return [
    [
        'key' => 'sales',
        'name' => 'Sales',
        'sort' => 5,
        'info' => 'Sales configuration',
        'fields' => [
            [
                'key' => 'sales.payment_methods',
                'name' => 'Payment Methods',
                'sort' => 5,
                'info' => 'Payment method configurations',
                'fields' => [
                    [
                        'key'    => 'sales.payment_methods.mpesa',
                        'name'   => 'M-Pesa',
                        'sort'   => 8,
                        'info'   => 'M-Pesa payment method configuration',
                        'fields' => [
                            [
                                'name'          => 'title',
                                'title'         => 'Title',
                                'type'          => 'text',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => true,
                                'info'          => 'Title of the payment method',
                            ], [
                                'name'          => 'description',
                                'title'         => 'Description',
                                'type'          => 'textarea',
                                'channel_based' => false,
                                'locale_based'  => true,
                                'info'          => 'Description of the payment method',
                            ], [
                                'name'          => 'BusinessShortCode',
                                'title'         => 'Business Shortcode',
                                'type'          => 'text',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => false,
                                'info'          => 'M-Pesa business shortcode',
                            ], [
                                'name'          => 'consumer_key',
                                'title'         => 'Consumer Key',
                                'type'          => 'text',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => false,
                                'info'          => 'M-Pesa API consumer key',
                            ], [
                                'name'          => 'consumer_secret',
                                'title'         => 'Consumer Secret',
                                'type'          => 'text',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => false,
                                'info'          => 'M-Pesa API consumer secret',
                            ], [
                                'name'          => 'passkey',
                                'title'         => 'Passkey',
                                'type'          => 'text',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => false,
                                'info'          => 'M-Pesa API passkey',
                            ], [
                                'name'          => 'InitiatorName',
                                'title'         => 'Initiator Name',
                                'type'          => 'text',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => false,
                                'info'          => 'M-Pesa initiator name',
                            ], [
                                'name'          => 'initiator_password',
                                'title'         => 'Initiator Password',
                                'type'          => 'password',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => false,
                                'info'          => 'M-Pesa initiator password',
                            ], [
                                'name'          => 'image',
                                'title'         => 'Logo',
                                'type'          => 'image',
                                'channel_based' => false,
                                'locale_based'  => false,
                                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp',
                                'info'          => 'M-Pesa payment method logo',
                            ], [
                                'name'    => 'sandbox',
                                'title'   => 'Environment',
                                'type'    => 'select',
                                'options' => [
                                    [
                                        'title' => 'Sandbox',
                                        'value' => true,
                                    ], [
                                        'title' => 'Live',
                                        'value' => false,
                                    ],
                                ],
                                'validation'    => 'required',
                                'info'          => 'Select environment (Sandbox or Live)',
                            ], [
                                'name'          => 'active',
                                'title'         => 'Status',
                                'type'          => 'boolean',
                                'validation'    => 'required',
                                'channel_based' => false,
                                'locale_based'  => true,
                                'info'          => 'Enable or disable the payment method',
                            ],
                        ]
                    ]
                ]
            ]
        ]
    ]
];