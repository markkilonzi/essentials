@extends('essentials::docs.layout')

@section('page', 'App defaults')
@section('lead', 'Safer, quieter Laravel out of the box, each default one switch away from off.')

@section('content')
    <x-essentials::docs.section id="what" title="What is applied">
        <p>The package applies these on boot. Each is a key under <code>essentials.defaults</code>; set it to <code>false</code> to switch it off.</p>
        <x-essentials::docs.table :columns="['Key', 'Effect']" :rows="[
            ['prohibit_destructive_commands', 'migrate:fresh, db:wipe and friends refuse to run in production.'],
            ['prevent_lazy_loading', 'A missed eager-load throws outside production, so N+1 queries surface in development.'],
            ['unwrapped_json_resources', 'API resources return their data without the data wrapper.'],
            ['debugbar', 'The debugbar is enabled locally when it is installed.'],
            ['context', 'The signed-in user, organization and request payload join every log entry of a web request.'],
            ['error_pages', 'The package error pages replace the framework ones.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="disable" title="Switch one off">
        <x-essentials::docs.code title="config/essentials.php">
            'defaults' => [
                'prevent_lazy_loading' => false,
            ],
        </x-essentials::docs.code>
        <x-essentials::docs.callout type="note" title="Passwords are never logged">
            The log context drops <code>password</code>, <code>password_confirmation</code>, <code>current_password</code> and <code>_token</code> and skips uploaded files.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>
@endsection
