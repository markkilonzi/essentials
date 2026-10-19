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
                <form method="POST" action="{{ route('customers.update', $customer) }}">
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
            <x-essentials::modal title="Customer history" src="{{ route('customers.history', $customer) }}" size="lg" />
            @endverbatim
        </x-essentials::docs.code>
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
