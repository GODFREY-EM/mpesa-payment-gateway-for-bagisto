<?php

use Illuminate\Support\Facades\Route;
use Webkul\Mpesa\Http\Controllers\MpesaController;

Route::group([
    'middleware' => ['web', 'theme', 'locale', 'currency']
], function () {
    Route::prefix('mpesa')->group(function () {
        Route::match(['get', 'post'], 'process', [MpesaController::class, 'initiateSTK'])->name('mpesa.process');
        Route::get('waiting/{orderId}', function($orderId) {
            return view('mpesa::stk-waiting-new', compact('orderId'));
        })->name('mpesa.waiting');
        Route::get('status/{orderId}', [MpesaController::class, 'checkStatus'])->name('mpesa.status');
        Route::post('callback', [MpesaController::class, 'callback'])->name('mpesa.callback');

        // Test route
        Route::get('test', function() {
            return view('mpesa::test');
        })->name('mpesa.test');

        // Test form route
        Route::get('test-form', function() {
            return view('mpesa::mpesa-form');
        })->name('mpesa.test-form');
    });
});
