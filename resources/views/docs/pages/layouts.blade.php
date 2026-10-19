@extends('essentials::docs.layout')

@section('page', 'Layouts')
@section('lead', 'The shell, the sign-in screens and the document every Nyumbani page extends.')

@section('content')
    <x-essentials::docs.section id="layouts" title="The layouts">
        <x-essentials::docs.table :columns="['Layout', 'Use for', 'Sections']" :rows="[
            ['essentials::layouts.portal', 'Signed-in pages with a sidebar and navbar', 'title, content, banner, sidebar, actions (stack)'],
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

    <x-essentials::docs.section id="branding" title="Favicons and manifest">
        <p>The base layout links the shared house-in-hand favicons, the Apple touch icon, a web manifest named after your app and a theme colour. They are served by the package, so no app copies icon files. Change the colour with <code>ESSENTIALS_THEME_COLOR</code>.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="rules" title="View rules">
        <ul>
            <li>No <code>@@php</code> in views. Prepare data in a controller or a component class.</li>
            <li>Import classes with <code>@@use</code> at the top of the view.</li>
            <li>Use one app layout that holds the sidebar include, and extend that.</li>
        </ul>
    </x-essentials::docs.section>
@endsection
