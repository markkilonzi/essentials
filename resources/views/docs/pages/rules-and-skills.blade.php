@extends('essentials::docs.layout')

@section('page', 'Rules and skills')
@section('lead', 'The conventions of Nyumbani, written down for people and for AI agents.')

@section('content')
    <x-essentials::docs.section id="rules" title="Rules">
        <p>Rules are short, path-scoped statements such as how controllers or views are written. The package ships them and copies them into the app.</p>
        <x-essentials::docs.code lang="bash" title="Terminal">
            php artisan nyumbani:install                    # first time, or any time a package changes
            php artisan nyumbani:install --no-interaction   # the same, never asking (what Composer runs)
        </x-essentials::docs.code>
        <p>One command does it all, and it is safe to run again (see <a href="{{ $docs->url('installation') }}#publish">Installation</a> for everything else it sets up): it always copies the current rules over the old ones, removes rules the package no longer ships, and refreshes Boost. They land in <code>.ai/rules/nyumbani-*.md</code>, and the index in <code>.ai/rules/index.md</code> maps each path to its rule. Do not edit them: the next run replaces them. The Composer <code>post-update-cmd</code> of a Nyumbani app runs it for you, so a <code>composer update</code> brings the newest rules and skills.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="skills" title="Skills">
        <p>Skills teach an AI agent how to use a part of the package. Laravel Boost finds them in the package's <code>resources/boost/skills</code> folder.</p>
        <p>You do not run Boost yourself. The first time, <code>nyumbani:install</code> runs <code>boost:install</code> and asks Boost's own questions about agents (a non-interactive run turns guidelines, skills and MCP all on). Boost reads skills only from packages your app requires itself, so the installer first makes sure <code>nyumbani/daraja</code> and <code>nyumbani/messaging</code> are in your <code>composer.json</code> next to Essentials. Every time, it adds <code>nyumbani/essentials</code>, <code>nyumbani/daraja</code> and <code>nyumbani/messaging</code> to <code>boost.json</code>, lists their skills there (<code>boost:update</code> installs only the skills named in <code>boost.json</code>), uses Claude Code as the agent when none is chosen, and runs <code>boost:update</code>, so rules, guidelines and skills stay current with one command.</p>
        <x-essentials::docs.table :columns="['Skill', 'Covers']" :rows="[
            ['nyumbani-modal', 'The modal component and its options.'],
            ['nyumbani-error-pages', 'The shipped error pages and how to change them.'],
            ['nyumbani-branding', 'Layouts, favicons, manifest and theme colour.'],
            ['nyumbani-ui-theme', 'Buttons, toasts and the loader.'],
            ['nyumbani-error-bags', 'Several forms on one page, the bag directive and reopening a modal after a failed save.'],
            ['nyumbani-logging', 'The discord log driver, its options and why records do not arrive.'],
            ['nyumbani-install', 'Running nyumbani:install, its setup checklist and how to fix each item.'],
            ['nyumbani-auth', 'Sign-in, password reset, home routes, the login throttle and impersonation.'],
            ['nyumbani-helpers', 'Formatting helpers in PHP, Blade and JavaScript, and the period filter.'],
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
