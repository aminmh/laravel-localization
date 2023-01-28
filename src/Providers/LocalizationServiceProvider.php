<?php

namespace Bugloos\LaravelLocalization\Providers;

use Bugloos\LaravelLocalization\{Loader,Translator};
use Illuminate\Contracts\Foundation\Application;
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
        $this->app->bind('localization', function (Application $app) {
            return new Translator(new Loader($app['files'], $app['path.lang']), $app->getLocale());
        });
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
            __DIR__ . '/../config/localization.php' => config_path('localization.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    private function getAppLangPath()
    {
        return $this->app['path.lang'];
    }

    private function getFileSystem()
    {
        return $this->app['files'];
    }
}
