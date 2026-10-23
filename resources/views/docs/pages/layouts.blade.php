@extends('docs::layout')

@section('page', 'Layouts')
@section('lead', 'The shell, the sign-in screens and the document every Nyumbani page extends.')

@section('content')
    <x-docs::section id="layouts" title="The layouts">
        <x-docs::table :columns="['Layout', 'Use for', 'Sections']" :rows="[
            ['essentials::layouts.portal', 'Signed-in pages with a sidebar and navbar', 'title, content, banner, sidebar, brand, navbar, user-menu, actions (stack)'],
            ['essentials::layouts.auth', 'Authentication pages only: sign-in, forgot password and reset password', 'heading, description, content'],
            ['essentials::layouts.error', 'Error pages', 'code, heading, detail, icon, actions'],
            ['essentials::layouts.app', 'The base document the others extend', 'title, body, styles and js (stacks)'],
        ]" />
    </x-docs::section>

    <x-docs::section id="gallery" title="Extend each layout">
        <p>Every Nyumbani page extends one of four layouts. Pick the tab for the kind of page you are building: it shows what the layout is for and the code of a page that extends it.</p>
        <x-docs::tabs :labels="['Portal', 'Auth', 'Error', 'App']">
            <x-docs::tab label="Portal">
                <p>For every signed-in page: the sidebar, the navbar, the page header and the content. A guest who reaches it is sent to <code>login</code>. The sidebar is the same on every page, so define it once and let your pages extend an app layout, in three small files. <code>php artisan nyumbani:install --portal</code> publishes all three for you, plus a dashboard page, and its sidebar already links to the docs of the Nyumbani packages.</p>

                <h3>1. The app layout</h3>
                <p>Extend the portal once, and name the view that holds your menu in its <code>sidebar</code> section. Every page extends this file. The portal renders the view for you.</p>
                <x-docs::code lang="blade" title="resources/views/layouts/app.blade.php">
                    @verbatim
                    @extends('essentials::layouts.portal')

                    @section('sidebar', 'partials.sidebar')

                    {{-- Just an example: anything pushed to the js stack is printed after the page --}}
                    @push('js')
                        <script>console.log('Runs on every page');</script>
                    @endpush
                    @endverbatim
                </x-docs::code>
                <x-docs::callout type="warning" title="The sidebar is required">
                    If the <code>sidebar</code> section is missing, the portal throws a <code>LogicException</code>: <em>The portal layout needs a sidebar. Fill its 'sidebar' section, for example @@section('sidebar', 'partials.sidebar') in your app layout.</em> A name that is not a view, such as a typo, throws <em>The sidebar view [partials.sidbar] does not exist.</em> Markup in the section still works if you prefer to write the menu inline.
                </x-docs::callout>

                <h3>2. The sidebar</h3>
                <p>Build the menu with the navigation components. This is the only place the menu lives.</p>
                <x-docs::code lang="blade" title="resources/views/partials/sidebar.blade.php">
                    @verbatim
                    <x-essentials::nav-link href="{{ user()->homeRoute() }}" label="Dashboard" :active="request()->url() === user()->homeRoute()">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3" />
                    </x-essentials::nav-link>

                    <x-essentials::nav-group label="Billing" :active="request()->routeIs('invoices.*')">
                        <x-slot:icon>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5" />
                        </x-slot:icon>

                        <x-essentials::nav-link href="{{ route('invoices.index') }}" label="Invoices" :active="request()->routeIs('invoices.*')" sub />
                    </x-essentials::nav-group>
                    @endverbatim
                </x-docs::code>

                <h3>3. A page</h3>
                <p>A page extends your app layout, not the portal, and fills only what is its own. The sidebar comes with the layout.</p>
                <x-docs::code lang="blade" title="resources/views/customers/index.blade.php">
                    @verbatim
                    @extends('layouts.app')

                    @section('title', 'Customers')

                    @section('banner')
                        <div class="mb-1.5 rounded-md bg-primary/10 p-3 text-sm">Your trial ends in 3 days.</div>
                    @endsection

                    @push('actions')
                        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">New customer</a>
                    @endpush

                    @section('content')
                        ...
                    @endsection
                    @endverbatim
                </x-docs::code>
                <p>The portal has three more optional sections, <code>brand</code> (the name beside the menu button), <code>navbar</code> (extras before the user menu) and <code>user-menu</code> (links above Logout). Put them in the app layout too when they are the same everywhere.</p>
            </x-docs::tab>

            <x-docs::tab label="Auth">
                <p>For authentication pages only: sign-in, forgot password and reset password. It is a branded panel beside a card, and Essentials already ships those three pages on it, so most apps never extend it. It is not a general layout for public or marketing pages; use the App layout for those. Fill the heading, the description and the content.</p>
                <x-docs::code lang="blade" title="resources/views/auth/welcome.blade.php">
                    @verbatim
                    @extends('essentials::layouts.auth')

                    @section('title', 'Welcome back')
                    @section('heading', 'Welcome back')
                    @section('description', 'Sign in to continue to your account')

                    @section('content')
                        <form method="POST" action="{{ route('welcome.store') }}">
                            @csrf

                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" placeholder="e.g. jane@example.com">

                            <button type="submit" class="btn btn-primary btn-lg w-full uppercase tracking-wide">Continue</button>
                        </form>
                    @endsection
                    @endverbatim
                </x-docs::code>
            </x-docs::tab>

            <x-docs::tab label="Error">
                <p>For error pages: the big code, an icon, a heading, a line of detail and your own buttons next to <em>Take me home</em> and <em>Go back</em>. This is the shipped 404 page, whose file is the whole example.</p>
                <x-docs::code lang="blade" title="resources/views/errors/404.blade.php">
                    {!! file_get_contents(essentials_path('resources/views/errors/404.blade.php')) !!}
                </x-docs::code>
            </x-docs::tab>

            <x-docs::tab label="App">
                <p>The base document the others extend: head, favicons, your CSS and JS, the toasts and two stacks. Use it directly for a page that is none of the above.</p>
                <x-docs::code lang="blade" title="resources/views/landing.blade.php">
                    @verbatim
                    @extends('essentials::layouts.app')

                    @section('title', 'A bare page')
                    @section('body-class', 'h-full bg-gray-100 text-gray-700 antialiased')

                    @section('body')
                        <main class="mx-auto max-w-xl p-10 text-center">
                            <h1 class="text-2xl font-semibold">A bare page</h1>
                        </main>
                    @endsection

                    {{-- Just an example: anything pushed to the js stack is printed after the page --}}
                    @push('js')
                        <script>console.log('Runs after the page, from the js stack');</script>
                    @endpush
                    @endverbatim
                </x-docs::code>
                <x-docs::table :columns="['Slot', 'Use']" :rows="[
                    ['title', 'The browser tab: Title - App name.'],
                    ['body, body-class', 'The page, and the classes on the body.'],
                    ['styles (stack)', 'Extra stylesheets in the head, with @@push.'],
                    ['js (stack)', 'Scripts after the page, with @@push.'],
                ]" />
            </x-docs::tab>
        </x-docs::tabs>
    </x-docs::section>

    <x-docs::section id="portal" title="A portal page">
        <x-docs::code lang="blade" title="resources/views/invoices/index.blade.php">
            @verbatim
            @extends('layouts.app')

            @section('title', 'Invoices')

            @push('actions')
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">New invoice</a>
            @endpush

            @section('content')
                ...
            @endsection
            @endverbatim
        </x-docs::code>
    </x-docs::section>

    <x-docs::section id="sidebar" title="The sidebar">
        <p>Build the menu with the navigation components and place it in the <code>sidebar</code> section. Every top-level link and group takes an icon.</p>
        <x-docs::code lang="blade" title="resources/views/partials/sidebar.blade.php">
            @verbatim
            <x-essentials::nav-link href="{{ user()->homeRoute() }}" label="Dashboard" :active="request()->url() === user()->homeRoute()">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </x-essentials::nav-link>

            <x-essentials::nav-group label="Billing" :active="request()->routeIs('invoices.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </x-slot:icon>

                <x-essentials::nav-link href="{{ route('invoices.index') }}" label="Invoices" :active="request()->routeIs('invoices.*')" sub />
            </x-essentials::nav-group>
            @endverbatim
        </x-docs::code>

        <h3>Add an icon</h3>
        <p>An icon is the inside of an SVG: the <code>&lt;path&gt;</code> elements, without the <code>&lt;svg&gt;</code> around them. The component draws the <code>&lt;svg&gt;</code> for you, 24 by 24, as an outline with a stroke of 2, in the colour of the link. To add one:</p>
        <ol>
            <li>Pick an icon from the <strong>Outline</strong> set of <a href="https://heroicons.com" target="_blank" rel="noopener">Heroicons</a>, which is free to use. Any 24 by 24 outline icon works.</li>
            <li>Choose <strong>Copy SVG</strong> and keep only the <code>&lt;path ... /&gt;</code> lines inside it.</li>
            <li>Paste them inside the link, or inside the <code>icon</code> slot of a group, as above.</li>
        </ol>
        <p>A link inside a group (<code>sub</code>) has no icon, and a link with an empty slot shows only its label. Keep one icon per top-level item, so the sidebar still reads when it is collapsed to icons.</p>
        <p>Paths to start from:</p>
        <x-docs::tabs :labels="['Users', 'Money', 'Reports', 'Settings']">
            <x-docs::tab label="Users">
                <x-docs::code lang="blade" title="Users">
                    @verbatim
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    @endverbatim
                </x-docs::code>
            </x-docs::tab>
            <x-docs::tab label="Money">
                <x-docs::code lang="blade" title="Money">
                    @verbatim
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    @endverbatim
                </x-docs::code>
            </x-docs::tab>
            <x-docs::tab label="Reports">
                <x-docs::code lang="blade" title="Reports">
                    @verbatim
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    @endverbatim
                </x-docs::code>
            </x-docs::tab>
            <x-docs::tab label="Settings">
                <x-docs::code lang="blade" title="Settings">
                    @verbatim
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    @endverbatim
                </x-docs::code>
            </x-docs::tab>
        </x-docs::tabs>
        <p>Make the user model implement <code>HasHomeRoute</code> so the logo and the sign-in redirect know where each user lands, including a user with no access.</p>
        <x-docs::code>
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Nyumbani\Essentials\Contracts\HasHomeRoute;

            class User extends Authenticatable implements HasHomeRoute
            {
                public function homeRoute(): string
                {
                    return route('dashboard');
                }
            }
        </x-docs::code>
    </x-docs::section>

    <x-docs::section id="sections" title="Every section of the portal layout">
        <x-docs::table :columns="['Section', 'Where it shows', 'Notes']" :rows="[
            ['title', 'The page header and the browser tab', 'Required. The tab reads Title - App name.'],
            ['content', 'The page body, inside the card every portal page sits in', 'Always called content. The layout draws the card (light grey with a soft shadow), so do not add an outer card of your own.'],
            ['sidebar', 'The left navigation', 'Fill it with nav-link and nav-group.'],
            ['banner', 'Above the content', 'Optional notices, such as a trial warning.'],
            ['brand', 'The navbar, beside the menu button', 'Defaults to the app name.'],
            ['navbar', 'The navbar, before the user menu', 'Optional extras such as a search box.'],
            ['user-menu', 'The user dropdown, above Logout', 'Optional links such as Profile.'],
            ['actions (stack)', 'The right of the page header', 'Use @push(\'actions\') for page buttons.'],
        ]" />
        <p>The portal layout needs a signed-in user: a guest is redirected to <code>login</code>, or gets a 401 when the request expects JSON. The sidebar shows the user's <code>avatar</code> (from <code>HasProfileImage</code>), or a generated one.</p>
        <p>While a user is impersonating another, a bar under the page header says so and offers <strong>Leave</strong>. It shows when the user model has <code>isImpersonated()</code>, which the impersonation package adds. See <a href="{{ $docs->url('authentication') }}#impersonation">Authentication</a>.</p>
    </x-docs::section>

    <x-docs::section id="branding" title="Favicons and manifest">
        <p>The base layout links the shared favicons (the dark Nyumbani logo on an amber tile, so they show on light and dark tabs), the Apple touch icon, a web manifest named after your app and a theme colour. They are served by the package, so no app copies icon files. Change the colour with <code>ESSENTIALS_THEME_COLOR</code>.</p>
            <x-docs::example title="The shared images, served by the package">
            <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3 md:grid-cols-4">
                @foreach (['icon-primary.png', 'icon-secondary.png', 'loader.svg', 'auth-background.jpg', 'shell-header.jpeg', 'shell-sidebar.jpeg'] as $image)
                    <figure class="rounded-md border border-gray-200 p-2 text-center">
                        <div class="flex h-20 items-center justify-center bg-gray-100"><img src="{{ route('essentials.image', ['image' => $image]) }}" alt="{{ $image }}" class="max-h-20 max-w-full object-contain"></div>
                        <figcaption class="mt-1 break-all text-[11px] text-gray-500">{{ $image }}</figcaption>
                    </figure>
                @endforeach
                @foreach (['favicon-32x32.png', 'apple-touch-icon.png', 'android-chrome-192x192.png'] as $image)
                    <figure class="rounded-md border border-gray-200 p-2 text-center">
                        <div class="flex h-20 items-center justify-center bg-gray-100"><img src="{{ route('essentials.favicon', ['image' => $image]) }}" alt="{{ $image }}" class="max-h-20 max-w-full object-contain"></div>
                        <figcaption class="mt-1 break-all text-[11px] text-gray-500">favicons/{{ $image }}</figcaption>
                    </figure>
                @endforeach
            </div>
            <p class="mt-3 text-sm text-gray-500">Use one with <code>route('essentials.image', ['image' => 'icon-secondary.png'])</code>, or a favicon with <code>route('essentials.favicon', ['image' => 'favicon-32x32.png'])</code>. Apps need no copies.</p>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="rules" title="View rules">
        <ul>
            <li>No <code>@@php</code> in views. Prepare data in a controller or a component class.</li>
            <li>Import classes with <code>@@use</code> at the top of the view.</li>
            <li>Use one app layout that holds the sidebar include, and extend that.</li>
        </ul>
    </x-docs::section>
@endsection
