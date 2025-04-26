<?php

use Illuminate\Support\Facades\Route;
use Webkul\AzamPay\Http\Controllers\AzamPayController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {

    Route::get('/azampay-redirect', [AzamPayController::class, 'redirect'])->name('azampay.process'); // Updated route to match AzamPay

    Route::get('/azampay-success', [AzamPayController::class, 'success'])->name('azampay.success'); // Updated route to match AzamPay

    Route::post('/azampay-cancel', [AzamPayController::class, 'failure'])->name('azampay.cancel'); // Updated route to match AzamPay

});
