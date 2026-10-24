@extends('docs::layout')

@section('page', 'JavaScript')
@section('lead', 'Small, dependency-free behaviours that every page gets once they are imported.')

@section('content')
    <x-docs::section id="scripts" title="What each script does">
        <x-docs::table :columns="['Script', 'Does']" :rows="[
            ['toastr', 'Shows the toasts that back()->alert() flashes.'],
            ['shell', 'Runs the sidebar: a drawer on phones, collapsible to icons on large screens, closed by a tap or Escape.'],
            ['submit', 'Stops double submission and shows a spinner on the button that was pressed.'],
            ['file-input', 'Powers the file input component with previews and a remove action.'],
            ['modal', 'Registers the Alpine UI plugin and the essentialsModal component.'],
            ['helpers', 'Adds money(), numeric(), prettify() and the date helpers to window.'],
        ]" />
    </x-docs::section>

    <x-docs::section id="bundle" title="One bundled file">
        <p>An app imports a single file, <code>vendor/nyumbani/essentials/resources/dist/essentials.js</code>. It already contains every script above together with the packages they need: toastr and jQuery, Alpine UI and pluralize. The app installs none of them with npm.</p>
        <p>The scripts under <code>resources/js</code> are the source of that file. When you change one inside the package, run <code>npm run build</code> in the package folder and commit <code>resources/dist</code> with it.</p>
    </x-docs::section>

    <x-docs::section id="submit" title="Double-submit guard">
        <p>Every submit button is guarded automatically, including forms rendered later by Alpine or a DataTable. Opt a button out when it must be pressed more than once. The button is never disabled, because a disabled button drops its <code>name</code> and <code>value</code> from the form. It shows a spinner, ignores the pointer and reads as disabled to assistive technology, while the form refuses a second submission, so a button that picks one of several options still posts its value.</p>
        <x-docs::code lang="blade">
            @verbatim
            <button type="submit" class="btn btn-primary">Save</button>

            <button type="submit" data-allow-resubmit class="btn btn-primary">Add another</button>
            @endverbatim
        </x-docs::code>
        <x-docs::callout type="tip">The guard resets when the page is restored from the browser's back button, so a returning user is not stuck with a spinning button.</x-docs::callout>
            <x-docs::example title="Live: press each button">
            <iframe name="docs-null-target" title="Ignored" class="hidden"></iframe>
            <form method="POST" action="about:blank" target="docs-null-target" class="flex flex-wrap gap-1.5" x-data x-on:submit="setTimeout(() => $el.dispatchEvent(new Event('reset-loading')), 2000)">
                <button type="submit" class="btn btn-primary">Guarded (resets after 2 seconds for this demo)</button>
            </form>
            <p class="mt-3 text-sm text-gray-500">A real page navigates away, so the button stays disabled until it does. This demo posts to a hidden frame, so it resets the button with a <code>reset-loading</code> event after two seconds.</p>
        </x-docs::example>
    </x-docs::section>
@endsection
