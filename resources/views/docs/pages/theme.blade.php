@extends('essentials::docs.layout')

@section('page', 'Theme and buttons')
@section('lead', 'One orange, one dark slate, and a single kind of button.')

@section('content')
    <x-essentials::docs.section id="colours" title="Colours">
        <p>The theme defines these Tailwind colours. Use them instead of hard-coded hex values.</p>
        <div class="not-prose my-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach (['primary-darker' => 'bg-primary-darker', 'primary-dark' => 'bg-primary-dark', 'primary' => 'bg-primary', 'secondary-dark' => 'bg-secondary-dark', 'secondary' => 'bg-secondary', 'secondary-light' => 'bg-secondary-light'] as $name => $class)
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
        <x-essentials::docs.code lang="blade">
            @verbatim
            <label for="email" aria-required="true">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="e.g. jane@example.com" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" required>
            @endverbatim
        </x-essentials::docs.code>
        <p>Put an icon span before an input, or a password toggle after it, and the input makes room for it: <code>&lt;span data-icon&gt;</code> before gives left padding, and a following <code>[data-password-toggle]</code> gives right padding.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="file-input" title="File input">
        <p><code>&lt;x-essentials::file-input&gt;</code> replaces the browser's file field with a preview: a thumbnail for an image, the name and size otherwise, and an <em>Upload file</em> or <em>Change file</em> link. Pass the record's current upload to show what is already saved.</p>
        <x-essentials::docs.code lang="blade">
            @verbatim
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                <x-essentials::file-input name="photo" label="Profile photo" accept="image/*" :current="$user->profileImage" />
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
            @endverbatim
        </x-essentials::docs.code>
        <x-essentials::docs.table :columns="['Prop', 'Default', 'Use']" :rows="[
            ['name', 'required', 'The field name the controller reads, and the default id.'],
            ['label', 'none', 'The label above the preview.'],
            ['accept', 'none', 'The browser\'s accept filter, such as image/* or .pdf.'],
            ['current', 'none', 'The saved Upload. It supplies url, title, formattedSize and isImage().'],
        ]" />
        <p>The form must use <code>enctype="multipart/form-data"</code>. See <a href="{{ $docs->url('traits') }}#uploads">Uploads and profile images</a> for the model side.</p>
            <x-essentials::docs.example title="Live: choose a file to see its preview">
            <x-essentials::file-input name="example" label="Choose a file" accept="image/*" />
        </x-essentials::docs.example>
    </x-essentials::docs.section>
@endsection
