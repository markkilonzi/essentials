@extends('essentials::docs.layout')

@section('page', 'Logging to Discord')
@section('lead', 'Send errors to a Discord channel without flooding it.')

@section('content')
    <x-essentials::docs.section id="channel" title="Add the channel">
        <p>The package registers a <code>discord</code> log driver, the way Laravel ships a <code>slack</code> one. A channel only names the driver and its options; you write no handler and no taps.</p>
        <x-essentials::docs.code title="config/logging.php">
            'discord' => [
                'driver' => 'discord',
                'url' => env('DISCORD_WEBHOOK_URL'),
                'username' => env('APP_NAME', 'Nyumbani'),
                'level' => env('DISCORD_LOG_LEVEL', 'error'),
            ],
        </x-essentials::docs.code>
        <p>Add <code>discord</code> to the <code>stack</code> channel's list to receive errors there:</p>
        <x-essentials::docs.code title="config/logging.php">
            'stack' => [
                'driver' => 'stack',
                'channels' => ['daily', 'discord'],
            ],
        </x-essentials::docs.code>
        <x-essentials::docs.table :columns="['Option', 'Default', 'What it does']" :rows="[
            ['url', 'none', 'The Discord webhook address. With none, the channel logs nowhere and raises no error, so a machine without a webhook still works.'],
            ['username', 'the app name', 'The name the messages are posted under.'],
            ['level', 'error', 'The lowest level sent: debug, info, notice, warning, error, critical, alert or emergency.'],
            ['throttle', '[attempts => 20, seconds => 60]', 'At most this many messages in that many seconds, counted in your cache store across workers. Records over the limit still reach your other channels. Set false to switch the throttle off.'],
            ['max_length', '3500', 'The longest message sent. Longer ones are cut, to fit Discord.'],
        ]" />
        <x-essentials::docs.code title="Every option">
            'discord' => [
                'driver' => 'discord',
                'url' => env('DISCORD_WEBHOOK_URL'),
                'username' => env('APP_NAME', 'Nyumbani'),
                'level' => 'warning',
                'throttle' => ['attempts' => 10, 'seconds' => 60],
                'max_length' => 2000,
            ],
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="message" title="What arrives in Discord">
        <p>Each error becomes one embed: the level and channel as the title, the log message as the description, and fields underneath. The fields come from the log context, which Essentials fills for every web request (see <a href="{{ $docs->url('defaults') }}">App defaults</a>), so an error tells you who hit it.</p>
        <x-essentials::docs.table :columns="['Embed part', 'Holds']" :rows="[
            ['Title', 'The level and channel, such as ERROR - STACK. The colour follows the level.'],
            ['Description', 'The log message, cut to the max_length option.'],
            ['File', 'The file and line of the exception, when there is one.'],
            ['Name, Organization, Request', 'The signed-in user, their organization and the request payload (passwords are removed).'],
            ['error.log attachment', 'The full stack trace as a text file, when the record carries an exception.'],
        ]" />
        <p>A field is cut to 1,024 characters, at most 25 are sent, and the embed as a whole stays under Discord's 6,000-character limit. A failed delivery is logged to the <code>daily</code> channel, never back to Discord.</p>
            <x-essentials::docs.playground demo="discord" manual="Send to Discord" title="Send a log to Discord" :fields="[
            ['name' => 'level', 'label' => 'Level', 'type' => 'select', 'value' => 'Error', 'options' => ['Debug' => 'Debug', 'Info' => 'Info', 'Warning' => 'Warning', 'Error' => 'Error', 'Critical' => 'Critical']],
            ['name' => 'message', 'label' => 'Log message', 'value' => 'The payment could not be settled.', 'placeholder' => 'e.g. The payment could not be settled.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="env" title="Environment">
        <x-essentials::docs.code lang="bash" title=".env">
            DISCORD_WEBHOOK_URL=
            DISCORD_LOG_LEVEL=error
        </x-essentials::docs.code>
        <x-essentials::docs.callout type="warning">The webhook URL is a secret. Keep it in <code>.env</code> only.</x-essentials::docs.callout>

    </x-essentials::docs.section>
@endsection
