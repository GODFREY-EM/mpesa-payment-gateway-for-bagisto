<?php

namespace Webkul\Mpesa\Payment;

use Webkul\Payment\Payment\Payment;
use Illuminate\Support\Facades\Storage;
use Webkul\Checkout\Facades\Cart;

class Mpesa extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'mpesa';

    /**
     * Get redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('mpesa.process');
    }

    /**
     * Get payment method image
     *
     * @return string
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        if ($url) {
            return Storage::url($url);
        }

        return asset('vendor/mpesa/images/mpesa-logo.svg');
    }

    /**
     * Returns payment method additional information
     *
     * @return array
     */
    public function getAdditionalDetails()
    {
        if (! $cart = Cart::getCart()) {
            return [];
        }

        return [
            'title' => 'M-Pesa Payment',
            'description' => 'Pay using M-Pesa mobile money',
            'cart' => Cart::getCart(),
            'billingAddress' => Cart::getCart()->billing_address,
            'html' => view('mpesa::mpesa-form')->render(),
        ];
    }

    /**
     * Returns payment method view
     *
     * @return string
     */
    public function getPaymentView()
    {
        return 'shop::checkout.onepage.payment';
    }

    /**
     * Returns payment method title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getConfigData('title') ?: 'M-Pesa';
    }

    /**
     * Returns payment method description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getConfigData('description') ?: 'Pay with M-Pesa mobile money';
    }

    /**
     * Returns true if payment method is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getConfigData('active');
    }

    /**
     * Returns payment method sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getConfigData('sort');
    }
}