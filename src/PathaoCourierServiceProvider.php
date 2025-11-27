<?php

namespace Nayemuf\PathaoCourier;

use Illuminate\Support\ServiceProvider;

class PathaoCourierServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/pathao.php',
            'pathao'
        );

        // Register singleton for PathaoCourier client
        $this->app->singleton('pathao.courier', function ($app) {
            return new PathaoCourier(
                config('pathao.client_id'),
                config('pathao.client_secret'),
                config('pathao.username'),
                config('pathao.password'),
                config('pathao.sandbox', false),
                config('pathao.store_id')
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__ . '/../config/pathao.php' => config_path('pathao.php'),
        ], 'pathao-config');
    }
}

