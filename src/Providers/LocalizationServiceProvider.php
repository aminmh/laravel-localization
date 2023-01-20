<?php

namespace Bugloos\LaravelLocalization\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class LocalizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // App::setLocale();

        $this->publishes([
            __DIR__.'/../config/localization.php' => config_path('localization.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
