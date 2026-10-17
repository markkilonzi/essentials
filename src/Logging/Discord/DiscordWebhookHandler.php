<?php

namespace Nyumbani\Essentials\Logging\Discord;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Throwable;

class DiscordWebhookHandler extends AbstractProcessingHandler
{
    /**
     * Embed colors keyed by Monolog level, matching Discord's decimal color format.
     *
     * @var array<int, int>
     */
    private const array LEVEL_COLORS = [
        Level::Debug->value => 0x95A5A6,
        Level::Info->value => 0x3498DB,
        Level::Notice->value => 0x3498DB,
        Level::Warning->value => 0xF1C40F,
        Level::Error->value => 0xE74C3C,
        Level::Critical->value => 0x992D22,
        Level::Alert->value => 0x992D22,
        Level::Emergency->value => 0x000000,
    ];

    /**
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __construct(
        private readonly string $webhookUrl,
        private readonly string $username = 'Nyumbani Bot',
        int|string|Level $level = Level::Error,
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * Post the log record to Discord's native webhook endpoint
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function write(LogRecord $record): void
    {
        //  Define the initial fields
        $fields = Arr::dot(array_merge($record->extra, Arr::except($record->context, [
            'exception',
        ])));

        //  Define the map callback
        $map = fn ($value, $key) => [
            'name' => (string) str($key)->replace('.', ' ')->headline()->limit(256),
            'value' => (string) str(is_scalar($value) ? $value : json_encode($value))->limit(1024),
            'inline' => false,
        ];

        //  Get the exception, if any, so its file/line can be surfaced as a field
        $exception = $record->context['exception'] ?? null;

        //  Flatten whatever AddContext
        $fields = collect($fields)
            ->reject(fn ($value) => $value === null || $value === '' || empty($value))
            ->map($map)
            ->take(25)
            ->values()
            ->all();

        //  Prepend the exception's file and line, when there is one
        if ($exception instanceof Throwable) {
            //  Get file name
            $file = $exception->getFile();

            //  Get the line number
            $line = $exception->getLine();

            //  Get the file name
            $locationFields[] = [
                'name' => 'File',
                'value' => "{$file} - Line {$line}",
                'inline' => false,
            ];

            //  Merge the fields
            $fields = array_merge($locationFields, $fields);
        }

        //  Get the title
        $title = strtoupper($record->level->getName().' - '.$record->channel);

        //  Get the description
        $description = $record->message;

        //  trim or drop trailing fields once that shared budget runs out, due to Discord's 6000 char limit
        $fields = $this->capFieldsToEmbedBudget($fields, 6000 - mb_strlen($title) - mb_strlen($description));

        //  Build the payload
        $payload = [
            'username' => $this->username,
            'embeds' => [[
                'title' => $title,
                'description' => $description,
                'color' => self::LEVEL_COLORS[$record->level->value] ?? 0xE74C3C,
                'fields' => $fields,
                'timestamp' => $record->datetime->format(\DateTimeInterface::ATOM),
            ]],
        ];

        //  Build the request
        $request = Http::asMultipart();

        //  Attach the stack trace into the file
        if ($exception instanceof Throwable) {
            $request = $request->attach(
                'files[0]',
                $this->formatTrace($exception),
                'error.log',
                ['Content-Type' => 'text/plain'],
            );
        }

        //  Send the webhook - substitute invalid UTF-8 bytes
        $response = $request->post($this->webhookUrl, [
            'payload_json' => json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE),
        ]);

        //  Log delivery failures
        if ($response->failed()) {
            Log::channel('daily')->warning('Discord webhook delivery failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    /**
     * Keep fields within Discord's shared 6000 char embed budget
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    private function capFieldsToEmbedBudget(array $fields, int $budget): array
    {
        //  Capped fields
        $capped = [];

        //  Loop through the fields
        foreach ($fields as $field) {
            //  Determine the name length
            $nameLength = mb_strlen($field['name']);

            //  Determine the available space
            $availableForValue = $budget - $nameLength;

            //  Determine if there's enough space
            if ($availableForValue <= 0) {
                break;
            }

            //  Else, if the value is too long, trim it
            if (mb_strlen($field['value']) > $availableForValue) {
                $field['value'] = (string) str($field['value'])->limit($availableForValue, '');
            }

            //  Add the field
            $capped[] = $field;

            //  Reduce the budget
            $budget -= $nameLength + mb_strlen($field['value']);
        }

        //  Return the capped fields
        return $capped;
    }

    /**
     * Build the full, untruncated exception trace as plain text.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    private function formatTrace(Throwable $exception): string
    {
        return sprintf(
            "%s: %s\nin %s:%d\n\n%s",
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString(),
        );
    }
}
