---
paths:
  - 'config/logging.php'
---

# Logging

## Discord is a log driver, not a handler
Add the Discord channel with the package's `discord` driver. Do not write `'handler'`, `'with'` or `'tap'` entries, and do not import `DiscordWebhookHandler`.
```php
'discord' => [
    'driver' => 'discord',
    'url' => env('DISCORD_WEBHOOK_URL'),
    'username' => env('APP_NAME', 'Nyumbani'),
],
```
`nyumbani:install` adds this block to `config/logging.php` when the app has no `discord` channel; the level, throttle and message length keep the driver's defaults unless you set them. Add `discord` to the `stack` channel's list (for example `LOG_STACK=daily,discord`). Optional keys: `throttle` (`['attempts' => 20, 'seconds' => 60]`, or `false`) and `max_length` (3500). With no `url` the channel logs nowhere and raises no error.

## The webhook URL is a secret
`DISCORD_WEBHOOK_URL` lives only in the git-ignored `.env`; `.env.example` lists the key with a blank value.
