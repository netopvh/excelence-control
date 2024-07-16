<?php

namespace App\Providers;

use Automattic\WooCommerce\Client;
use Illuminate\Support\ServiceProvider;

class WooCommerceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                env('WOOCOMMERCE_STORE_URL'), // URL da sua loja
                env('WOOCOMMERCE_CONSUMER_KEY'), // Consumer Key
                env('WOOCOMMERCE_CONSUMER_SECRET'), // Consumer Secret
                [
                    'wp_api' => true,
                    'version' => 'wc/v3',
                ]
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
