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
        <p>You do not run Boost yourself. Choosing <strong>Laravel Boost</strong> in <code>nyumbani:install</code> runs <code>boost:install</code>, adds <code>nyumbani/essentials</code> to <code>boost.json</code>, and runs <code>boost:update</code>. With <code>--sync</code> it only refreshes an installed Boost, and never installs it.</p>
        <x-essentials::docs.code lang="bash" title="Terminal">
            php artisan nyumbani:install --sync   # refresh the rules, guidelines and skills
        </x-essentials::docs.code>
        <x-essentials::docs.table :columns="['Skill', 'Covers']" :rows="[
            ['nyumbani-modal', 'The modal component and its options.'],
            ['nyumbani-error-pages', 'The shipped error pages and how to change them.'],
            ['nyumbani-branding', 'Layouts, favicons, manifest and theme colour.'],
            ['nyumbani-ui-theme', 'Buttons, toasts and the loader.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="own" title="Your own project skills">
        <p>Project-specific skills go in <code>.ai/skills</code>, not <code>.claude/skills</code>. Anything that should bind every Nyumbani app belongs in the package so every app inherits it.</p>
    </x-essentials::docs.section>
@endsection
