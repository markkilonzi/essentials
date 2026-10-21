@extends('docs::layout')

@section('page', 'Installation')
@section('lead', 'Add the package to an app in five short steps.')

@section('content')
    <x-docs::section id="before" title="Before you start">
        <x-docs::table :columns="['You need', 'Why']" :rows="[
            ['PHP 8.3 or newer, Laravel 12 or 13', 'What the package supports.'],
            ['Livewire 4', 'Every Nyumbani app uses it. Essentials requires it, and it starts Alpine, which the modal and shell rely on.'],
            ['Tailwind CSS 4 and Vite', 'The theme is a Tailwind 4 file, and the layouts load resources/css/app.css and resources/js/app.js with @vite.'],
            ['A User model and a users table', 'Fortify signs these in. The model implements HasHomeRoute, see Authentication.'],
            ['An Organization model', 'Only if your app is multi-tenant. Some traits and the log context look for it.'],
        ]" />
    </x-docs::section>

    <x-docs::section id="require" title="1. Require the package">
        <p>Essentials is a private package. Add it to the app with Composer. It brings Fortify, Spatie permissions, activity log and impersonation with it, the two other Nyumbani packages, <code>nyumbani/daraja</code> and <code>nyumbani/messaging</code>, so there is nothing more to require for M-Pesa or SMS, and <code>nyumbani/docs</code>, which serves the docs you are reading.</p>
        <x-docs::code lang="bash" title="Terminal">
            composer require nyumbani/essentials
        </x-docs::code>
        <p>Laravel discovers the service providers on its own, so there is nothing to register. Daraja and Messaging come with it, but Laravel Boost loads AI skills only from packages your app lists itself, so step 5 also adds them to your <code>composer.json</code>. The docs package needs no step: it brings its own styles and scripts, and serves them only in the local environment.</p>
    </x-docs::section>

    <x-docs::section id="theme" title="2. Import the theme">
        <p>Import the shared theme once in the app's Tailwind file. It sets the colours, form styles and the button classes, and tells Tailwind to scan the package's views, its view components and its scripts for classes, so the modal, the loader and the submit spinner are styled without any setting of yours. Classes your app writes in PHP are not scanned by the theme, so add a <code>@@source</code> line for each place: your enums, your data tables and the data tables config.</p>
        <x-docs::code lang="css" title="resources/css/app.css">
            @import 'tailwindcss';
            @import '../../vendor/nyumbani/essentials/resources/css/theme.css';

            @source '../../app/Enums/*.php';
            @source '../../app/DataTables/**/*.php';
            @source '../../config/dt-tables.php';
        </x-docs::code>
        <p><code>php artisan nyumbani:install</code> adds these three lines for you, as it does the import. Once <code>vite.config.js</code> has the shared aliases, it writes them as <code>@@vendor/nyumbani/essentials/resources/css/theme.css</code>, <code>@@enums/*.php</code> and so on, and moves the relative paths of an earlier run to the aliases. The relative paths shown here work in any app.</p>
    </x-docs::section>

    <x-docs::section id="scripts" title="3. Import the scripts">
        <p>Import one file. It holds every Nyumbani script together with the packages they need, so there is nothing to install with npm. Prefer to take only some of them? Choose the second tab: the scripts carry what they need, so there is still nothing to install.</p>
        <x-docs::tabs :labels="['Everything (recommended)', 'Some scripts']">
            <x-docs::tab label="Everything (recommended)">
                <x-docs::code lang="js" title="resources/js/app.js">
                    import '../../vendor/nyumbani/essentials/resources/dist/essentials.js';
                </x-docs::code>
            </x-docs::tab>
            <x-docs::tab label="Some scripts">
                <x-docs::code lang="js" title="resources/js/app.js">
                    import '../../vendor/nyumbani/essentials/resources/dist/toastr.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/shell.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/submit.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/file-input.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/modal.js';
                    import '../../vendor/nyumbani/essentials/resources/dist/helpers.js';
                </x-docs::code>
            </x-docs::tab>
        </x-docs::tabs>
        <x-docs::table :columns="['Included', 'What it does']" :rows="[
            ['toasts', 'The alert toasts, with toastr and jQuery bundled in.'],
            ['shell', 'The sidebar, drawer and user menu.'],
            ['submit', 'The double-submit guard.'],
            ['file-input', 'The file input component.'],
            ['modal', 'The modal, with Alpine UI bundled in.'],
            ['helpers', 'money(), numeric(), prettify(), pluralize() and the date helpers.'],
        ]" />
        <p>Livewire must start in the same file, because it starts the Alpine the modal, the shell and the docs examples run on. Import the bundle first, then start Livewire. <code>php artisan nyumbani:install</code> adds all of this for you, and leaves a file that already starts Livewire alone.</p>
        <x-docs::code lang="js" title="resources/js/app.js">
            import '../../vendor/nyumbani/essentials/resources/dist/essentials.js';
            import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

            //  Start Livewire, which starts the Alpine it bundles
            Livewire.start();
        </x-docs::code>
        <x-docs::callout title="Alpine is not bundled">
            Every Nyumbani app uses Livewire, which already ships and starts Alpine with its own plugins (focus, collapse, intersect, persist, mask, anchor, sort). Essentials reuses them and bundles only Alpine UI, which the modal needs. Import the file before <code>Livewire.start()</code>. Without <code>Livewire.start()</code> Alpine never starts, and the modals, tabs and playgrounds do nothing.
        </x-docs::callout>
        <p>To add an Alpine plugin of your own, register it on <code>alpine:init</code>. Skip the ones Livewire already includes.</p>
        <x-docs::code lang="js" title="resources/js/app.js">
            import mask from '@alpinejs/mask';

            document.addEventListener('alpine:init', () => window.Alpine.plugin(mask));
        </x-docs::code>
        <x-docs::callout title="Build after adding classes">
            Run <code>npm run build</code> whenever a package view uses a Tailwind class the app has not compiled yet.
        </x-docs::callout>
    </x-docs::section>

    <x-docs::section id="layout" title="4. Use a layout">
        <p>Extend the portal layout and name the view that holds your menu. Keep it in one app layout, so you do not repeat it on every page. A portal page with no sidebar throws an error that says how to define one.</p>
        <x-docs::code lang="blade" title="resources/views/layouts/app.blade.php">
            @verbatim
            @extends('essentials::layouts.portal')

            @section('sidebar', 'partials.sidebar')
            @endverbatim
        </x-docs::code>
    </x-docs::section>

    <x-docs::section id="check" title="Check that it works">
        <ol>
            <li>Run <code>npm run build</code> so Vite compiles the theme and scripts.</li>
            <li>Open <code>/login</code>. You should see the Nyumbani sign-in page with the app name.</li>
            <li>Open <code>/{{ $docs->path() }}</code> locally. The docs you are reading appear.</li>
            <li>Visit a page that does not exist. You should get the friendly 404 with the logo.</li>
        </ol>
        <p>Something missing? The sign-in page unstyled means the theme import is missing or <code>npm run build</code> has not run. A page full of unstyled toasts means the scripts were not imported, and modals or tabs that do nothing mean Livewire was not started in <code>resources/js/app.js</code>.</p>
        <p>Next, build a page with the <a href="{{ $docs->url('quick-start') }}">Quick start</a>.</p>
    </x-docs::section>

    <x-docs::section id="publish" title="5. Run the installer">
        <p>One command sets the app up, and it is safe to run again whenever a package changes. If Laravel Boost is missing, it installs it first with <code>composer require laravel/boost --dev</code>, so there is nothing to install by hand.</p>
        <x-docs::code lang="bash" title="Terminal">
            php artisan nyumbani:install
            php artisan migrate
        </x-docs::code>
        <p>The installer does the following, and each step only adds what is missing:</p>
        <x-docs::table :columns="['Step', 'What it does', 'On a rerun']" :rows="[
            ['Rules', 'Copies the Nyumbani rules into .ai/rules and rebuilds the index.', 'Replaces them with the newest, and removes rules the package no longer ships.'],
            ['Config files', 'Publishes config/essentials.php, daraja.php, messaging.php, permission.php, activitylog.php and dt-tables.php.', 'Skips any that exist, so your edits stay.'],
            ['Migrations', 'Publishes one migration that creates the permission tables with the group column built in, and the activity log migration. An app that already has Spatie\'s permission migration without the column gets a small one that adds it.', 'Skips any the app already has.'],
            ['Log channel', 'Adds the discord channel to config/logging.php, with the driver\'s defaults for level, throttle and message length. You still add discord to the log stack.', 'Skips it if the app has a discord channel, or if the file has no channels list.'],
            ['.env.example', 'Adds the keys the packages read, blank.', 'Adds only the keys that are missing. Your .env is never touched.'],
            ['Vite aliases', 'Adds @node-modules, @public, @resources, @vendor, @storage, @enums, @data-tables and @config to resolve.alias in vite.config.js, and keeps the file only if Node accepts it.', 'Adds only the aliases that are missing. Leaves a config with its own resolve setting, or with no defineConfig, as it is and tells you what to add.'],
            ['Imports', 'Adds the theme and the @source lines for enums, data tables and their config to resources/css/app.css, and the scripts to resources/js/app.js, and starts Livewire there (which starts the Alpine it bundles). Uses the @vendor, @enums, @data-tables and @config aliases when vite.config.js has them, and the relative paths when it does not.', 'Adds a line only if it is not there, does not start Livewire twice, and moves an earlier relative path to its alias.'],
            ['permissions.json', 'Creates a starter database/seeders/data/permissions.json.', 'Skips it if it exists.'],
            ['Roles and permissions', 'Runs php artisan nyumbani:permissions, which syncs the roles and permissions from database/seeders/data/permissions.json. It needs the permission tables, so on a first install it says to run php artisan migrate and then run the installer again.', 'Runs every time the tables exist, which is safe: it only creates what is missing, gives new permissions to users who hold the role and deletes the ones removed from the file.'],
            ['Storage link', 'Links public/storage for uploads.', 'Skips it if the link exists.'],
            ['Dependencies', 'Installs laravel/boost as a dev dependency if it is missing, and requires nyumbani/daraja and nyumbani/messaging in your composer.json if you only have them through Essentials. Boost loads skills and guidelines only from packages your app requires itself.', 'Skips what is already there.'],
            ['Boost', 'The first time, runs boost:install with its own questions. Adds Essentials, Daraja and Messaging, and their skills, to boost.json, uses Claude Code as the agent when none is chosen, and refreshes the guidelines and skills.', 'Only refreshes.'],
        ]" />
        <p>With <code>--no-interaction</code> (what Composer runs) it never asks: Boost's first install turns guidelines, skills and MCP all on.</p>

        <h3>The checklist</h3>
        <p>The installer never edits your application code. When it finishes it prints what it found and what is left for you:</p>
        <x-docs::code lang="text" title="Setup checklist">
            Theme imported in resources/css/app.css ............ done
            Vite aliases in vite.config.js ..................... done
            Enums, data tables and their config scanned ........ done
            Scripts imported in resources/js/app.js ............ done
            Livewire started in resources/js/app.js ............ done
            User model implements HasHomeRoute ................. done
            User model has roles and default permissions ....... to do
                Use Nyumbani\Essentials\Traits\HasDefaultPermissions on the user model.
            Migrations have run (permissions with group, activity_log) .. to do
                Run php artisan migrate.
            Laravel Boost is installed ......................... done
            Boost is set up with the Nyumbani packages ......... done
            discord log channel ................................ optional
            Daraja credentials set ............................. optional
            Nyumbani Connect settings set ...................... optional
        </x-docs::code>
        <p><strong>to do</strong> items are needed for Essentials to work. <strong>optional</strong> items matter only if you use that feature: the Discord log channel, Daraja credentials and the Nyumbani Connect settings. Fix an item and run the command again to see it turn to <strong>done</strong>. The command then ends with a short invitation that links to this Quick start and to the docs of each package. Read <a href="{{ $docs->url('rules-and-skills') }}">Rules and skills</a> for what lands where.</p>
    </x-docs::section>
@endsection
