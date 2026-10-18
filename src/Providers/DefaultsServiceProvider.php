<?php

namespace Nyumbani\Essentials\Providers;

use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Nyumbani\Essentials\Http\Middleware\AddContext;

class DefaultsServiceProvider extends ServiceProvider
{
    /**
     * Apply the app-wide defaults, each switchable in essentials.defaults
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Prohibit destructive commands in the production environment
        if (config('essentials.defaults.prohibit_destructive_commands', true)) {
            DB::prohibitDestructiveCommands(app()->isProduction());
        }

        //  Prevent lazy loading outside production, so a missed eager-load throws
        if (config('essentials.defaults.prevent_lazy_loading', true)) {
            Model::preventLazyLoading(! app()->isProduction());
        }

        //  Enable the debugbar locally when it is installed
        if (config('essentials.defaults.debugbar', true) && app()->isLocal() && class_exists(Debugbar::class)) {
            Debugbar::enable();
        }

        //  Add the user, organization and request to the log context of every web request
        if (config('essentials.defaults.context', true)) {
            app(Router::class)->pushMiddlewareToGroup('web', AddContext::class);
        }
    }
}
