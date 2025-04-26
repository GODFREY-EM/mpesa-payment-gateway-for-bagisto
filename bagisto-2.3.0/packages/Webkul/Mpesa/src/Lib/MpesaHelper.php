<?php

namespace Webkul\Mpesa\Lib;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class MpesaHelper
{
    private $security_credential;
    private $env;
    private $shortcode;
    private $consumer_key;
    private $consumer_secret;
    private $passkey;
    private $certificate_path;
    private $initiator_name;

    public function __construct()
    {
        // Determine environment: if sandbox config exists and is truthy, use sandbox; otherwise live.
        $sandbox = $this->getConfig('sandbox');
        $this->env = ($sandbox !== null && $sandbox) ? 'sandbox' : 'live';

        // Retrieve required configuration values from admin config or .env
        // Try both formats of environment variables (with and without MPESA_ prefix)
        $this->shortcode = $this->getConfig('BusinessShortCode')
            ?: env('MPESA_BUSINESS_SHORTCODE')
            ?: env('BusinessShortCode');

        $this->consumer_key = $this->getConfig('consumer_key')
            ?: env('MPESA_CONSUMER_KEY')
            ?: env('consumer_key');

        $this->consumer_secret = $this->getConfig('consumer_secret')
            ?: env('MPESA_CONSUMER_SECRET')
            ?: env('consumer_secret');

        $this->passkey = $this->getConfig('passkey')
            ?: env('MPESA_PASSKEY')
            ?: env('PassKey');

        $this->initiator_name = $this->getConfig('InitiatorName')
            ?: env('MPESA_INITIATOR_NAME')
            ?: env('InitiatorName');

        // Log the configuration values (with sensitive data masked)
        Log::info('M-Pesa configuration loaded', [
            'environment' => $this->env,
            'shortcode' => $this->shortcode,
            'consumer_key' => $this->consumer_key ? substr($this->consumer_key, 0, 4) . '...' : null,
            'consumer_secret' => $this->consumer_secret ? substr($this->consumer_secret, 0, 4) . '...' : null,
            'passkey' => $this->passkey ? substr($this->passkey, 0, 4) . '...' : null,
            'initiator_name' => $this->initiator_name
        ]);

        if (!$this->shortcode) {
            throw new \Exception('Missing required configuration: BusinessShortCode');
        }
        if (!$this->consumer_key) {
            throw new \Exception('Missing required configuration: consumer_key');
        }
        if (!$this->consumer_secret) {
            throw new \Exception('Missing required configuration: consumer_secret');
        }
        if (!$this->passkey) {
            throw new \Exception('Missing required configuration: passkey / PassKey');
        }
        if (!$this->initiator_name) {
            throw new \Exception('Missing required configuration: InitiatorName');
        }

        // Set certificate path based on environment
        $this->certificate_path = __DIR__ . '/../certificates/' .
            ($this->env === 'sandbox' ? 'SandboxCertificate.cer' : 'ProductionCertificate.cer');

        try {
            $this->security_credential = $this->generateSecurityCredential();
        } catch (\Exception $e) {
            // If we can't generate the security credential, use a fallback
            Log::warning('Failed to generate security credential, using fallback: ' . $e->getMessage());
            $this->security_credential = env('MPESA_INITIATOR_PASSWORD') ?: env('initiator_password') ?: 'Safaricom123!!';
        }
    }

    /**
     * Retrieve a configuration value from various possible config locations.
     *
     * @param string $key The configuration key to retrieve
     * @return mixed The configuration value or null if not found
     */
    private function getConfig($key)
    {
        // First try the dedicated mpesa config file
        $value = Config::get("mpesa.{$key}");
        if (!empty($value)) {
            return $value;
        }

        // Then try Bagisto's configuration paths
        $paths = [
            "sales.payment_methods.mpesa.{$key}",
            "payment_methods.mpesa.{$key}",
            "mpesa.{$key}"
        ];

        // Try using Bagisto's core helper
        if (function_exists('core')) {
            foreach ($paths as $path) {
                $value = core()->getConfigData($path);
                if (!empty($value)) {
                    return $value;
                }
            }
        }

        // Then try using Laravel's Config facade with Bagisto paths
        foreach ($paths as $path) {
            $value = Config::get($path);
            if (!empty($value)) {
                return $value;
            }
        }

        return null;
    }

