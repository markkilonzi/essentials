@extends('essentials::docs.layout')

@section('page', 'Modal')
@section('lead', 'One dialog for every app: centred, slide-over, bottom sheet on phones, and able to load its own content.')

@section('content')
    <x-essentials::docs.section id="basic" title="A basic modal">
        <p>Never build a one-off modal. Use <code>&lt;x-essentials::modal&gt;</code>. The <code>trigger</code> slot opens it, the default slot is the body, and the <code>action</code> slot is a footer pinned below the scrolling body.</p>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <x-essentials::modal title="Add customer">
                <x-slot:trigger>
                    <button type="button" class="btn btn-primary">Add customer</button>
                </x-slot:trigger>

                <form id="customer-form" method="POST" action="{{ route('customers.store') }}" class="p-4">
                    @csrf
                    ...
                </form>

                <x-slot:action>
                    <button type="submit" form="customer-form" class="btn btn-primary">Save</button>
                </x-slot:action>
            </x-essentials::modal>
            @endverbatim
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="props" title="Props">
        <x-essentials::docs.table :columns="['Prop', 'Default', 'Use']" :rows="[
            ['title', 'none', 'The heading of the title bar.'],
            ['size', 'md', 'sm, md, lg, xl or full.'],
            ['position', 'center', 'right makes a slide-over.'],
            ['name', 'none', 'Lets events open and close the modal.'],
            ['error-bag', 'none', 'Reopens the modal when that bag has errors.'],
            ['dismissable', 'true', 'false is a static backdrop: Escape and outside clicks do nothing.'],
            ['src', 'none', 'A URL whose HTML is loaded the first time the modal opens.'],
            ['close-button, show-title', 'true', 'Hide the close button or the title bar.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="slide-over" title="Slide-over and sizes">
        <x-essentials::docs.code lang="blade">
            @verbatim
            <x-essentials::modal title="Filters" position="right" size="lg">...</x-essentials::modal>
            @endverbatim
        </x-essentials::docs.code>
        <p>On small screens a centred modal becomes a bottom sheet with a handle, so it is easy to reach with a thumb.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="events" title="Open and close by event">
        <p>Give the modal a <code>name</code>, then dispatch an event from anywhere. <code>$dispatch</code> only works inside an Alpine scope, so add <code>x-data</code> to the wrapper when the button has none.</p>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <x-essentials::modal name="edit-customer" title="Edit customer">...</x-essentials::modal>

            <div x-data>
                <button type="button" class="btn btn-primary" x-on:click="$dispatch('open-modal', 'edit-customer')">Edit</button>
            </div>
            @endverbatim
        </x-essentials::docs.code>
        <p>Close it with <code>$dispatch('close-modal', 'edit-customer')</code>.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="error-bag" title="Reopen after a failed save">
        <p>Give the modal an error bag and post the same name with <code>@@bag</code>. When validation fails, the page returns with the modal open and the errors showing. The Form Request needs nothing. See <a href="{{ $docs->url('error-bags') }}">Error bags</a>.</p>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <x-essentials::modal name="edit-customer" error-bag="edit-customer" title="Edit customer">
                <form method="POST" action="{{ route('customers.update', ['customer' => $customer]) }}">
                    @csrf
                    @bag('edit-customer')
                    ...
                </form>
            </x-essentials::modal>
            @endverbatim
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="remote" title="Remote content">
        <p>Pass <code>src</code> and return a Blade fragment, not a full page, from that route. The Nyumbani loader shows while it loads, a retry link shows if it fails, and the content is fetched once.</p>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <x-essentials::modal title="Customer history" src="{{ route('customers.history', ['customer' => $customer]) }}" size="lg" />
            @endverbatim
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="confirm" title="Two-step confirmation">
        <p>Add <code>confirm</code> to show a review before a form posts. The first step is your form. <strong>Continue</strong> runs the browser's own validation, so required fields, patterns and lengths report as usual. The second step shows your <code>review</code> slot, and the <code>action</code> slot holds the real submit button.</p>
        <x-essentials::docs.code lang="blade" title="resources/views/customers/create.blade.php">
            @verbatim
            <x-essentials::modal confirm name="new-customer" error-bag="new-customer" title="New customer">
                <form id="new-customer-form" method="POST" action="{{ route('customers.store') }}" class="p-4">
                    @csrf
                    @bag('new-customer')
                    <input name="name" type="text" required minlength="3">
                </form>

                <x-slot:review>
                    <p class="p-4">Name: <span x-text="data.name"></span></p>
                </x-slot:review>

                <x-slot:action>
                    <button type="submit" form="new-customer-form" class="btn btn-primary">Save customer</button>
                </x-slot:action>
            </x-essentials::modal>
            @endverbatim
        </x-essentials::docs.code>
        <x-essentials::docs.table :columns="['Piece', 'What it does']" :rows="[
            ['confirm', 'Turns the two steps on.'],
            ['default slot', 'Step one. Put the form here. It stays on the page during the review, so it still posts.'],
            ['review slot', 'Step two. Read what was typed with data.field_name, for example data.name.'],
            ['action slot', 'The submit button, shown in step two. Point it at the form with its form attribute set to the form id.'],
            ['continue-label, back-label', 'Button text. They default to Continue and Back.'],
        ]" />
        <p>The steps slide and fade into each other, so the move between the form and the review is visible. Back plays the same animation in reverse.</p>
        <h3>How the steps behave</h3>
        <ul>
            <li><strong>Continue validates.</strong> It calls the browser's <code>reportValidity()</code> on the form, so <code>required</code>, <code>minlength</code>, <code>pattern</code>, <code>type="email"</code> and the rest report with the browser's own messages. The review opens only when every field passes.</li>
            <li><strong>The Enter key counts as Continue.</strong> Step one renders no submit button, and a submit during step one is intercepted before any handler on the form sees it, so the review is never skipped. That includes forms with their own <code>x-on:submit</code> or Livewire <code>wire:submit</code> handlers, which only run in step two.</li>
            <li><strong>It always starts on step one.</strong> Opening, closing and reopening reset it. A failed save reopens it on step one with the errors when you set <code>error-bag</code>.</li>
            <li><strong>The form keeps posting.</strong> It stays on the page behind the review, so the submit button in the <code>action</code> slot sends every field. Give the button <code>form</code> set to the form id, because the footer sits outside the form.</li>
        </ul>
        <h3>What the review can read</h3>
        <p>When Continue passes, the modal copies every field of the form into <code>data</code>, keyed by the field's <code>name</code> attribute. A field named <code>phone_number</code> is <code>data.phone_number</code>. The values are read from the page as it stands, so Alpine <code>x-model</code> and Livewire <code>wire:model</code> inputs work.</p>
        <x-essentials::docs.table :columns="['Case', 'Result']" :rows="[
            ['Input, textarea, select with a name', 'Its current value. A select gives the option value, so show a label yourself.'],
            ['Input with no name', 'Not in data. Give it a name.'],
            ['Value held only in Alpine state, or a custom dropdown', 'Not in data. Write it to a hidden input with a name.'],
            ['Field removed with x-if', 'Not in data. x-show is fine.'],
            ['Checkbox group or name[]', 'Only one value. Show these yourself.'],
        ]" />
        <p>The <code>review</code> slot sits outside the form, so it cannot see the form's own <code>x-data</code>. It reads <code>data</code> only.</p>
        <x-essentials::docs.callout type="note" title="Not a confirm dialog">
            This reviews a form before saving. To confirm a destructive action, use the browser's <code>confirm()</code> as below.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="static" title="Static backdrop and confirmations">
        <x-essentials::docs.code lang="blade">
            @verbatim
            <x-essentials::modal title="Finish setup" :dismissable="false" :close-button="false">...</x-essentials::modal>
            @endverbatim
        </x-essentials::docs.code>
        <x-essentials::docs.callout type="note" title="No confirm dialog">
            Ask for confirmation with the browser's <code>confirm()</code>. Do not build a confirm modal.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>
@endsection
