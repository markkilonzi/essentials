@extends('essentials::docs.layout')

@section('page', 'Alerts and loader')
@section('lead', 'Tell the user what happened, and show them something while they wait.')

@section('content')
    <x-essentials::docs.section id="toasts" title="Toasts">
        <p>Flash from a controller and the layout shows it. Every type uses the same colour, so pass the honest type for the meaning.</p>
        <x-essentials::docs.code>
            return back()->alert('success', __('alerts.customer_saved'));
        </x-essentials::docs.code>
        <p>From JavaScript, the toastr global is available:</p>
        <x-essentials::docs.code lang="js">
            toastr.success('Saved');
            toastr.error('Could not save');
        </x-essentials::docs.code>
        <p>A Laravel <code>status</code> flash, such as the one Fortify sets, also shows as a success toast.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="loader" title="Loader">
        <p>The animated Nyumbani mark. Use it for any wait the user can see. Do not invent another spinner.</p>
        <div class="not-prose my-5 flex flex-wrap items-center justify-around gap-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <x-essentials::loader size="md" message="Nyumbani is loading this for you..." />
        </div>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <x-essentials::loader />
            <x-essentials::loader size="md" message="Nyumbani is loading this for you..." />
            <x-essentials::loader message="" />
            @endverbatim
        </x-essentials::docs.code>
        <x-essentials::docs.table :columns="['Prop', 'Default', 'Use']" :rows="[
            ['message', 'Nyumbani is processing your data...', 'The pulsing text. Pass an empty string to hide it, because null falls back to the default.'],
            ['size', 'lg', 'sm, md or lg.'],
        ]" />
    </x-essentials::docs.section>
@endsection
