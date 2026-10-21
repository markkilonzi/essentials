@extends('docs::layout')

@section('page', 'Error pages')
@section('lead', 'A friendly page for every common error, with the Nyumbani logo and a clear next step.')

@section('content')
    <x-docs::section id="shipped" title="What ships">
        <x-docs::table :columns="['Code', 'Heading']" :rows="[
            ['401', 'Hold on, who are you?'],
            ['403', 'Staff only beyond this door'],
            ['404', 'Page not found'],
            ['419', 'Your session took a nap'],
            ['429', 'Easy there, speedy'],
            ['500', 'Oops, something went wrong'],
            ['503', 'Back in a moment'],
        ]" />
        <p>Each page has the Nyumbani logo, the status code in a gradient, and an action: Sign in, Take me home, Go back or Try again.</p>
            <x-docs::example title="Live: every error page, as a user sees it">
            <div x-data="{ code: '404' }">
                <div class="mb-3 flex flex-wrap gap-1.5">
                    @foreach (['401', '403', '404', '419', '429', '500', '503'] as $code)
                        <button type="button" class="btn btn-primary btn-sm" x-bind:class="code === '{{ $code }}' ? '' : 'opacity-60'" x-on:click="code = '{{ $code }}'">{{ $code }}</button>
                    @endforeach
                </div>
                <iframe title="Error page preview" loading="lazy" class="h-[32rem] w-full rounded-md border border-gray-200" x-bind:src="@js(url(request()->route('package').'/docs/samples/errors')) + '/' + code"></iframe>
            </div>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="loading" title="How they load">
        <p>The package adds its views folder to the view paths, so Laravel finds the pages without publishing anything. A page in the app's own <code>resources/views/errors</code> still wins. Switch them off with <code>essentials.defaults.error_pages</code>.</p>
    </x-docs::section>

    <x-docs::section id="custom" title="Add or change a page">
        <x-docs::code lang="blade" title="resources/views/errors/410.blade.php">
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
        </x-docs::code>
        <x-docs::callout type="tip" title="Wording">
            Keep it warm and brief. Never blame the user, and say maintenance on a 503.
        </x-docs::callout>
    </x-docs::section>
@endsection
