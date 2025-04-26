<?php

namespace Webkul\AzamPay\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Checkout\Facades\Cart;
use Webkul\Payment\Payment\Payment;

class AzamPay extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'azampay';

    public function getRedirectUrl(): string
    {
        return route('azampay.process');
    }

    /**
     * Returns payment method image.
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/money-transfer.png', 'shop');
    }
}