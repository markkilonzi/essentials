---
name: nyumbani-logging
description: "Invoke when a Nyumbani app sends errors or logs to Discord: adding the discord log channel, its webhook URL, level, throttle or message length, or when log records do not reach Discord. Skip for other log channels."
metadata:
  author: nyumbani
---

# Nyumbani Logging

Essentials registers a `discord` log driver, the way Laravel ships a `slack` one. A channel only names the driver and its options. Do not write a `handler`, `with` or `tap` entry, and do not import `DiscordWebhookHandler`.

## The channel
```php
// config/logging.php
'discord' => [
    'driver' => 'discord',
    'url' => env('DISCORD_WEBHOOK_URL'),
    'username' => env('APP_NAME', 'Nyumbani'),
    'level' => env('DISCORD_LOG_LEVEL', 'error'),
],

'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'discord'],
],
```
`DISCORD_WEBHOOK_URL` is a secret: only in the git-ignored `.env`, blank in `.env.example`.

## Options
| Key | Default | Meaning |
| --- | --- | --- |
| `url` | none | The webhook. With none, the channel logs nowhere and raises no error. |
| `username` | the app name | The name messages are posted under. |
| `level` | `error` | The lowest level sent. |
| `throttle` | `['attempts' => 20, 'seconds' => 60]` | At most this many messages per window, counted in the cache store across workers. `false` switches it off. Records over the limit still reach the other channels. |
| `max_length` | `3500` | Longer messages are cut to fit Discord. |

## What arrives
One embed per record: the level and channel as the title, the message as the description, and fields from the log context. Essentials adds the signed-in user, their organization and the request payload (passwords removed) to every web request, so an error says who hit it. A record with an exception also attaches the full stack trace as `error.log` and a `File` field.

## When nothing arrives
- `url` is empty, or `level` is above the record's level.
- The throttle was reached: only Discord skips the extra records.
- `discord` is missing from the stack's `channels`.
- A failed delivery is logged to the `daily` channel as `Discord webhook delivery failed`, never back to Discord.

## Testing
Fake `Http` and log to the channel: `Log::channel('discord')->error('Boom')`, then `Http::assertSent(...)`. Build the logger directly with `(new CreateDiscordLogger)(['url' => '...'])` to inspect its handlers.
