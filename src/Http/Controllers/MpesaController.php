<?php

namespace Webkul\Mpesa\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Mpesa\Lib\MpesaHelper;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected $orderRepository;
    protected $invoiceRepository;
    protected $mpesa;

    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        MpesaHelper $mpesa
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->mpesa = $mpesa;
    }

    /**
     * Handles both rendering the payment form (GET) and processing the STK push (POST).
     */
    public function initiateSTK(Request $request)
    {
        if ($request->isMethod('get')) {
            $cart = Cart::getCart();

            if (!$cart) {
                return redirect()->route('shop.checkout.cart.index')
                    ->with('error', 'Cart is empty');
            }

            // For AJAX requests, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('mpesa::mpesa-form', [
                        'cart' => $cart,
                        'billingAddress' => $cart->billing_address
                    ])->render()
                ]);
            }

            // For regular requests, return the view
            return view('mpesa::mpesa-form', [
                'cart'           => $cart,
                'billingAddress' => $cart->billing_address
            ]);
        }

        // For POST request, process STK push initiation.
        $cart = Cart::getCart();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ]);
        }

        $phone = $request->input('mpesa_phone');

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number is required'
            ]);
        }

        // Format phone number: remove leading zero and prepend country code (254)
        $formattedPhone = preg_replace('/^0/', '254', $phone);

        // If phone doesn't start with 254, add it
        if (!preg_match('/^254/', $formattedPhone)) {
            $formattedPhone = '254' . ltrim($formattedPhone, '+');
        }

        if (!preg_match('/^254\d{9}$/', $formattedPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format. Please use format 07XXXXXXXX or 254XXXXXXXXX'
            ]);
        }

        try {
            $response = $this->mpesa->initiateSTKPush(
                $formattedPhone,
                $cart->grand_total,
                $cart->id,
                'Payment for Order #' . $cart->id
            );

            if (isset($response['ResponseCode']) && $response['ResponseCode'] === "0") {
                session()->put('mpesa_checkout_request_id', $response['CheckoutRequestID']);
                session()->put('mpesa_order_id', $cart->id);
                session()->put('mpesa_phone', $formattedPhone);

                // Check if we're in sandbox mode
                $isSandbox = config('mpesa.sandbox', false);

                $message = 'STK push sent successfully';

                // Add additional information for sandbox mode
                if ($isSandbox) {
                    $message .= '. IMPORTANT: In sandbox mode, you need to have a phone number registered in the Safaricom sandbox environment. If you don\'t receive a prompt, please contact Safaricom to register your phone number in the sandbox.';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect_url' => url('/mpesa/waiting/' . $cart->id),
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'sandbox_mode' => $isSandbox
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response['ResponseDescription'] ?? 'Failed to initiate payment'
            ]);
        } catch (\Exception $e) {
            Log::error('STK Push Exception: ' . $e->getMessage());

            // Extract the error message from the exception
            $errorMessage = $e->getMessage();

            // Check if it's a specific Safaricom API error
            if (strpos($errorMessage, 'Failed to initiate STK push:') !== false) {
                // Extract the specific error message from Safaricom
                $safaricomError = str_replace('Failed to initiate STK push: ', '', $errorMessage);

                return response()->json([
                    'success' => false,
                    'message' => 'M-Pesa Error: ' . $safaricomError,
                    'error_details' => $errorMessage
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again later.',
                'error_details' => $errorMessage
            ]);
        }
    }

    /**
     * Handles the callback from M-Pesa.
     */
    public function callback(Request $request)
    {
        Log::info('Mpesa callback received', ['data' => $request->all(), 'headers' => $request->headers->all()]);

        try {
            $callbackData = $request->all();

            // Log the raw request content for debugging
            Log::info('Raw callback content', ['content' => $request->getContent()]);

            if (!isset($callbackData['Body']['stkCallback'])) {
                Log::error('Invalid Mpesa callback data structure', ['data' => $callbackData]);
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback data']);
            }

            $stkCallback = $callbackData['Body']['stkCallback'];
            $resultCode = $stkCallback['ResultCode'];
            $checkoutRequestId = $stkCallback['CheckoutRequestID'];

            // Store the callback result in cache for the frontend to check
            $cacheKey = 'mpesa_callback_' . $checkoutRequestId;

            if ($resultCode == 0) {
                // Payment successful
                $items = $stkCallback['CallbackMetadata']['Item'];
                $amount = null;
                $mpesaReceiptNumber = null;
                $transactionDate = null;
                $phoneNumber = null;

                foreach ($items as $item) {
                    switch ($item['Name']) {
                        case 'Amount':
                            $amount = $item['Value'];
                            break;
                        case 'MpesaReceiptNumber':
                            $mpesaReceiptNumber = $item['Value'];
                            break;
                        case 'TransactionDate':
                            $transactionDate = $item['Value'];
                            break;
                        case 'PhoneNumber':
                            $phoneNumber = $item['Value'];
                            break;
                    }
                }

                // Store transaction details in cache
                \Cache::put($cacheKey, [
                    'success' => true,
                    'amount' => $amount,
                    'receipt' => $mpesaReceiptNumber,
                    'phone' => $phoneNumber,
                    'date' => $transactionDate
                ], now()->addHours(1));

                // Process the order if we have the cart ID in the metadata
                $cartId = null;

                // Try to extract cart ID from the AccountReference in the callback
                if (isset($stkCallback['AccountReference'])) {
                    $cartId = $stkCallback['AccountReference'];
                }

                if (!$cartId) {
                    // If not found in callback, try to get from session
                    $cartId = session()->get('mpesa_order_id');
                }

                if ($cartId) {
                    // Get the cart
                    $cart = Cart::getCart();

                    if ($cart && $cart->id == $cartId) {
                        // Create the order from cart data
                        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

                        // Update the order with payment details
                        $this->orderRepository->update([
                            'status' => 'processing',
                            'mpesa_receipt' => $mpesaReceiptNumber,
                            'mpesa_phone' => $phoneNumber,
                            'transaction_date' => $transactionDate
                        ], $order->id);

                        if ($order->canInvoice()) {
                            $this->invoiceRepository->create($this->prepareInvoiceData($order));
                        }

                        // Deactivate the current cart session
                        Cart::deActivateCart();

                        session()->flash('order', $order);
                    }
                }
            } else {
                // Payment failed
                \Cache::put($cacheKey, [
                    'success' => false,
                    'message' => $stkCallback['ResultDesc'] ?? 'Payment failed'
                ], now()->addHours(1));
            }

            // Always return success to M-Pesa to acknowledge receipt of callback
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        } catch (\Exception $e) {
            Log::error('Mpesa callback exception: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error processing callback']);
        }
    }

    /**
     * Prepares invoice data for the order.
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = ['order_id' => $order->id];
        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }
        Log::debug('Prepared invoice data', ['invoiceData' => $invoiceData]);
        return $invoiceData;
    }

    /**
     * Checks the status of the pending transaction.
     */
    public function checkStatus(Request $request, $orderId = null)
    {
        try {
            $checkoutRequestId = session()->get('mpesa_checkout_request_id');
            if (!$checkoutRequestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending transaction found'
                ]);
            }

            // First check if we have a cached callback result
            $cacheKey = 'mpesa_callback_' . $checkoutRequestId;
            $cachedResult = \Cache::get($cacheKey);

            if ($cachedResult) {
                if ($cachedResult['success']) {
                    // Payment was successful
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment completed successfully',
                        'redirect' => url('/checkout/success')
                    ]);
                } else {
                    // Payment failed
                    return response()->json([
                        'success' => false,
                        'message' => $cachedResult['message'] ?? 'Payment failed',
                        'redirect' => url('/checkout/onepage')
                    ]);
                }
            }

            // If no cached result, check with M-Pesa API
            $response = $this->mpesa->checkTransactionStatus($checkoutRequestId);

            Log::info('M-Pesa transaction status check response', ['response' => $response]);

            // Check if the response indicates success
            if (isset($response['ResultCode']) && $response['ResultCode'] === "0") {
                // Process the successful payment
                if ($orderId) {
                    // Get the cart
                    $cart = Cart::getCart();

                    if ($cart && $cart->id == $orderId) {
                        try {
                            // Create the order from cart data
                            $order = $this->orderRepository->create(Cart::prepareDataForOrder());

                            // Update the order with payment details
                            $this->orderRepository->update([
                                'status' => 'processing',
                                'mpesa_receipt' => $response['MpesaReceiptNumber'] ?? 'MPESA-' . time(),
                                'mpesa_phone' => $response['PhoneNumber'] ?? session()->get('mpesa_phone'),
                                'transaction_date' => now()->format('Y-m-d H:i:s')
                            ], $order->id);

                            if ($order->canInvoice()) {
                                $this->invoiceRepository->create($this->prepareInvoiceData($order));
                            }

                            // Deactivate the current cart session
                            Cart::deActivateCart();

                            session()->flash('order', $order);

                            // Store success in cache
                            \Cache::put($cacheKey, [
                                'success' => true,
                                'amount' => $cart->grand_total,
                                'receipt' => $response['MpesaReceiptNumber'] ?? 'MPESA-' . time(),
                                'phone' => $response['PhoneNumber'] ?? session()->get('mpesa_phone'),
                                'date' => now()->format('Y-m-d H:i:s')
                            ], now()->addHours(1));

                            Log::info('Order created successfully', ['order_id' => $order->id]);

                            return response()->json([
                                'success' => true,
                                'message' => 'Payment completed successfully',
                                'redirect' => url('/checkout/success')
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error creating order: ' . $e->getMessage(), [
                                'trace' => $e->getTraceAsString()
                            ]);

                            // Even if order creation fails, we should still indicate success
                            // since the payment was successful
                            \Cache::put($cacheKey, [
                                'success' => true,
                                'amount' => $cart->grand_total,
                                'receipt' => 'MPESA-' . time(),
                                'phone' => session()->get('mpesa_phone'),
                                'date' => now()->format('Y-m-d H:i:s'),
                                'error' => 'Order creation failed: ' . $e->getMessage()
                            ], now()->addHours(1));

                            return response()->json([
                                'success' => true,
                                'message' => 'Payment completed successfully, but there was an issue creating your order. Please contact customer support.',
                                'redirect' => url('/checkout/success')
                            ]);
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment is being processed',
                    'status'  => $response
                ]);
            } else if (isset($response['ResultCode']) && $response['ResultCode'] === "1032") {
                // User cancelled the payment
                return response()->json([
                    'success' => false,
                    'message' => 'Payment cancelled by user',
                    'status'  => $response,
                    'redirect' => url('/checkout/onepage')
                ]);
            }

            // Still pending
            return response()->json([
                'success' => false,
                'message' => $response['ResultDesc'] ?? 'Transaction is still pending',
                'status'  => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Mpesa status check exception: ' . $e->getMessage());

            // Extract the error message from the exception
            $errorMessage = $e->getMessage();

            // Check if it's a specific Safaricom API error
            if (strpos($errorMessage, 'Failed to check transaction status:') !== false) {
                // Extract the specific error message from Safaricom
                $safaricomError = str_replace('Failed to check transaction status: ', '', $errorMessage);

                return response()->json([
                    'success' => false,
                    'message' => 'M-Pesa Error: ' . $safaricomError,
                    'error_details' => $errorMessage
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to check transaction status. Please try again later.',
                'error_details' => $errorMessage
            ]);
        }
    }
}
