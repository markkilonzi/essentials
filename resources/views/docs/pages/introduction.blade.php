@extends('docs::layout')

@section('page', 'Introduction')
@section('lead', 'One sign-in, one look and one set of conventions for every internal Nyumbani app.')

@section('content')
    <x-docs::section id="what-it-is" title="What Essentials is">
        <p>Essentials is the opinionated foundation of the Nyumbani ecosystem. It does not give you building blocks to arrange as you like. It decides how a Nyumbani app looks and is structured, so a developer who knows one app can work in any other.</p>
        <p>It covers five areas:</p>
        <ol>
            <li><strong>Helpers</strong> for money, numbers, dates and the signed-in user.</li>
            <li><strong>Services and traits</strong> that models and controllers share.</li>
            <li><strong>UI</strong>: layouts, a modal, toasts, a loader, error pages, favicons and a theme.</li>
            <li><strong>Packages</strong> it standardises, such as Fortify, permissions and impersonation.</li>
            <li><strong>AI rules and skills</strong> that keep every contributor, human or not, on the same conventions.</li>
        </ol>
        <x-docs::callout type="tip" title="Use it before you build it">
            Before writing a modal, a toast, a helper or an error page, check these docs. If Essentials has one, use it. If it is missing, add it to the package so every app gains it.
        </x-docs::callout>
    </x-docs::section>

    <x-docs::section id="path" title="Where to start">
        <x-docs::table :columns="['You are...', 'Read, in this order']" :rows="[
            ['Adding Essentials to an app', 'Installation, Configuration, then Quick start.'],
            ['Building your first feature', 'Quick start, Anatomy of a feature, then the page for each piece you meet.'],
            ['Looking for one thing', 'Use the filter at the top of the sidebar, or the section list below.'],
            ['An AI agent, or working with one', 'Rules and skills, then the Conventions pages.'],
        ]" />
    </x-docs::section>

    <x-docs::section id="explore" title="Explore by concept">
        <div class="not-prose mt-4 grid gap-4 sm:grid-cols-2">
            <x-docs::card title="Installation" href="{{ $docs->url('installation') }}">Add the package, import the theme and scripts, and publish the rules.</x-docs::card>
            <x-docs::card title="Helpers" href="{{ $docs->url('helpers') }}">money(), numeric(), format_date() and the Blade directives built on them.</x-docs::card>
            <x-docs::card title="Traits" href="{{ $docs->url('traits') }}">Ulids, uploads, money, number identifiers and more for your models.</x-docs::card>
            <x-docs::card title="Layouts" href="{{ $docs->url('layouts') }}">The portal, auth, error and base layouts, with the sidebar and navbar, and the code to extend each.</x-docs::card>
            <x-docs::card title="Modal" href="{{ $docs->url('modal') }}">Dialogs, slide-overs, remote content and reopen-on-error.</x-docs::card>
            <x-docs::card title="Quick start" href="{{ $docs->url('quick-start') }}">Build one real page and meet most of Essentials on the way.</x-docs::card>
            <x-docs::card title="Authentication" href="{{ $docs->url('authentication') }}">Sign-in, password reset, home routes and impersonation.</x-docs::card>
            <x-docs::card title="Rules and skills" href="{{ $docs->url('rules-and-skills') }}">The conventions the package publishes into your app and your AI agent.</x-docs::card>
        </div>
    </x-docs::section>

    <x-docs::section id="packages" title="The other Nyumbani packages">
        <p>Essentials is the foundation, and it requires three more Nyumbani packages, so every app that installs it gets all four. Each has its own docs, in the package switcher at the top of this page, and a short page for each under Foundation in the sidebar. They need only Laravel themselves, so they never require Essentials.</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-docs::card title="Daraja" href="{{ url('nyumbani-daraja/docs') }}">M-Pesa through Safaricom: STK push, C2B and Pull transactions, and the guard for their callbacks.</x-docs::card>
            <x-docs::card title="Messaging" href="{{ url('nyumbani-messaging/docs') }}">Queue SMS through Nyumbani Connect with handlers, scopes, rate limiting and retries.</x-docs::card>
            <x-docs::card title="DtTables" href="{{ url('nyumbani-dt-tables/docs') }}">Searchable, sortable tables: inline, server-side and API, with a Tailwind theme.</x-docs::card>
        </div>
    </x-docs::section>

    <x-docs::section id="about-docs" title="About these docs">
        <p>These pages are served by the package itself at <code>/{{ $docs->path() }}</code>, and only in the <code>local</code> environment: anywhere else the address answers 404. Every example here is meant to be copied. A <em>Terminal</em> box is a command, a file name above a box says where the code lives, and a boxed note marks a pitfall.</p>
    </x-docs::section>
@endsection
