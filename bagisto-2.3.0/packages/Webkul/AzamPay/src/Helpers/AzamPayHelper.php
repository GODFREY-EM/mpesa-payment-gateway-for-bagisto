<?php

namespace Webkul\AzamPay\Helpers;

use Illuminate\Support\Facades\Http;

class AzamPayHelper
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $apiUrl;

    public function __construct()
    {
        $this->clientId = core()->getConfigData('sales.payment_methods.azampay.client_id');
        $this->clientSecret = core()->getConfigData('sales.payment_methods.azampay.client_secret');
        $this->apiUrl = 'https://sandbox.azampay.co.tz'; // change to live URL for production
    }

    public function createCheckoutSession(array $paymentData): string
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'amount'       => $paymentData['amount'],
            'currency'     => $paymentData['currency'],
            'externalId'   => $paymentData['order_id'],
            'provider'     => 'AzamPay',
            'redirectUrl'  => $paymentData['success_url'],
            'cancelUrl'    => $paymentData['cancel_url'],
            'language'     => 'en',
        ];

        $response = Http::withToken($accessToken)
            ->post($this->apiUrl . '/checkout/api/v1/session', $payload);

        if ($response->successful()) {
            return $response->json('checkoutUrl');
        }

        throw new \Exception('Failed to create AzamPay session: ' . $response->body());
    }

    protected function getAccessToken(): string
    {
        $response = Http::withHeaders([
            'X-API-KEY'      => $this->clientId,
            'X-API-SECRET'   => $this->clientSecret,
        ])->post($this->apiUrl . '/auth/v1/token');

        if ($response->successful()) {
            return $response->json('accessToken');
        }

        throw new \Exception('Failed to retrieve AzamPay access token: ' . $response->body());
    }
}
