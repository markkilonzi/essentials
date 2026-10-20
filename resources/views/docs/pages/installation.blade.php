@extends('essentials::docs.layout')

@section('page', 'Installation')
@section('lead', 'Add the package to an app in five short steps.')

@section('content')
    <x-essentials::docs.section id="before" title="Before you start">
        <x-essentials::docs.table :columns="['You need', 'Why']" :rows="[
            ['PHP 8.3 or newer, Laravel 12 or 13', 'What the package supports.'],
            ['Livewire', 'Every Nyumbani app uses it. It starts Alpine, which the modal and shell rely on.'],
            ['Tailwind CSS 4 and Vite', 'The theme is a Tailwind 4 file, and the layouts load resources/css/app.css and resources/js/app.js with @vite.'],
            ['A User model and a users table', 'Fortify signs these in. The model implements HasHomeRoute, see Authentication.'],
            ['An Organization model', 'Only if your app is multi-tenant. Some traits and the log context look for it.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="require" title="1. Require the package">
        <p>Essentials is a private package. Add it to the app with Composer. It brings Fortify, Spatie permissions, activity log and impersonation with it, and the two other Nyumbani packages, <code>nyumbani/daraja</code> and <code>nyumbani/messaging</code>, so there is nothing more to require for M-Pesa or SMS.</p>
        <x-essentials::docs.code lang="bash" title="Terminal">
            composer require nyumbani/essentials
        </x-essentials::docs.code>
        <p>Laravel discovers the service providers on its own, so there is nothing to register.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="theme" title="2. Import the theme">
        <p>Import the shared theme once in the app's Tailwind file. It sets the colours, form styles and the button classes, and scans the package views for classes.</p>
        <x-essentials::docs.code lang="css" title="resources/css/app.css">
            @import 'tailwindcss';
            @import '../../vendor/nyumbani/essentials/resources/css/theme.css';
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="scripts" title="3. Import the scripts">
        <p>Import one file. It holds every Nyumbani script together with the packages they need, so there is nothing to install with npm. Prefer to take only some of them? Choose the second tab: the scripts carry what they need, so there is still nothing to install.</p>
        <x-essentials::docs.tabs :labels="['Everything (recommended)', 'Some scripts']">
            <x-essentials::docs.tab label="Everything (recommended)">
                <x-essentials::docs.code lang="js" title="resources/js/app.js">
                    import '../../vendor/nyumbani/essentials/resources/dist/essentials.js';
                </x-essentials::docs.code>
            </x-essentials::docs.tab>
            <x-essentials::docs.tab label="Some scripts">
                <x-essentials::docs.code lang="js" title="resources/js/app.js">
                    import '../../vendor/nyumbani/essentials/resources/dist/toastr.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/shell.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/submit.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/file-input.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/modal.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/helpers.js';
                </x-essentials::docs.code>
            </x-essentials::docs.tab>
        </x-essentials::docs.tabs>
        <x-essentials::docs.table :columns="['Included', 'What it does']" :rows="[
            ['toasts', 'The alert toasts, with toastr and jQuery bundled in.'],
            ['shell', 'The sidebar, drawer and user menu.'],
            ['submit', 'The double-submit guard.'],
            ['file-input', 'The file input component.'],
            ['modal', 'The modal, with Alpine UI bundled in.'],
            ['helpers', 'money(), numeric(), prettify(), pluralize() and the date helpers.'],
        ]" />
        <x-essentials::docs.callout title="Alpine is not bundled">
            Every Nyumbani app uses Livewire, which already ships and starts Alpine with its own plugins (focus, collapse, intersect, persist, mask, anchor, sort). Essentials reuses them and bundles only Alpine UI, which the modal needs. Import the file before <code>Livewire.start()</code>.
        </x-essentials::docs.callout>
        <p>To add an Alpine plugin of your own, register it on <code>alpine:init</code>. Skip the ones Livewire already includes.</p>
        <x-essentials::docs.code lang="js" title="resources/js/app.js">
            import mask from '@alpinejs/mask';

            document.addEventListener('alpine:init', () => window.Alpine.plugin(mask));
        </x-essentials::docs.code>
        <x-essentials::docs.callout title="Build after adding classes">
            Run <code>npm run build</code> whenever a package view uses a Tailwind class the app has not compiled yet.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="layout" title="4. Use a layout">
        <p>Extend the portal layout and supply a sidebar. Keep the sidebar include in one app layout, so you do not repeat it on every page.</p>
        <x-essentials::docs.code lang="blade" title="resources/views/layouts/app.blade.php">
            @verbatim
            @extends('essentials::layouts.portal')

            @section('sidebar')
                @include('partials.sidebar')
            @endsection
            @endverbatim
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="check" title="Check that it works">
        <ol>
            <li>Run <code>npm run build</code> so Vite compiles the theme and scripts.</li>
            <li>Open <code>/login</code>. You should see the Nyumbani sign-in page with the app name.</li>
            <li>Open <code>/{{ $docs->path() }}</code> locally. The docs you are reading appear.</li>
            <li>Visit a page that does not exist. You should get the friendly 404 with the logo.</li>
        </ol>
        <p>Something missing? The sign-in page unstyled means the theme import is missing or <code>npm run build</code> has not run. A page full of unstyled toasts means the scripts were not imported.</p>
        <p>Next, build a page with the <a href="{{ $docs->url('quick-start') }}">Quick start</a>.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="publish" title="5. Install the rules and Boost">
        <p>One command publishes the conventions and sets up Laravel Boost, so editors and AI agents follow them. Install Boost first with <code>composer require laravel/boost --dev</code>.</p>
        <x-essentials::docs.code lang="bash" title="Terminal">
            php artisan nyumbani:install
        </x-essentials::docs.code>
        <p>It asks what to install: the rules, Boost, or both. Boost's own questions about agents come next, and Essentials is enabled for you, so its skills and guidelines load. Read <a href="{{ $docs->url('rules-and-skills') }}">Rules and skills</a> for what lands where.</p>
    </x-essentials::docs.section>
@endsection
