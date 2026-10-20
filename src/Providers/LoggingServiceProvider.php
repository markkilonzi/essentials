<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Nyumbani\Essentials\Logging\Discord\CreateDiscordLogger;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register the discord log driver, so a channel only names it
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        Log::extend('discord', fn ($app, array $config) => (new CreateDiscordLogger)($config));
    }
}
