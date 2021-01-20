<?php

namespace LaravelMl;

use Illuminate\Support\ServiceProvider;
use LaravelMl\Commands\ModelsSync;

class LaravelMlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-ml');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ml');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-ml.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ml'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-ml'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-ml'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([
                ModelsSync::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-ml');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-ml', function ($app) {
            return $app->make(LaravelMl::class);
        });
        $this->app->singleton('laravel-ml-api', function ($app) {
            return $app->make(Api::class);
        });
    }
}