    private function generateSecurityCredential()
    {
        try {
            // For development/testing, we'll use a hardcoded credential
            // This is a workaround for certificate issues
            if ($this->env === 'sandbox') {
                // Return a dummy security credential for sandbox environment
                return 'Safaricom123!!';
            }

            // For production, we'll try to use the certificate
            // If you're in production, you should properly configure the certificate
            $initiator_password = $this->getConfig('InitiatorPassword')
                                ?: $this->getConfig('initiator_password')
                                ?: env('MPESA_INITIATOR_PASSWORD')
                                ?: env('initiator_password');

            if (!$initiator_password) {
                throw new \Exception('Missing required configuration: initiator_password');
            }

            // In production, we would encrypt the password with the certificate
            // But for now, we'll just return the password as is
            return $initiator_password;

            /* Uncomment this code when you have a valid certificate
            $cert = file_get_contents($this->certificate_path);
            if (!$cert) {
                throw new \Exception('Could not read certificate file');
            }

            $pubKey = openssl_pkey_get_public($cert);
            if (!$pubKey) {
                throw new \Exception('Invalid certificate');
            }

            openssl_public_encrypt($initiator_password, $encrypted, $pubKey, OPENSSL_PKCS1_PADDING);
            return base64_encode($encrypted);
            */
        } catch (\Exception $e) {
            Log::error('Failed to generate security credential: ' . $e->getMessage());
            // Return the initiator password as a fallback
            return env('MPESA_INITIATOR_PASSWORD') ?: env('initiator_password') ?: 'Safaricom123!!';
        }
    }

    /**
     * Get the base URL for the Daraja API based on the environment
     *
     * @return string The base URL for the Daraja API
     */
    private function getBaseUrl()
    {
        // Sandbox: https://sandbox.safaricom.co.ke
        // Production: https://api.safaricom.co.ke
       // Use these instead (as per Vodacom API docs)
        $this->baseUrl = $this->env === 'sandbox'
        ? 'https://openapi.m-pesa.com/sandbox'
         : 'https://openapi.m-pesa.com/openapi';


        Log::info('Using Daraja API base URL', ['url' => $baseUrl, 'environment' => $this->env]);

        return $baseUrl;
    }

    private function getAccessToken()
    {
        $cacheKey = 'mpesa_access_token';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Log the authentication details (with sensitive data masked)
            Log::info('Authenticating with Daraja API', [
                'consumer_key' => substr($this->consumer_key, 0, 4) . '...',
                'consumer_secret' => substr($this->consumer_secret, 0, 4) . '...',
                'environment' => $this->env,
                'endpoint' => $this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials'
            ]);

            // When in sandbox mode, disable SSL verification to bypass self-signed certificate issues.
            $httpClient = Http::withBasicAuth($this->consumer_key, $this->consumer_secret);
            if ($this->env === 'sandbox') {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = Http::withBasicAuth($this->consumer_key, $this->consumer_secret)
            ->post($this->getBaseUrl() . '/ipg/v1/vodacomTZN/getToken');
            
            // Log the full response for debugging
            Log::info('Access token raw response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                Log::error('Failed to generate access token', [
                    'response' => $response->json(),
                    'status_code' => $response->status(),
                    'error_message' => $response->json()['errorMessage'] ?? 'Unknown error'
                ]);

                throw new \Exception('Failed to generate access token: ' . ($response->json()['errorMessage'] ?? 'Unknown error'));
            }

            // Check if the response contains the expected data
            if ($response->successful() && $response->json('access_token')) {
                $token = $response->json('access_token');
                $expiresIn = $response->json('expires_in', 3600);

                Cache::put($cacheKey, $token, Carbon::now()->addSeconds($expiresIn - 60));
                return $token;
            }

