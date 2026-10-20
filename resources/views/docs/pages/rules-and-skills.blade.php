@extends('essentials::docs.layout')

@section('page', 'Rules and skills')
@section('lead', 'The conventions of Nyumbani, written down for people and for AI agents.')

@section('content')
    <x-essentials::docs.section id="rules" title="Rules">
        <p>Rules are short, path-scoped statements such as how controllers or views are written. The package ships them and copies them into the app.</p>
        <x-essentials::docs.code lang="bash" title="Terminal">
            php artisan nyumbani:install          # choose what to install: rules, Boost or both
            php artisan nyumbani:install --all    # install everything, without asking
            php artisan nyumbani:install --sync   # refresh what is already installed
        </x-essentials::docs.code>
        <p>They land in <code>.ai/rules/nyumbani-*.md</code>, and the index in <code>.ai/rules/index.md</code> maps each path to its rule. Do not edit them; run <code>--sync</code> to refresh. The Composer scripts of a Nyumbani app run the sync for you, and it also refreshes Boost.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="skills" title="Skills">
        <p>Skills teach an AI agent how to use a part of the package. Laravel Boost finds them in the package's <code>resources/boost/skills</code> folder.</p>
        <p>You do not run Boost yourself. Choosing <strong>Laravel Boost</strong> in <code>nyumbani:install</code> runs <code>boost:install</code>, adds <code>nyumbani/essentials</code> to <code>boost.json</code>, and runs <code>boost:update</code>. With <code>--sync</code> it only refreshes an installed Boost, and never installs it, so the same command keeps rules, guidelines and skills current.</p>
        <x-essentials::docs.table :columns="['Skill', 'Covers']" :rows="[
            ['nyumbani-modal', 'The modal component and its options.'],
            ['nyumbani-error-pages', 'The shipped error pages and how to change them.'],
            ['nyumbani-branding', 'Layouts, favicons, manifest and theme colour.'],
            ['nyumbani-ui-theme', 'Buttons, toasts and the loader.'],
            ['nyumbani-error-bags', 'Several forms on one page, the bag directive and reopening a modal after a failed save.'],
        ]" />
        <p>Other Nyumbani packages ship their own skills the same way. <code>nyumbani/daraja</code> adds <code>nyumbani-daraja-payments</code> and <code>nyumbani-daraja-webhooks</code>; <code>nyumbani/messaging</code> adds <code>nyumbani-messaging</code>. Add each package to <code>boost.json</code> and run <code>php artisan boost:update</code>. Their docs are in the switcher at the top of this page.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="read" title="Read the rules">
        <p>The rules are written out, with examples, on the pages that follow.</p>
        <ul>
            <li><a href="{{ $docs->url('code-style') }}">Code style</a>: doc blocks, comments, names, imports and tests.</li>
            <li><a href="{{ $docs->url('app-code') }}">Application code</a>: services, helpers, providers and the Laravel idioms.</li>
            <li><a href="{{ $docs->url('http-layer') }}">HTTP layer</a>: routes, controllers, Form Requests, policies and language files.</li>
            <li><a href="{{ $docs->url('data-layer') }}">Models and data</a>: models, migrations, enums, seeders and data tables.</li>
            <li><a href="{{ $docs->url('view-layer') }}">Views and scripts</a>: layouts, Blade, forms, Alpine, Livewire and design.</li>
        </ul>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="own" title="Your own project skills">
        <p>Project-specific skills go in <code>.ai/skills</code>, not <code>.claude/skills</code>. Anything that should bind every Nyumbani app belongs in the package so every app inherits it.</p>
    </x-essentials::docs.section>
@endsection
