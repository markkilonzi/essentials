@extends('essentials::docs.layout')

@section('page', 'Logging to Discord')
@section('lead', 'Send errors to a Discord channel without flooding it.')

@section('content')
    <x-essentials::docs.section id="channel" title="Add the channel">
        <p>The package provides a Monolog handler and two taps: one that throttles repeats, and one that trims long messages to fit Discord.</p>
        <x-essentials::docs.code title="config/logging.php">
            use Nyumbani\Essentials\Logging\Discord\DiscordWebhookHandler;
            use Nyumbani\Essentials\Logging\Discord\ThrottleDiscordChannel;
            use Nyumbani\Essentials\Logging\Discord\TruncateDiscordMessage;

            'discord' => [
                'driver' => 'monolog',
                'level' => env('DISCORD_LOG_LEVEL', 'debug'),
                'handler' => DiscordWebhookHandler::class,
                'with' => [
                    'webhookUrl' => (string) env('DISCORD_WEBHOOK_URL'),
                    'username' => env('APP_NAME', 'Nyumbani'),
                    'level' => env('DISCORD_LOG_LEVEL', 'debug'),
                ],
                'tap' => [
                    ThrottleDiscordChannel::class.':20,60',
                    TruncateDiscordMessage::class.':3500',
                ],
            ],
        </x-essentials::docs.code>
        <p>Add <code>discord</code> to the <code>stack</code> channel's list to receive errors there.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="env" title="Environment">
        <x-essentials::docs.code lang="bash" title=".env">
            DISCORD_WEBHOOK_URL=
            DISCORD_LOG_LEVEL=error
        </x-essentials::docs.code>
        <x-essentials::docs.callout type="warning">The webhook URL is a secret. Keep it in <code>.env</code> only.</x-essentials::docs.callout>
        <p>The throttle <code>20,60</code> allows 20 messages in 60 seconds, and <code>3500</code> is the longest message sent.</p>
    </x-essentials::docs.section>
@endsection
