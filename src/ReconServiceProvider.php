<?php

namespace Recon;

use Illuminate\Support\ServiceProvider;
use Recon\Api\Api;
use Recon\Commands\ReconCommand;
use Recon\Commands\ReconRetrainCommand;
use Recon\Commands\ReconSeedCommand;

class ReconServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'recon');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'recon');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('recon.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/recon'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/recon'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/recon'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([
                ReconCommand::class,
                ReconSeedCommand::class,
                ReconRetrainCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'recon');

        // Register the main class to use with the facade
        $this->app->singleton('recon', function ($app) {
            return $app->make(Recon::class);
        });
        $this->app->singleton('recon-api', function ($app) {
            return $app->make(Api::class);
        });
    }
}
