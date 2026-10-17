<?php

namespace Nyumbani\Essentials;

use Illuminate\Support\ServiceProvider;

class EssentialsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function register(): void
    {
        //  Load helpers
        $this->loadHelpersFrom(__DIR__.'/Utils');

        //  Load the configuration file
        $this->mergeConfigFrom(essentials_path('config/essentials.php'), 'essentials');
    }

    /**
     * Bootstrap the application services.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Load the views
        $this->loadViewsFrom(essentials_path('resources/views'), 'essentials');

        //  Load the translations
        $this->loadTranslationsFrom(essentials_path('lang'), 'essentials');

        //  Load the routes
        $this->loadRoutesFrom(essentials_path('routes/web.php'));

        //  Publish the configuration file
        $this->publishes([
            essentials_path('config/essentials.php') => config_path('essentials.php'),
        ], 'essentials-config');
    }

    /**
     * Add any package helpers to the application.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function loadHelpersFrom(string $path): void
    {
        //  Require each helper file once
        foreach (glob("$path/*.php") as $helper) {
            require_once $helper;
        }
    }
}
