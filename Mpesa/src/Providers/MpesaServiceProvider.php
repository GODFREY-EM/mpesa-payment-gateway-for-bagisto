<?php

namespace Webkul\Mpesa\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Webkul\Theme\ViewRenderEventManager;
use Webkul\Checkout\Facades\Cart;
use Brunoadul\Mpesa\Lib\MpesaHelper;

class MpesaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mpesa');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'mpesa');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');


        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Resources/views' => resource_path('views/vendor/mpesa'),
            ], 'mpesa-views');

            $this->publishes([
                __DIR__ . '/../Config/system.php' => config_path('mpesa-system.php'),
                __DIR__ . '/../Config/paymentmethods.php' => config_path('mpesa-payment.php'),
                __DIR__ . '/../Config/mpesa.php' => config_path('mpesa.php'),
            ], 'mpesa-config');

            $this->publishes([
                __DIR__ . '/../Resources/lang' => resource_path('lang/vendor/mpesa'),
            ], 'mpesa-lang');

            $this->publishes([
                __DIR__ . '/../../publishable/assets' => public_path('vendor/mpesa'),
            ], 'mpesa-assets');
        }

        // Inject custom M-Pesa modal and override in checkout page
        Event::listen('bagisto.shop.layout.body.after', function ($viewRenderEventManager) {
            if (request()->is('checkout/onepage') || request()->is('checkout/onepage/*')) {
                $cart = Cart::getCart();
                if ($cart) {
                    return view('mpesa::mpesa-modal', ['cart' => $cart]) . view('mpesa::checkout-override');
                }
            }
        });
    }
    

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    $this->registerConfig();

    // Register the M-Pesa Payment method class
    $this->app->singleton(\Webkul\Mpesa\Payment\Mpesa::class, function ($app) {
        return new \Webkul\Mpesa\Payment\Mpesa();
    });

    $this->app->alias(\Webkul\Mpesa\Payment\Mpesa::class, 'mpesa');

    // Register the MpesaHelper class
    $this->app->singleton(\Brunoadul\Mpesa\Lib\MpesaHelper::class, function ($app) {
        return new \Brunoadul\Mpesa\Lib\MpesaHelper();
    });
    }


    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/mpesa.php', 'mpesa'
        );
    }
}
