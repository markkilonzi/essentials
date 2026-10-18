<?php

namespace Nyumbani\Essentials\Logging\Discord;

use Illuminate\Log\Logger;
use Nyumbani\Essentials\Logging\RateLimitedHandler;

class ThrottleDiscordChannel
{
    /**
     * Rate limit the channel handlers.
     */
    public function __invoke(Logger $logger, string $maxAttempts = '20', string $decaySeconds = '60'): void
    {
        //  Define an empty array
        $handlers = [];

        //  Loop through all handlers and replace them with rate limited versions
        foreach ($logger->getHandlers() as $handler) {
            $handlers[] = new RateLimitedHandler(
                $handler,
                'discord-notifications',
                (int) $maxAttempts,
                (int) $decaySeconds,
            );
        }

        //  Set the new handlers
        $logger->setHandlers($handlers ?? []);
    }
}
