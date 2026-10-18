<?php

namespace Nyumbani\Essentials\Logging\Discord;

use Illuminate\Log\Logger;
use Monolog\LogRecord;

class TruncateDiscordMessage
{
    /**
     * Truncate log messages to the embed limit.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __invoke(Logger $logger, string $maxLength = '3500'): void
    {
        //  Create a callback that truncates the message
        $callback = fn (LogRecord $record) => strlen($record->message) > (int) $maxLength
            ? $record->with(message: str()->limit($record->message, (int) $maxLength))
            : $record;

        //  Push a processor that trims oversized messages before they reach the handler
        $logger->pushProcessor($callback);
    }
}
