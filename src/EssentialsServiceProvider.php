<?php

namespace Nyumbani\Essentials;

use Illuminate\Support\ServiceProvider;
use Nyumbani\Essentials\Console\Commands\InstallCommand;
use Nyumbani\Essentials\Console\Commands\PermissionsCommand;
use Nyumbani\Essentials\Providers\DefaultsServiceProvider;
use Nyumbani\Essentials\Providers\FortifyServiceProvider;
use Nyumbani\Essentials\Providers\MacroServiceProvider;
use Nyumbani\Essentials\Providers\ModelServiceProvider;
use Nyumbani\Essentials\Providers\ViewServiceProvider;

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

        //  Register the Blade directives
        $this->app->register(ViewServiceProvider::class);

        //  Register the redirect macros
        $this->app->register(MacroServiceProvider::class);

        //  Register the model defaults
        $this->app->register(ModelServiceProvider::class);

        //  Register the app-wide defaults
        $this->app->register(DefaultsServiceProvider::class);

        //  Register the Fortify defaults
        $this->app->register(FortifyServiceProvider::class);
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

        //  Register the Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class, PermissionsCommand::class]);
        }
    }

    /**
     * Add any package helpers to the application.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function loadHelpersFrom(string $path): void
    {
        foreach (glob("$path/*.php") as $helper) {
            require_once $helper;
        }
    }
}
