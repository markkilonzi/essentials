@extends('essentials::docs.layout')

@section('page', 'Configuration')
@section('lead', 'Sensible defaults for every app, changed from one config file or the environment.')

@section('content')
    <x-essentials::docs.section id="publish" title="Publish the config">
        <p>Apps inherit the defaults, and <code>php artisan nyumbani:install</code> publishes the file for you. Edit it only to change one.</p>
        <x-essentials::docs.code lang="bash" title="Terminal">
            php artisan vendor:publish --tag=essentials-config
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="options" title="Options">
        <x-essentials::docs.table :columns="['Key', 'Default', 'What it does']" :rows="[
            ['currency', 'KES', 'The symbol money() and currency() use. Set with ESSENTIALS_CURRENCY.'],
            ['fortify.max_attempts', '5', 'Sign-in attempts per minute for each username and IP address.'],
            ['defaults.*', 'true', 'Switches for the app-wide defaults. See App defaults.'],
            ['branding.description', 'null', 'The sentence under the app name on the sign-in page. Set with ESSENTIALS_DESCRIPTION.'],
            ['branding.theme_color', '#d97706', 'The browser address bar and installed app colour. Set with ESSENTIALS_THEME_COLOR.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="env" title="Environment variables">
        <x-essentials::docs.code lang="bash" title=".env">
            ESSENTIALS_CURRENCY=KES
            ESSENTIALS_THEME_COLOR=#d97706
            ESSENTIALS_DESCRIPTION="One sign-in for every internal app."
        </x-essentials::docs.code>
        <x-essentials::docs.callout type="warning" title="Keep secrets out of .env.example">
            Credentials, webhook URLs and keys live only in the git-ignored <code>.env</code>. <code>.env.example</code> lists every key with a blank value.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>
@endsection
