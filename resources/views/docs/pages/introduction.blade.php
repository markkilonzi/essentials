@extends('essentials::docs.layout')

@section('page', 'Introduction')
@section('lead', 'One sign-in, one look and one set of conventions for every internal Nyumbani app.')

@section('content')
    <x-essentials::docs.section id="what-it-is" title="What Essentials is">
        <p>Essentials is the opinionated foundation of the Nyumbani ecosystem. It does not give you building blocks to arrange as you like. It decides how a Nyumbani app looks and is structured, so a developer who knows one app can work in any other.</p>
        <p>It covers five areas:</p>
        <ol>
            <li><strong>Helpers</strong> for money, numbers, dates and the signed-in user.</li>
            <li><strong>Services and traits</strong> that models and controllers share.</li>
            <li><strong>UI</strong>: layouts, a modal, toasts, a loader, error pages, favicons and a theme.</li>
            <li><strong>Packages</strong> it standardises, such as Fortify, permissions and impersonation.</li>
            <li><strong>AI rules and skills</strong> that keep every contributor, human or not, on the same conventions.</li>
        </ol>
        <x-essentials::docs.callout type="tip" title="Use it before you build it">
            Before writing a modal, a toast, a helper or an error page, check these docs. If Essentials has one, use it. If it is missing, add it to the package so every app gains it.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="explore" title="Explore by concept">
        <div class="not-prose mt-4 grid gap-4 sm:grid-cols-2">
            <x-essentials::docs.card title="Installation" href="{{ $docs->url('installation') }}">Add the package, import the theme and scripts, and publish the rules.</x-essentials::docs.card>
            <x-essentials::docs.card title="Helpers" href="{{ $docs->url('helpers') }}">money(), numeric(), format_date() and the Blade directives built on them.</x-essentials::docs.card>
            <x-essentials::docs.card title="Traits" href="{{ $docs->url('traits') }}">Ulids, uploads, money, number identifiers and more for your models.</x-essentials::docs.card>
            <x-essentials::docs.card title="Layouts" href="{{ $docs->url('layouts') }}">The portal, guest and base layouts, with the sidebar and navbar.</x-essentials::docs.card>
            <x-essentials::docs.card title="Modal" href="{{ $docs->url('modal') }}">Dialogs, slide-overs, remote content and reopen-on-error.</x-essentials::docs.card>
            <x-essentials::docs.card title="Rules and skills" href="{{ $docs->url('rules-and-skills') }}">The conventions the package publishes into your app and your AI agent.</x-essentials::docs.card>
        </div>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="about-docs" title="About these docs">
        <p>These pages are served by the package itself at <code>/{{ $docs->path() }}</code>. Every example here is meant to be copied.</p>
    </x-essentials::docs.section>
@endsection
