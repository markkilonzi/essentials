<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Nyumbani\Essentials\Services\Messaging\MessageProcessed;
use Nyumbani\Essentials\Services\Messaging\SendMessageNotifications;

class MessagingServiceProvider extends ServiceProvider
{
    /**
     * Wire up messaging, switchable in essentials.defaults
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Leave messaging alone when switched off
        if (! config('essentials.defaults.messaging', true)) {
            return;
        }

        //  Define the connect client
        $this->handleConnectClient();

        //  Define the rate limiter
        $this->handleRateLimiter();

        //  Define the listener
        $this->handleListener();
    }

    /**
     * Add the pre-authenticated Nyumbani Connect HTTP client
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function handleConnectClient(): void
    {
        //  Define the macro, reading the config when the client is built
        Http::macro('connect', fn () => Http::baseUrl(config('essentials.messaging.connect.base_url'))
            ->asJson()
            ->acceptJson()
            ->withToken(config('essentials.messaging.connect.access_token')));
    }

    /**
     * Limit how fast queued messages go out
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function handleRateLimiter(): void
    {
        //  Define the rate
        $rate = config('essentials.messaging.rate_limit_per_second');

        //  Define the callback
        $callback = fn () => Limit::perSecond($rate)->by('messages');

        //  Define the rate limiter
        RateLimiter::for('essentials.messages', $callback);
    }

    /**
     * Send processed messages through their gateways
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function handleListener(): void
    {
        Event::listen(MessageProcessed::class, SendMessageNotifications::class);
    }
}
