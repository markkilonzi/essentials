@extends('essentials::docs.layout')

@section('page', 'Layouts')
@section('lead', 'The shell, the sign-in screens and the document every Nyumbani page extends.')

@section('content')
    <x-essentials::docs.section id="layouts" title="The layouts">
        <x-essentials::docs.table :columns="['Layout', 'Use for', 'Sections']" :rows="[
            ['essentials::layouts.portal', 'Signed-in pages with a sidebar and navbar', 'title, content, banner, sidebar, brand, navbar, user-menu, actions (stack)'],
            ['essentials::layouts.guest', 'Sign-in, reset and other public pages', 'heading, description, content'],
            ['essentials::layouts.error', 'Error pages', 'code, heading, detail, icon, actions'],
            ['essentials::layouts.app', 'The base document the others extend', 'title, body, styles and js (stacks)'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="portal" title="A portal page">
        <x-essentials::docs.code lang="blade" title="resources/views/invoices/index.blade.php">
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
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="sidebar" title="The sidebar">
        <p>Build the menu with the navigation components and place it in the <code>sidebar</code> section.</p>
        <x-essentials::docs.code lang="blade" title="resources/views/partials/sidebar.blade.php">
            @verbatim
            <x-essentials::nav-link href="{{ route('home') }}" label="Dashboard" :active="request()->routeIs('home')" />

            <x-essentials::nav-group label="Billing" :active="request()->routeIs('invoices.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5" />
                </x-slot:icon>

                <x-essentials::nav-link href="{{ route('invoices.index') }}" label="Invoices" :active="request()->routeIs('invoices.*')" sub />
            </x-essentials::nav-group>
            @endverbatim
        </x-essentials::docs.code>
        <p>Make the user model implement <code>HasHomeRoute</code> so the logo and the sign-in redirect know where each user lands, including a user with no access.</p>
        <x-essentials::docs.code>
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Nyumbani\Essentials\Contracts\HasHomeRoute;

            class User extends Authenticatable implements HasHomeRoute
            {
                public function homeRoute(): string
                {
                    return route('home');
                }
            }
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="sections" title="Every section of the portal layout">
        <x-essentials::docs.table :columns="['Section', 'Where it shows', 'Notes']" :rows="[
            ['title', 'The page header and the browser tab', 'Required. The tab reads Title - App name.'],
            ['content', 'The page body', 'Always called content.'],
            ['sidebar', 'The left navigation', 'Fill it with nav-link and nav-group.'],
            ['banner', 'Above the content', 'Optional notices, such as a trial warning.'],
            ['brand', 'The navbar, beside the menu button', 'Defaults to the app name.'],
            ['navbar', 'The navbar, before the user menu', 'Optional extras such as a search box.'],
            ['user-menu', 'The user dropdown, above Logout', 'Optional links such as Profile.'],
            ['actions (stack)', 'The right of the page header', 'Use @push(\'actions\') for page buttons.'],
        ]" />
        <p>The portal layout needs a signed-in user: a guest is redirected to <code>login</code>, or gets a 401 when the request expects JSON. The sidebar shows the user's <code>avatar</code> (from <code>HasProfileImage</code>), or a generated one.</p>
        <p>While a user is impersonating another, a bar under the page header says so and offers <strong>Leave</strong>. It shows when the user model has <code>isImpersonated()</code>, which the impersonation package adds. See <a href="{{ $docs->url('authentication') }}#impersonation">Authentication</a>.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="branding" title="Favicons and manifest">
        <p>The base layout links the shared house-in-hand favicons, the Apple touch icon, a web manifest named after your app and a theme colour. They are served by the package, so no app copies icon files. Change the colour with <code>ESSENTIALS_THEME_COLOR</code>.</p>
            <x-essentials::docs.example title="The shared images, served by the package">
            <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3 md:grid-cols-4">
                @foreach (['icon-primary.png', 'icon-secondary.png', 'loader.svg', 'favicon-32x32.png', 'apple-touch-icon.png', 'android-chrome-192x192.png', 'auth-background.jpg', 'shell-header.jpeg', 'shell-sidebar.jpeg'] as $image)
                    <figure class="rounded-md border border-gray-200 p-2 text-center">
                        <div class="flex h-20 items-center justify-center bg-gray-100"><img src="{{ route('essentials.image', ['image' => $image]) }}" alt="{{ $image }}" class="max-h-20 max-w-full object-contain"></div>
                        <figcaption class="mt-1 break-all text-[11px] text-gray-500">{{ $image }}</figcaption>
                    </figure>
                @endforeach
            </div>
            <p class="mt-3 text-sm text-gray-500">Use one with <code>route('essentials.image', ['image' => 'icon-secondary.png'])</code>. Apps need no copies.</p>
        </x-essentials::docs.example>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="rules" title="View rules">
        <ul>
            <li>No <code>@@php</code> in views. Prepare data in a controller or a component class.</li>
            <li>Import classes with <code>@@use</code> at the top of the view.</li>
            <li>Use one app layout that holds the sidebar include, and extend that.</li>
        </ul>
    </x-essentials::docs.section>
@endsection
