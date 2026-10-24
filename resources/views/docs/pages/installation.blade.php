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
        <p>Essentials is a private package. Add it to the app with Composer. It brings Fortify, Spatie permissions, activity log and impersonation with it, and <code>nyumbani/docs</code>, which serves the docs you are reading. The other Nyumbani packages, <code>nyumbani/daraja</code> (M-Pesa), <code>nyumbani/messaging</code> (SMS through Connect) and <code>nyumbani/dt-tables</code> (data tables), are optional: the installer asks which ones the app uses and requires only those.</p>
        <x-docs::code lang="bash" title="Terminal">
            composer require nyumbani/essentials
        </x-docs::code>
        <p>Laravel discovers the service providers on its own, so there is nothing to register. Laravel Boost loads AI skills only from packages your app lists itself, so step 5 requires the optional packages you choose in your own <code>composer.json</code>. The docs package needs no step: it brings its own styles and scripts, and serves them only in the local environment.</p>
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
        <p>Extend the portal layout and name the view that holds your menu. Keep it in one app layout, so you do not repeat it on every page. A portal page with no sidebar throws an error that says how to define one. <code>php artisan nyumbani:install --portal</code> publishes this layout, a sidebar (<code>admins.partials.sidebar</code>) and a dashboard page for you, so you can skip writing them.</p>
        <x-docs::code lang="blade" title="resources/views/layouts/app.blade.php">
            @verbatim
            @extends('essentials::layouts.portal')

            @section('sidebar', 'partials.sidebar')
            @endverbatim
        </x-docs::code>
    </x-docs::section>

    <x-docs::section id="check" title="Check that it works">
        <ol>
            <li>The installer already ran <code>npm run build</code> when the app has <code>node_modules</code>. If it did not (or after you add classes), run <code>npm install</code> and <code>npm run build</code> so Vite compiles the theme and scripts.</li>
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
        <p>On an interactive run it first shows a checkbox, <strong>Which Nyumbani packages does this app use?</strong>, with Daraja, Messaging and Data tables, the ones the app already requires ticked. It then asks two optional questions, both defaulting to no: <strong>Set up a basic portal?</strong> and <strong>Run the migrations now?</strong>. Scripts skip the checkbox with <code>--with=daraja,dt-tables</code> or <code>--without=messaging</code>; a run with nobody to ask (Composer's) manages only the optional packages the app already requires and never installs a new one. The installer does the following, in this order, and each step only adds what is missing:</p>
        <x-docs::table :columns="['Step', 'What it does', 'On a rerun']" :rows="[
            ['Dependencies', 'Installs laravel/boost as a dev dependency if it is missing, and requires the optional packages you chose (nyumbani/daraja, nyumbani/messaging, nyumbani/dt-tables) in your composer.json when it does not already. Boost loads skills and guidelines only from packages your app requires itself.', 'Skips what is already there. A package the app already requires and you leave out is kept unless you confirm its removal (default no) or pass --remove, which runs composer remove, drops it and its skills from boost.json and leaves your config file for you to delete.'],
            ['Rules', 'Copies the Nyumbani rules into .ai/rules and rebuilds the index.', 'Replaces them with the newest, and removes rules the package no longer ships.'],
            ['Config files', 'Publishes config/essentials.php, permission.php and activitylog.php, plus daraja.php, messaging.php and dt-tables.php for the packages you chose. A dt-tables.php published now is set to the Tailwind theme, with btn btn-sm btn-primary buttons and the rounded-lg border px-3 py-1.5 text-sm class on inputs and selects.', 'Skips any that exist, so your edits stay, including a dt-tables.php you already changed.'],
            ['Migrations', 'Publishes one migration that creates the permission tables with the group column built in, and the activity log migration. An app that already has Spatie\'s permission migration without the column gets a small one that adds it.', 'Skips any the app already has.'],
            ['.env.example', 'Adds the keys Essentials and the packages you chose read, blank.', 'Adds only the keys that are missing. Your .env is never touched.'],
            ['Log channel', 'Adds the discord channel to config/logging.php, with the driver\'s defaults for level, throttle and message length. You still add discord to the log stack.', 'Skips it if the app has a discord channel, or if the file has no channels list.'],
            ['Vite aliases', 'Adds @node-modules, @public, @resources, @vendor, @storage, @enums, @data-tables and @config to resolve.alias in vite.config.js, and keeps the file only if Node accepts it.', 'Adds only the aliases that are missing. Leaves a config with its own resolve setting, or with no defineConfig, as it is and tells you what to add.'],
            ['Imports', 'Adds the theme and the @source lines for enums, data tables and their config to resources/css/app.css, and the scripts to resources/js/app.js, and starts Livewire there (which starts the Alpine it bundles). Uses the @vendor, @enums, @data-tables and @config aliases when vite.config.js has them, and the relative paths when it does not.', 'Adds a line only if it is not there, does not start Livewire twice, and moves an earlier relative path to its alias.'],
            ['permissions.json', 'Creates a starter database/seeders/data/permissions.json.', 'Skips it if it exists.'],
            ['Basic portal (optional)', 'Asks Set up a basic portal? and, on yes (or with php artisan nyumbani:install --portal), publishes resources/views/admins/pages/dashboard.blade.php, admins/partials/sidebar.blade.php and layouts/app.blade.php (tag essentials-portal). The sidebar holds a Dashboard link and a Docs group linking the docs of Essentials, Daraja and Messaging (local only). It then prints what is left by hand, but only while the HasHomeRoute checklist item is not done: a Route::view route for the admins.pages.dashboard view inside your auth group in routes/web.php, under any name you like, and the user model changes. The sidebar link follows whatever URL homeRoute() returns. A non-interactive run never asks.', 'Never overwrites a view the app already has.'],
            ['Build assets', 'Runs npm run build when the app has a package.json and node_modules, so the theme, scripts and any published views are compiled. A failed build is reported and does not stop the install.', 'Runs every time. Without node_modules it says to run npm install first.'],
            ['Migrations (optional)', 'Asks Run the migrations now? when the permission and activity tables are missing, defaulting to no. On yes it runs php artisan migrate, so the roles and permissions can be synced in the same run. A non-interactive run never asks.', 'Skips the question once the tables exist.'],
            ['Roles and permissions', 'Runs php artisan nyumbani:permissions, which syncs the roles and permissions from database/seeders/data/permissions.json. It needs the permission tables, so on a first install it says to run php artisan migrate and then run the installer again, unless you answered yes to the migrations question.', 'Runs every time the tables exist, which is safe: it only creates what is missing, gives new permissions to users who hold the role and deletes the ones removed from the file.'],
            ['Storage link', 'Links public/storage for uploads.', 'Skips it if the link exists.'],
            ['Pint', 'Runs vendor/bin/pint on the PHP files the installer published or edited (the config files, the migrations and config/logging.php), and only those, so your own code is never reformatted. Skipped when the app has no Pint.', 'Runs every time, and only touches files that exist.'],
            ['Boost', 'The first time, runs boost:install with its own questions. Adds Essentials and the packages you chose, and their skills, to boost.json, uses Claude Code as the agent when none is chosen, and refreshes the guidelines and skills.', 'Only refreshes.'],
        ]" />
        <p>With <code>--no-interaction</code> (what Composer runs) it never asks: Boost's first install turns guidelines, skills and MCP all on.</p>

        <h3>The checklist</h3>
        <p>The installer never edits your application code. When it finishes it prints what it found and what is left for you:</p>
        <x-docs::code lang="text" title="Setup checklist">
            Theme imported in resources/css/app.css ............ done
            Enums, data tables and their config scanned ........ done
            Vite aliases in vite.config.js ..................... done
            Scripts imported in resources/js/app.js ............ done
            Livewire started in resources/js/app.js ............ done
            User model implements HasHomeRoute ................. done
            User model has roles and default permissions ....... to do
                Use Nyumbani\Essentials\Traits\HasDefaultPermissions on the user model.
            User model can be impersonated ..................... to do
                Use Lab404\Impersonate\Models\Impersonate on the user model.
            Migrations have run (permissions with group, activity_log) .. to do
                Run php artisan migrate.
            Laravel Boost is installed ......................... done
            Boost is set up with the Nyumbani packages ......... done
            Basic portal (dashboard, layout and sidebar) ....... optional
                Run php artisan nyumbani:install --portal.
            discord log channel ................................ optional
            Daraja credentials set ............................. optional
            Nyumbani Connect settings set ...................... optional
        </x-docs::code>
        <p>With the basic portal, the <code>HasHomeRoute</code> item is one check for both halves: the user model implements <code>HasHomeRoute</code> and a GET route inside the <code>auth</code> middleware is the dashboard: it is named <code>dashboard</code> or <code>home</code> (with any prefix, such as <code>admins.dashboard</code>) or shows the <code>admins.pages.dashboard</code> view. What runs it and which routes file it is in do not matter; a route outside <code>auth</code> does not count. Its fix names only what is missing.</p>
        <p><strong>to do</strong> items are needed for Essentials to work. <strong>optional</strong> items matter only if you use that feature: the Discord log channel and, for the packages you chose, the Daraja credentials and the Nyumbani Connect settings. Fix an item and run the command again to see it turn to <strong>done</strong>. The command then ends with a short invitation that links to this Quick start and to the docs of each package. Read <a href="{{ $docs->url('rules-and-skills') }}">Rules and skills</a> for what lands where.</p>
    </x-docs::section>

    <x-docs::section id="options" title="Command options">
        <p><code>php artisan nyumbani:install</code> takes four options of its own, next to Laravel's (<code>-n</code> / <code>--no-interaction</code> never asks, <code>-q</code>, <code>-v</code>, <code>--env</code>).</p>
        <x-docs::table :columns="['Option', 'What it does', 'Example']" :rows="[
            ['--portal', 'Sets up the basic portal (dashboard, layout and sidebar) without asking the question.', 'php artisan nyumbani:install --portal'],
            ['--with=', 'Installs the named optional packages without showing the checkbox. Names are daraja, messaging and dt-tables (or the full nyumbani/daraja form), comma separated or repeated. Packages the app already requires stay chosen.', 'php artisan nyumbani:install --with=daraja,dt-tables'],
            ['--without=', 'Leaves the named optional packages out without showing the checkbox. A left-out package gets no config file, .env.example keys, rule, checklist item or Boost entry. It wins over --with and over a package the app already requires, which is kept unless you also remove it.', 'php artisan nyumbani:install --without=messaging'],
            ['--remove', 'Runs composer remove for each optional package the app requires and you left out, without asking, then drops it and its skills from boost.json and finishes in a fresh process. Your own config file is left for you to delete. Without this option, an interactive run asks once per package (default no) and a run with nobody to ask removes nothing.', 'php artisan nyumbani:install --without=messaging --remove'],
        ]" />
        <p><strong>Which packages are chosen.</strong> With neither <code>--with</code> nor <code>--without</code>, an interactive run shows the checkbox with the packages the app already requires ticked. With <code>--no-interaction</code> it chooses the packages the app already requires and never installs a new one, which is why Composer's <code>post-update-cmd</code> is safe. Either flag skips the checkbox: the chosen packages are the ones the app already requires, plus <code>--with</code>, minus <code>--without</code>.</p>
        <x-docs::code lang="bash" title="Terminal">
            # Ask which packages to install (interactive)
            php artisan nyumbani:install

            # An app that sends no SMS and takes no payments, such as Nyumbani Connect
            php artisan nyumbani:install --with=dt-tables --without=messaging,daraja

            # Stop using Messaging: confirm the removal, or pass --remove to skip the question
            php artisan nyumbani:install --without=messaging
            php artisan nyumbani:install --without=messaging --remove

            # Set everything up in a script, with no questions
            php artisan nyumbani:install --no-interaction --portal --with=daraja,messaging,dt-tables
        </x-docs::code>
        <p>When the installer has to require or remove packages it finishes in a second process, because the classes of the new packages are not loaded in the first. That process receives the choice as flags, so it never asks again.</p>
        <p><code>php artisan nyumbani:permissions</code> has no options. It syncs the roles and permissions from <code>database/seeders/data/permissions.json</code>, and the installer runs it for you once the permission tables exist.</p>
    </x-docs::section>

    <x-docs::section id="portal" title="Set up the basic portal (optional)">
        <p>The portal gives a new app a working signed-in shell in a minute: a layout, a sidebar and a dashboard page, all on the shared portal layout. It is optional and the installer asks first, defaulting to no. Do it once, in this order, and nothing is overwritten along the way.</p>

        <h3>1. Publish the views</h3>
        <x-docs::code lang="bash" title="Terminal">
            php artisan nyumbani:install --portal
        </x-docs::code>
        <p>Or run <code>php artisan nyumbani:install</code> and answer <strong>yes</strong> to <em>Set up a basic portal?</em>. It publishes these files, each only when the app does not have it yet:</p>
        <x-docs::table :columns="['File', 'What it is']" :rows="[
            ['resources/views/layouts/app.blade.php', 'Your one app layout. It extends the portal layout and points its sidebar at the sidebar below.'],
            ['resources/views/admins/partials/sidebar.blade.php', 'The menu: a Dashboard link and, locally, a Docs group linking the docs of Essentials, Daraja, Messaging and DtTables. Add your own links here.'],
            ['resources/views/admins/pages/dashboard.blade.php', 'A welcome page with quick links to those docs.'],
        ]" />
        <p>A file you already have is never replaced, so your edits are safe when you run the installer again. To take the newest version of a view, delete that one file and run the installer again. Publishing all three over your copies is <code>php artisan vendor:publish --tag=essentials-portal --force</code>, so use it only on files you have not changed.</p>

        <h3>2. Add the dashboard route</h3>
        <p>The installer edits no routes, and removes a package only when you confirm or pass <code>--remove</code> (see the Dependencies row above). Add this to the bottom of <code>routes/web.php</code>. It must sit inside the <code>auth</code> middleware, because the portal layout sends a guest to sign in. The route can have any name, as long as the user model returns its URL in step 3.</p>
        <x-docs::code title="routes/web.php">
            use Illuminate\Support\Facades\Route;

            Route::middleware('auth')->group(function () {
                Route::view('dashboard', 'admins.pages.dashboard')->name('dashboard');
            });
        </x-docs::code>

        <h3>3. Make the user model ready</h3>
        <p>The sidebar and the sign-in redirect ask the user model where to go. Add the contract and the two traits to your existing model; keep the traits it already uses, such as <code>HasFactory</code> and <code>Notifiable</code>.</p>
        <x-docs::code title="app/Models/User.php">
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Lab404\Impersonate\Models\Impersonate;
            use Nyumbani\Essentials\Contracts\HasHomeRoute;
            use Nyumbani\Essentials\Traits\HasDefaultPermissions;

            class User extends Authenticatable implements HasHomeRoute
            {
                use HasDefaultPermissions, Impersonate;

                public function homeRoute(): string
                {
                    return route('dashboard');
                }
            }
        </x-docs::code>

        <h3>4. Build, migrate and sign in</h3>
        <p>The installer already ran <code>npm run build</code> if the app has <code>node_modules</code>. Run the migrations if you answered no to that question, then create a user to sign in with. There is no registration page, so seed one and give it a role from <code>permissions.json</code> (see <a href="{{ $docs->url('permissions') }}">Permissions</a>).</p>
        <x-docs::code lang="bash" title="Terminal">
            php artisan migrate
            php artisan nyumbani:permissions
        </x-docs::code>
        <x-docs::code title="database/seeders/DatabaseSeeder.php">
            use App\Models\User;

            User::factory()
                ->create(['name' => 'Admin', 'email' => 'admin@example.com'])
                ->assignDefaultPermissions('admin');
        </x-docs::code>
        <p>Then open <code>/login</code>, sign in, and you land on <code>/dashboard</code>. Run <code>php artisan nyumbani:install</code> once more: the <em>User model implements HasHomeRoute and the dashboard has a route</em> item should read <strong>done</strong>.</p>

        <h3>If something goes wrong</h3>
        <x-docs::table :columns="['What you see', 'Why', 'What to do']" :rows="[
            ['View [admins.pages.dashboard] not found', 'The portal views were not published.', 'Run php artisan nyumbani:install --portal.'],
            ['Route [dashboard] not defined', 'The route from step 2 is missing, or homeRoute() names a route that does not exist.', 'Add the route, or return the URL of your own route from homeRoute().'],
            ['A LogicException about the sidebar', 'Your layout extends the portal layout but names no sidebar view, or names one that does not exist.', 'Set the layout sidebar section to admins.partials.sidebar, or to your own partial.'],
            ['I already had a layouts/app.blade.php', 'The installer never replaces it, so it still points at your old sidebar.', 'Point its sidebar section at admins.partials.sidebar, or copy the links you want into your own partial.'],
            ['The pages have no styling or the toasts do not appear', 'The theme or the scripts are not compiled.', 'Run npm install, then npm run build. The checklist says if the theme or script import is missing.'],
            ['The Docs group is missing from the sidebar', 'The package docs are served only in the local environment.', 'Set APP_ENV=local while developing. Nothing is wrong in other environments.'],
            ['Sign-in sends me to a page that is not the dashboard', 'A user goes to the page they were trying to reach first, then to homeRoute().', 'Check what homeRoute() returns for that user.'],
            ['The checklist still says to do after I added the route', 'The installer reads the routes loaded when it started.', 'Run php artisan nyumbani:install again.'],
        ]" />
    </x-docs::section>
@endsection
