@extends('essentials::docs.layout')

@section('page', 'Theme and buttons')
@section('lead', 'One orange, one dark slate, and a single kind of button.')

@section('content')
    <x-essentials::docs.section id="colours" title="Colours">
        <p>The theme defines these Tailwind colours. Use them instead of hard-coded hex values.</p>
        <div class="not-prose my-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach (['primary-darkest' => 'bg-primary-darkest', 'primary-darker' => 'bg-primary-darker', 'primary-dark' => 'bg-primary-dark', 'primary' => 'bg-primary', 'primary-light' => 'bg-primary-light', 'secondary-dark' => 'bg-secondary-dark', 'secondary' => 'bg-secondary', 'secondary-light' => 'bg-secondary-light'] as $name => $class)
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="{{ $class }} h-14"></div>
                    <p class="px-3 py-2 text-xs font-medium text-gray-600">{{ $name }}</p>
                </div>
            @endforeach
        </div>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="buttons" title="Buttons">
        <p>Every button is primary. There is no outline, danger or secondary variant, and <code>btn-secondary</code> only exists so old markup keeps its colour. Choose a size, never a colour.</p>
        <div class="not-prose my-5 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <button type="button" class="btn btn-primary btn-sm">Small</button>
            <button type="button" class="btn btn-primary">Default</button>
            <button type="button" class="btn btn-primary btn-lg">Large</button>
            <button type="button" class="btn btn-primary" disabled>Disabled</button>
        </div>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-primary btn-sm">Edit</button>
            <button type="submit" class="btn btn-primary btn-lg">Pay now</button>
            @endverbatim
        </x-essentials::docs.code>
        <x-essentials::docs.callout title="Destructive actions">
            A delete button is still a primary button. Guard it with the browser's own confirm instead of building a confirm dialog.
        </x-essentials::docs.callout>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <button type="submit" class="btn btn-primary" onclick="return confirm('Delete this invoice?')">Delete</button>
            @endverbatim
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="forms" title="Forms">
        <p>Inputs, selects, checkboxes and labels are styled by the theme, so write plain HTML. A field with <code>aria-invalid="true"</code> gets a red border, and <code>aria-required="true"</code> adds a red asterisk.</p>
    </x-essentials::docs.section>
@endsection
