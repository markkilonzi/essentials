<?php

namespace Nyumbani\Essentials\Logging;

use Illuminate\Support\Facades\RateLimiter;
use Monolog\Handler\Handler;
use Monolog\Handler\HandlerInterface;
use Monolog\LogRecord;

class RateLimitedHandler extends Handler
{
    public function __construct(
        private readonly HandlerInterface $handler,
        private readonly string $key,
        private readonly int $maxAttempts,
        private readonly int $decaySeconds,
    ) {}

    /**
     * Handle a log record
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handle(LogRecord $record): bool
    {
        //  Check if the handler should be used
        if (! $this->isHandling($record)) {
            return false;
        }

        //  Check if the rate limit has been exceeded
        return (bool) RateLimiter::attempt(
            $this->key,
            $this->maxAttempts,
            fn () => $this->handler->handle($record),
            $this->decaySeconds,
        );
    }

    /**
     * Check if the handler should be used for the given record.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function isHandling(LogRecord $record): bool
    {
        return $this->handler->isHandling($record);
    }
}
