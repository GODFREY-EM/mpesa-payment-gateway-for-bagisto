<?php

namespace Webkul\AzamPay\Providers;

use Illuminate\Support\ServiceProvider;

class AzamPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
{
    $this->loadRoutesFrom(__DIR__.'/../Routes/shop-routes.php');

    $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'azampay');

    $viewsPath = __DIR__ . '/../Resources/views';
    if (is_dir($viewsPath)) {
        $this->loadViewsFrom($viewsPath, 'azampay');
    }
}


    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/paymentmethods.php', 'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php', 'core'
        );
    }
}