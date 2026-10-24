@extends('docs::layout')

@section('page', 'Views and scripts')
@section('lead', 'Layouts, components, forms, Alpine, Livewire, design tokens and the scripts the package ships.')

@section('content')
    <x-docs::section id="structure" title="Structure and layouts">
        <x-docs::rule title="Audience-first folders">
            <p>Views are grouped by audience, <code>admins/{pages,partials}</code> and <code>system-admins/{pages,partials}</code>, with shared <code>layouts</code> and <code>components</code>. Modal components live in <code>components/{audience}/modal</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Pages extend the shared layout">
            <p>Signed-in pages extend <code>essentials::layouts.portal</code> and the authentication pages (sign-in, forgot password and reset password) extend <code>essentials::layouts.auth</code>, both built on <code>essentials::layouts.app</code>. The auth layout is for those pages only: any other public page extends <code>essentials::layouts.app</code>. A portal page fills <code>title</code>, <code>sidebar</code> and <code>content</code>, plus optional <code>brand</code>, <code>navbar</code>, <code>user-menu</code> and <code>banner</code>, and <code>@@push('actions')</code>. The content section is always called <code>content</code>. The portal draws <code>portal.content</code> when your app layout defines it, so that layout can wrap every page, for example in a card. Build the sidebar from <code>nav-link</code> and <code>nav-group</code> in the app's own partial. Routes stay protected with the <code>auth</code> middleware; the layout's redirect is only a safety net.</p>
        </x-docs::rule>
        <x-docs::rule title="Auth pages and favicons come from the package">
            <p>Login, forgot-password and reset-password are provided by Essentials, so do not build your own. The layout links the Nyumbani favicons, apple-touch icon and web manifest; add no <code>&lt;link rel="icon"&gt;</code> or favicon files. The manifest takes the app name and <code>essentials.branding.theme_color</code>.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="blade" title="Blade">
        <x-docs::rule title="No php blocks, no inline PHP">
            <p>Do not write <code>@@php</code> blocks to compute data. A component computes values in its class. A page, partial or layout gets them from the controller, a view composer registered in <code>ViewServiceProvider</code>, or the owning DataTable. Reading <code>$errors</code> or <code>old()</code> inline is fine. The one exception is a throwaway like <code>@@php($i = 0)</code> when there is truly no other place.</p>
        </x-docs::rule>
        <x-docs::rule title="Import with use">
            <p>Import every class a view uses with <code>@@use('Vendor\Class')</code> at the top and use the short name. Never write a fully-qualified name inline.</p>
        </x-docs::rule>
        <x-docs::rule title="Components">
            <p>Build reusable markup as anonymous <code>x-*</code> components with <code>@@props()</code> while the component only prints what it is given. A component that works something out (a class list, a width from a size, a default from the session) is a class-based component in <code>app/View/Components</code>. Reserve <code>@@include</code> for layout glue such as the sidebar, navbar and alerts. Use self-closing tags for slot-less components and <code>$attributes-&gt;merge()</code> for class flexibility. A component with a short script carries it at the bottom of its file in <code>@@pushOnce('js')</code>; a long script goes in a file in <code>resources/js</code> registered with <code>Alpine.data</code> on <code>alpine:init</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Directives for formatting">
            <p>Use <code>@@date</code>, <code>@@time</code>, <code>@@datetime</code>, <code>@@dateforhumans</code>, <code>@@numeric</code>, <code>@@currency</code>, <code>@@percentage</code>, <code>@@prettify</code>, <code>@@bag</code> and <code>@@money</code> instead of raw <code>format()</code>, <code>date()</code> or <code>number_format()</code>. Never write a literal "KES" or "Ksh", even in a placeholder; use <code>e.g. 1000</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Blade syntax">
            <p>Use <code>@@class()</code> for conditional classes, <code>@@selected</code> and <code>@@checked</code> for form state, the <code>:$var</code> shorthand when names match, <code>@@js()</code> to pass PHP data to Alpine, and <code>@@push('js')</code> for inline scripts. Nest content one level deeper than the enclosing directive. Pluralize with <code>str()-&gt;plural()</code> and link with <code>route()</code> and keyed parameters.</p>
        </x-docs::rule>
        <x-docs::rule title="Plain English copy">
            <p>View copy is plain English text, never wrapped in <code>__()</code> or <code>@@lang()</code>. Only flash messages go through the <code>alerts.*</code> keys.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="forms" title="Forms and confirmations">
        <x-docs::rule title="Forms">
            <p>Every text-like input has a helpful placeholder: an example, the expected format, or what a blank value means, never just the label. Required labels carry <code>aria-required="true"</code>. Forms never carry a hidden tenant id.</p>
        </x-docs::rule>
        <x-docs::rule title="The double-submit guard">
            <p>Every submit button is disabled and shows a spinner once its form is submitted, by the package's submit script. Do not add your own guard. Opt a button out with <code>data-allow-resubmit</code> only when something else runs its busy state. Dispatch <code>reset-loading</code> on the form to restore a button after an AJAX submit. Never call <code>form.submit()</code>; use <code>requestSubmit()</code>, and never from inside a <code>submit</code> listener on the same form.</p>
        </x-docs::rule>
        <x-docs::rule title="Destructive actions use confirm()">
            <p>Ask for confirmation of a delete or void with the browser's <code>confirm()</code>, and build no confirmation modal for it.</p>
            <x-docs::code lang="blade" title="resources/views/customers/show.blade.php">
                @verbatim
                <form method="POST" action="{{ route('customers.destroy', ['customer' => $customer]) }}" x-on:submit="if (! confirm('Delete this customer?')) $event.preventDefault()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary">Delete</button>
                </form>
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="Review before saving">
            <p>When a form needs a review step before it posts, use the modal's <code>confirm</code> option with a <code>review</code> slot and a submit in the <code>action</code> slot. Do not hand-roll two-step toggles with <code>x-show="confirm"</code>; they skip native validation or let Enter bypass the review. See Modal.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="scripts" title="Scripts, Alpine and Livewire">
        <x-docs::rule title="One import for the package scripts">
            <p>Import <code>resources/dist/essentials.js</code> once. It is pre-bundled with toastr, jQuery, Alpine UI and pluralize, so the app installs none of them and must not add them to its own <code>package.json</code>. To take only some, import the files in <code>dist</code> one by one. Never import the files under <code>resources/js</code> from an app, and import the bundle before <code>Livewire.start()</code>. See Installation.</p>
        </x-docs::rule>
        <x-docs::rule title="The package owns these">
            <p>Do not write your own toast setup (flash messages raise toastr toasts), shell script (sidebar, drawer and user menu), formatting helpers (<code>money</code>, <code>numeric</code>, <code>prettify</code>, <code>pluralize</code>, <code>formatDate</code> and the rest, mirroring the PHP helpers), file input (use <code>x-essentials::file-input</code>) or modal (use <code>x-essentials::modal</code> for every modal).</p>
        </x-docs::rule>
        <x-docs::rule title="Alpine">
            <p>Keep inline <code>x-data="{ ... }"</code> for trivial state, a couple of keys. Register anything larger with <code>Alpine.data('name', () =&gt; ({ ... }))</code> inside <code>document.addEventListener('alpine:init', ...)</code>, in a <code>@@push('js')</code> block at the bottom of the same file, then use <code>x-data="name"</code>. Components are functions taking a <code>data</code> parameter with destructured defaults, use <code>init()</code>, <code>$watch()</code> and getters, and write <code>x-on:</code> instead of <code>@@</code>. Livewire ships every Alpine plugin except Alpine UI, which Essentials registers, so register only your own plugins, on <code>alpine:init</code>, and never install or start another Alpine.</p>
        </x-docs::rule>
        <x-docs::rule title="Livewire multi-file components">
            <p>The project runs Livewire 4. Build every component as a multi-file component with <code>php artisan make:livewire name --mfc</code>. It lives in its own folder, such as <code>resources/views/components/post/⚡create/</code>, with <code>create.php</code> (the class), <code>create.blade.php</code> (the view) and, when needed, <code>create.js</code>. Do not write single-file, Volt or class-based components.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="design" title="Design">
        <x-docs::rule title="Shared theme and buttons">
            <p>Import the shared theme in the app's Tailwind css and use its primary and secondary colour scales. Do not define your own palette. Every button is primary: use <code>.btn .btn-primary</code> (plus <code>.btn-sm</code> or <code>.btn-lg</code>), never <code>.btn-secondary</code> or raw colour utilities. Design mobile-first and meet WCAG AA.</p>
        </x-docs::rule>
        <x-docs::rule title="1.5 units between cards">
            <p>Cards sit 1.5 spacing units apart, never more: <code>gap-1.5</code>, <code>m-1.5</code>, <code>space-y-1.5</code> or <code>p-1.5</code> on the container, not <code>gap-4</code> or <code>mb-4</code>. Spacing inside a card follows the design.</p>
        </x-docs::rule>
    </x-docs::section>
@endsection
