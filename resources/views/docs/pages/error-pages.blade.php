@extends('essentials::docs.layout')

@section('page', 'Error pages')
@section('lead', 'A friendly page for every common error, with the Nyumbani logo and a clear next step.')

@section('content')
    <x-essentials::docs.section id="shipped" title="What ships">
        <x-essentials::docs.table :columns="['Code', 'Heading']" :rows="[
            ['401', 'Hold on, who are you?'],
            ['403', 'Staff only beyond this door'],
            ['404', 'Page not found'],
            ['419', 'Your session took a nap'],
            ['429', 'Easy there, speedy'],
            ['500', 'Oops, something went wrong'],
            ['503', 'Back in a moment'],
        ]" />
        <p>Each page has the Nyumbani logo, the status code in a gradient, and an action: Sign in, Take me home, Go back or Try again.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="loading" title="How they load">
        <p>The package adds its views folder to the view paths, so Laravel finds the pages without publishing anything. A page in the app's own <code>resources/views/errors</code> still wins. Switch them off with <code>essentials.defaults.error_pages</code>.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="custom" title="Add or change a page">
        <x-essentials::docs.code lang="blade" title="resources/views/errors/410.blade.php">
            @verbatim
            @extends('essentials::layouts.error')

            @section('code', '410')
            @section('heading', 'Gone for good')
            @section('detail', 'This page has been removed.')
            @section('icon')
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            @endsection
            @section('actions')
                <a href="{{ url('/') }}" class="btn btn-primary btn-lg">Take me home</a>
            @endsection
            @endverbatim
        </x-essentials::docs.code>
        <x-essentials::docs.callout type="tip" title="Wording">
            Keep it warm and brief. Never blame the user, and say maintenance on a 503.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>
@endsection