            throw new \Exception('Failed to extract access token from response');
        } catch (\Exception $e) {
            Log::error('Exception getting access token: ' . $e->getMessage());
            throw $e;
        }
    }

    public function initiateSTKPush($phone, $amount, $reference, $description)
    {
        // Generate timestamp in the format YYYYMMDDHHmmss
        $timestamp = date('YmdHis');

        // Generate password by base64 encoding BusinessShortCode + Passkey + Timestamp
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        // Set callback URL - ensure this is a publicly accessible URL
        // First try to get from config, then fall back to the local URL
        $callbackUrl = $this->getConfig('callback_url') ?: env('MPESA_CALLBACK_URL') ?: url('/mpesa/callback');

        // Log the callback URL to ensure it's correct
        Log::info('Using callback URL', ['url' => $callbackUrl]);

        // Format amount to ensure it's an integer (Daraja API requires integer amounts)
        $amount = intval($amount);

        // Format phone number to ensure it's in the correct format (254XXXXXXXXX)
        // Remove any non-numeric characters
        if ($phone) {
            $phone = preg_replace('/[^0-9]/', '', $phone);
        } else {
            throw new \Exception('Phone number is required');
        }

        // Log the original phone number
        Log::info('Original phone number', ['phone' => $phone]);

        // Handle different phone number formats
        if (strlen($phone) > 9) {
            if (substr($phone, 0, 1) === '0') {
                // If it starts with 0, replace with 254
                $phone = '254' . substr($phone, 1);
            } else if (substr($phone, 0, 3) !== '254') {
                // If it doesn't start with 254, add it
                $phone = '254' . $phone;
            }
        } elseif (strlen($phone) === 9) {
            // If it's just 9 digits, add 254 prefix
            $phone = '254' . $phone;
        }

        // For sandbox testing, ensure the phone number is registered in the Safaricom sandbox
        // Safaricom sandbox typically uses test phone numbers like 254708374149
        if ($this->env === 'sandbox') {
            // Log a warning if the phone number might not be registered in the sandbox
            Log::warning('Make sure this phone number is registered in the Safaricom sandbox', ['phone' => $phone]);
        }

        // Log the formatted phone number
        Log::info('Formatted phone number', ['phone' => $phone]);

        // Create HTTP client with access token
        $httpClient = Http::withToken($this->getAccessToken());

        // Disable SSL verification in sandbox mode to handle self-signed certificates
        if ($this->env === 'sandbox') {
            $httpClient = $httpClient->withoutVerifying();
        }

        try {
            // Prepare the payload according to Daraja API documentation
            // https://developer.safaricom.co.ke/APIs/MpesaExpressSimulate
            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $amount,
                'PartyA'            => $phone,
                'PartyB'            => $this->shortcode,
                'PhoneNumber'       => $phone,
                'CallBackURL'       => $callbackUrl,
                'AccountReference'  => $reference,
                'TransactionDesc'   => $description
            ];

            // Log the complete request details for debugging
            Log::info('Initiating STK Push with payload', [
                'payload' => $payload,
                'endpoint' => $this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest',
                'environment' => $this->env,
                'shortcode' => $this->shortcode,
                'consumer_key' => substr($this->consumer_key, 0, 5) . '...',
                'consumer_secret' => substr($this->consumer_secret, 0, 5) . '...',
                'passkey' => substr($this->passkey, 0, 5) . '...'
            ]);

            $response = $httpClient->post($this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest', $payload);

            // Log the full response for debugging
            Log::info('STK push raw response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                Log::error('STK push failed', [
                    'response' => $response->json(),
                    'status_code' => $response->status(),
                    'error_message' => $response->json()['errorMessage'] ?? 'Unknown error'
                ]);

                throw new \Exception('Failed to initiate STK push: ' . ($response->json()['errorMessage'] ?? 'Unknown error'));
            }

            if ($response->successful()) {
                Log::info('STK Push successful', ['response' => $response->json()]);
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Exception during STK push: ' . $e->getMessage());
            throw $e;
        }
    }

    public function checkTransactionStatus($checkoutRequestId)
    {
        try {
            $timestamp = date('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $httpClient = Http::withToken($this->getAccessToken());
            if ($this->env === 'sandbox') {
                $httpClient = $httpClient->withoutVerifying();
            }

            // According to Daraja API documentation, these are the required parameters
            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId
            ];

            Log::info('Checking transaction status', ['payload' => $payload]);

            // Make sure we're using the correct endpoint
            $response = $httpClient->post($this->getBaseUrl() . '/mpesa/stkpushquery/v1/query', $payload);

            // Log the full response for debugging
            Log::info('Transaction status check raw response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            // Check if the response is successful
            if (!$response->successful()) {
                Log::error('Transaction status check failed', ['response' => $response->json()]);
                throw new \Exception('Failed to check transaction status: ' . ($response->json()['errorMessage'] ?? 'Unknown error'));
            }

            // If we got a successful response, log and return it
            if ($response->successful()) {
                Log::info('Transaction status check successful', ['response' => $response->json()]);
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Exception during transaction status check: ' . $e->getMessage());
            throw $e;
        }
    }
}
