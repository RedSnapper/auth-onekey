<?php

namespace RedSnapper\OneKey;

use Illuminate\Support\ServiceProvider;

class OneKeyServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        $this->publishes([
          __DIR__.'/../config/onekey.php' => config_path('onekey.php'),
        ], 'onekey-config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
          __DIR__.'/../config/onekey.php', 'onekey'
        );

        $this->app->singleton(OneKeyProvider::class, function ($app) {
            return new OneKeyProvider(new PhpCASBridge, config('onekey'));
        });
    }
}