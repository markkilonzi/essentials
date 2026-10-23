@extends('docs::layout')

@section('page', 'Modal')
@section('lead', 'One dialog for every app: centred, slide-over, bottom sheet on phones, and able to load its own content.')

@section('content')
    <x-docs::section id="basic" title="A basic modal">
        <p>Never build a one-off modal. Use <code>&lt;x-essentials::modal&gt;</code>. The <code>trigger</code> slot opens it, the default slot is the body, and the <code>action</code> slot is a footer pinned below the scrolling body.</p>
        <x-docs::code lang="blade">
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
        </x-docs::code>
            <x-docs::example title="Live: a trigger slot, x-model, and a long body with a pinned footer">
            <div x-data="{ plain: false, wide: false }" class="flex flex-wrap gap-1.5">
                <x-essentials::modal title="Opened with a trigger slot">
                    <x-slot:trigger><span class="btn btn-primary btn-sm">Trigger slot</span></x-slot:trigger>
                    <p class="p-4">The button is the <code>trigger</code> slot, so this modal needs no Alpine state in the page.</p>
                </x-essentials::modal>

                <button type="button" class="btn btn-primary btn-sm" x-on:click="plain = true">x-model</button>
                <button type="button" class="btn btn-primary btn-sm" x-on:click="wide = true">Long body, sticky footer</button>

                <x-essentials::modal x-model="plain" title="Edit something">
                    <p class="p-4">Press Escape, click outside or use the close button. This is the default <code>md</code> size.</p>
                    <x-slot:action>
                        <div class="flex justify-end gap-1.5 p-3 lg:p-0">
                            <button type="button" class="btn btn-primary btn-sm" x-on:click="plain = false">Cancel</button>
                            <button type="button" class="btn btn-primary btn-sm" x-on:click="plain = false">Save</button>
                        </div>
                    </x-slot:action>
                </x-essentials::modal>

                <x-essentials::modal x-model="wide" title="A long body" size="lg">
                    @foreach (range(1, 14) as $line)
                        <p class="border-b border-gray-100 p-4">Line {{ $line }}: the title bar and the footer stay in view while only this body scrolls.</p>
                    @endforeach
                    <x-slot:action>
                        <div class="flex justify-end gap-1.5 p-3 lg:p-0">
                            <button type="button" class="btn btn-primary btn-sm" x-on:click="wide = false">Done</button>
                        </div>
                    </x-slot:action>
                </x-essentials::modal>
            </div>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="props" title="Props">
        <x-docs::table :columns="['Prop', 'Default', 'Use']" :rows="[
            ['title', 'none', 'The heading of the title bar.'],
            ['size', 'md', 'sm, md, lg, xl or full. Any other value is used as a Tailwind class, such as max-w-2xl.'],
            ['position', 'center', 'right makes a slide-over.'],
            ['name', 'none', 'Lets events open and close the modal.'],
            ['error-bag', 'none', 'Reopens the modal when that bag has errors.'],
            ['dismissable', 'true', 'false is a static backdrop: Escape and outside clicks do nothing.'],
            ['src', 'none', 'A URL whose HTML is loaded the first time the modal opens.'],
            ['close-button, show-title', 'true', 'Hide the close button or the title bar.'],
        ]" />
            <x-docs::example title="Live: every size">
            <div class="flex flex-wrap gap-1.5">
                @foreach (['sm', 'md', 'lg', 'xl', 'full'] as $size)
                    <x-essentials::modal title="Size {{ $size }}" :size="$size">
                        <x-slot:trigger><span class="btn btn-primary btn-sm">{{ $size }}</span></x-slot:trigger>
                        <p class="p-4">This modal uses <code>size="{{ $size }}"</code>.</p>
                    </x-essentials::modal>
                @endforeach
            </div>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="slide-over" title="Slide-over and sizes">
        <x-docs::code lang="blade">
            @verbatim
            <x-essentials::modal title="Filters" position="right" size="lg">...</x-essentials::modal>
            @endverbatim
        </x-docs::code>
        <p>On small screens a centred modal becomes a bottom sheet with a handle, so it is easy to reach with a thumb.</p>
            <x-docs::example title="Live: a slide-over">
            <x-essentials::modal position="right" title="Slide-over">
                <x-slot:trigger><span class="btn btn-primary btn-sm">Open the slide-over</span></x-slot:trigger>
                <p class="p-4">It takes the full height, so it suits edit forms and detail views. On a phone, a centred modal becomes a bottom sheet instead.</p>
            </x-essentials::modal>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="events" title="Open and close by event">
        <p>Give the modal a <code>name</code>, then dispatch an event from anywhere. <code>$dispatch</code> only works inside an Alpine scope, so add <code>x-data</code> to the wrapper when the button has none.</p>
        <x-docs::code lang="blade">
            @verbatim
            <x-essentials::modal name="edit-customer" title="Edit customer">...</x-essentials::modal>

            <div x-data>
                <button type="button" class="btn btn-primary" x-on:click="$dispatch('open-modal', 'edit-customer')">Edit</button>
            </div>
            @endverbatim
        </x-docs::code>
        <p>Close it with <code>$dispatch('close-modal', 'edit-customer')</code>. When the user closes it themselves, the modal dispatches <code>modal-closed</code>. To bind its open state to your own Alpine or Livewire state, put <code>x-model</code> or <code>wire:model</code> on the tag.</p>
            <x-docs::example title="Live: open and close by event">
            <div x-data class="flex flex-wrap gap-1.5">
                <button type="button" class="btn btn-primary btn-sm" x-on:click="$dispatch('open-modal', 'docs-by-event')">Open by event</button>
            </div>

            <x-essentials::modal name="docs-by-event" title="Opened by name">
                <p class="p-4">The button holds no state of this modal. It only dispatched <code>open-modal</code> with the name.</p>
                <x-slot:action>
                    <div class="flex justify-end gap-1.5 p-3 lg:p-0">
                        <button type="button" class="btn btn-primary btn-sm" x-on:click="$dispatch('close-modal', 'docs-by-event')">Close with an event</button>
                    </div>
                </x-slot:action>
            </x-essentials::modal>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="error-bag" title="Reopen after a failed save">
        <p>A user fills in a form inside a modal and saves. If the server rejects it, a normal redirect reloads the page and the modal is gone, along with what the user was doing. Give the modal an <strong>error bag</strong> and it reopens by itself, with the errors showing and the typed values kept.</p>

        <h3>Set it up in four steps</h3>
        <x-docs::steps :steps="[
            ['title' => 'Name the modal and its bag', 'actor' => 'The modal tag', 'detail' => 'Give the modal a name and an error-bag. Using the same word for both keeps it easy to follow.', 'code' => '<x-essentials::modal name=\'edit-customer\' error-bag=\'edit-customer\' ...>'],
            ['title' => 'Post the bag name with the form', 'actor' => 'Inside the form', 'detail' => 'Put the bag directive inside the form, with the same name. It prints a hidden _error_bag input, which tells Laravel which bag to put the errors in.', 'code' => ''.'@'.'bag(\'edit-customer\')'],
            ['title' => 'Validate as usual', 'actor' => 'Your Form Request or controller', 'detail' => 'Nothing changes on the server. When validation fails, Laravel redirects back and fills the posted bag. The Form Request needs no trait and no override.'],
            ['title' => 'Read the errors from that bag', 'actor' => 'Back in the modal', 'detail' => 'Show errors with the bag name as the second argument, and refill the field from old() only when this bag has errors. The modal sees the bag has errors and opens itself.', 'code' => ''.'@'.'error(\'name\', \'edit-customer\')'],
        ]" />

        <h3>The whole thing</h3>
        <x-docs::code lang="blade" title="resources/views/customers/index.blade.php">
            @verbatim
            <x-essentials::modal name="edit-customer" error-bag="edit-customer" title="Edit customer">
                <form method="POST" action="{{ route('customers.update', ['customer' => $customer]) }}">
                    @csrf
                    @method('PUT')
                    @bag('edit-customer')

                    <label for="name" aria-required="true">Name</label>
                    <input id="name" name="name" type="text" required
                           value="{{ $errors->hasBag('edit-customer') ? old('name') : $customer->name }}"
                           aria-invalid="{{ $errors->getBag('edit-customer')->has('name') ? 'true' : 'false' }}"
                           placeholder="e.g. Jane Wanjiru">
                    @error('name', 'edit-customer')
                        <span class="text-xs text-red-600" role="alert">{{ $message }}</span>
                    @enderror

                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                </form>
            </x-essentials::modal>
            @endverbatim
        </x-docs::code>
        <x-docs::code title="app/Http/Requests/Admins/UpdateCustomerRequest.php">
            use Illuminate\Foundation\Http\FormRequest;

            class UpdateCustomerRequest extends FormRequest
            {
                public function rules(): array
                {
                    //  Define the name rule
                    $name = ['required', 'string', 'min:3', 'max:255'];

                    return ['name' => $name];
                }
            }
        </x-docs::code>

        <h3>How it works</h3>
        <ol>
            <li>The reader submits. The hidden <code>_error_bag</code> input travels with the form.</li>
            <li>Validation fails. Laravel redirects back and flashes the errors into the bag named by <code>_error_bag</code>, and the old input with them.</li>
            <li>The page loads again. The modal tag checks whether the session holds errors in <em>its</em> bag. If it does, it opens on load.</li>
            <li>The reader sees their own modal, the typed values and the messages, without clicking anything.</li>
        </ol>
        <p>A modal only reopens for its own bag, so a failed save in one modal never opens another. On a page with several forms, give each its own bag name. See <a href="{{ $docs->url('error-bags') }}">Error bags</a>.</p>

        <h3>If it does not reopen</h3>
        <x-docs::table :columns="['Symptom', 'Likely cause', 'Fix']" :rows="[
            ['The page reloads and the modal stays closed', 'The modal has no error-bag, or the name differs from the one in '.'@'.'bag.', 'Use the same string in error-bag and '.'@'.'bag.'],
            ['It reopens but shows no message', 'The view reads '.'@'.'error(\'name\') without the bag, which only reads the default bag.', 'Pass the bag as the second argument: '.'@'.'error(\'name\', \'edit-customer\').'],
            ['The fields are empty after the failure', 'The input value does not use old().', 'Use old(\'name\') when $errors->hasBag(...) is true, else the saved value.'],
            ['Every modal on the page refills its fields', 'old() is used without checking the bag.', 'Wrap it in $errors->hasBag(\'edit-customer\') as above.'],
            ['It does not reopen after a JSON or Livewire request', 'Bags apply to the redirect after a failed validation. A JSON answer carries the errors without a bag.', 'Use a normal form post, or open the modal from your own state.'],
        ]" />
        <p>A modal that always uses one bag can set it on the Form Request instead, with <code>protected $errorBag = 'edit-customer';</code>, and skip <code>@@bag</code>.</p>

            <x-docs::example title="Live: reopen after a failed save">
            <p class="mb-3 text-sm text-gray-500">Save with fewer than 3 letters. The server rejects it and the page returns with this modal open and the error inside. Save a longer name to see the success toast.</p>
            <div x-data>
                <button type="button" class="btn btn-primary btn-sm" x-on:click="$dispatch('open-modal', 'docs-bag-modal')">Open the form</button>
            </div>

            <x-essentials::modal name="docs-bag-modal" error-bag="docs-bag-modal" title="Create a customer">
                <form method="POST" action="{{ route('essentials.docs.samples.form', ['package' => request()->route('package')]) }}" class="p-4">
                    @csrf
                    @bag('docs-bag-modal')
                    <label for="docs-bag-name">Name</label>
                    <input id="docs-bag-name" name="name" type="text" value="{{ $errors->hasBag('docs-bag-modal') ? old('name') : '' }}" placeholder="e.g. Jane Wanjiru" aria-invalid="{{ $errors->getBag('docs-bag-modal')->has('name') ? 'true' : 'false' }}">
                    @error('name', 'docs-bag-modal')
                        <span class="mt-1 block text-xs text-red-600" role="alert">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn btn-primary btn-sm mt-3">Save</button>
                </form>
            </x-essentials::modal>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="remote" title="Remote content">
        <p>Pass <code>src</code> and return a Blade fragment, not a full page, from that route. The Nyumbani loader shows while it loads, a retry link shows if it fails, and the content is fetched once.</p>
        <x-docs::code lang="blade">
            @verbatim
            <x-essentials::modal title="Customer history" src="{{ route('customers.history', ['customer' => $customer]) }}" size="lg" />
            @endverbatim
        </x-docs::code>
            <x-docs::example title="Live: content loaded on demand">
            <p class="mb-3 text-sm text-gray-500">The first open shows the loader for a second, then the content. Close it and open it again: the time does not change, because it is only fetched once.</p>
            <x-essentials::modal title="Loaded on demand" :src="route('essentials.docs.samples.modal-content', ['package' => request()->route('package')])">
                <x-slot:trigger><span class="btn btn-primary btn-sm">Open remote content</span></x-slot:trigger>
            </x-essentials::modal>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="confirm" title="Two-step confirmation">
        <p>Add <code>confirm</code> to show a review before a form posts. The first step is your form. <strong>Continue</strong> runs the browser's own validation, so required fields, patterns and lengths report as usual. The second step shows the <code>data-review</code> block you wrote inside the form, and the <code>action</code> slot holds the real submit button.</p>
        <x-docs::code lang="blade" title="resources/views/customers/create.blade.php">
            @verbatim
            <x-essentials::modal confirm name="new-customer" error-bag="new-customer" title="New customer">
                <form id="new-customer-form" method="POST" action="{{ route('customers.store') }}" class="p-4">
                    @csrf
                    @bag('new-customer')
                    <input name="name" type="text" required minlength="3">

                    <div data-review>
                        <p class="p-4">Name: <span x-text="data.name"></span></p>
                    </div>
                </form>

                <x-slot:action>
                    <button type="submit" form="new-customer-form" class="btn btn-primary">Save customer</button>
                </x-slot:action>
            </x-essentials::modal>
            @endverbatim
        </x-docs::code>
        <x-docs::table :columns="['Piece', 'What it does']" :rows="[
            ['confirm', 'Turns the two steps on.'],
            ['default slot', 'Step one. Put the form here. It stays on the page during the review, so it still posts.'],
            ['data-review block', 'Step two. Write it inside the form. The modal copies it into the review, so a form shared between modals, or loaded with src, carries its own review. Read what was typed with data.field_name, for example data.name.'],
            ['action slot', 'The submit button, shown in step two. Point it at the form with its form attribute set to the form id.'],
            ['continue-label, back-label', 'Button text. They default to Continue and Back.'],
        ]" />
        <h3>The review lives in the form</h3>
        <p>Put the review in a <code>data-review</code> block inside the form. When the reader presses Continue, the modal copies the block into the review step. The block is hidden in the form, and it reads <code>data</code>. Because the block travels with the form, one form partial serves a modal on the page and a form loaded from a url with <code>src</code>, and nothing is repeated. A remote form also hides itself during the review, so it needs no <code>x-show</code> of its own.</p>
        <x-docs::code lang="blade" title="resources/views/customers/partials/form.blade.php">
            @verbatim
            <form id="customer-form" method="POST" action="{{ $action }}" class="p-4">
                @csrf
                <input name="name" type="text" required minlength="3">

                <div data-review>
                    Name: <span x-text="data.name"></span>
                </div>
            </form>
            @endverbatim
        </x-docs::code>
        <x-docs::callout type="note" title="The review slot is retired">
            Older code may still use <code>&lt;x-slot:review&gt;</code>, and it keeps working, but new code never does. A <code>data-review</code> block is the only way to write a review.
        </x-docs::callout>
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
        <x-docs::table :columns="['Case', 'Result']" :rows="[
            ['Input, textarea, select with a name', 'Its current value. A select gives the option value, so show a label yourself.'],
            ['Input with no name', 'Not in data. Give it a name.'],
            ['Value held only in Alpine state, or a custom dropdown', 'Not in data. Write it to a hidden input with a name.'],
            ['Field removed with x-if', 'Not in data. x-show is fine.'],
            ['Checkbox group or name[]', 'Only one value. Show these yourself.'],
        ]" />
        <p>The review is copied into the modal, so it cannot see the form's own <code>x-data</code>. It reads <code>data</code> only.</p>
        <x-docs::callout type="note" title="Not a confirm dialog">
            This reviews a form before saving. To confirm a destructive action, use the browser's <code>confirm()</code> as below.
        </x-docs::callout>
            <x-docs::example title="Live: review before saving">
            <x-essentials::modal confirm name="docs-confirm" error-bag="docs-confirm" title="New customer">
                <x-slot:trigger><span class="btn btn-primary btn-sm">Add a customer</span></x-slot:trigger>

                <form id="docs-confirm-form" method="POST" action="{{ route('essentials.docs.samples.form', ['package' => request()->route('package')]) }}" class="p-4">
                    @csrf
                    @bag('docs-confirm')
                    <label for="docs-confirm-name" aria-required="true">Name</label>
                    <input id="docs-confirm-name" name="name" type="text" required minlength="3" placeholder="At least 3 letters">

                    <div data-review>
                        <div class="p-4">
                            <p class="text-gray-500">Check the details, then save.</p>
                            <p class="mt-2 text-lg"><span class="text-gray-500">Name:</span> <span x-text="data.name"></span></p>
                        </div>
                    </div>
                </form>

                <x-slot:action>
                    <button type="submit" form="docs-confirm-form" class="btn btn-primary">Save customer</button>
                </x-slot:action>
            </x-essentials::modal>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="static" title="Static backdrop and confirmations">
        <x-docs::code lang="blade">
            @verbatim
            <x-essentials::modal title="Finish setup" :dismissable="false" :close-button="false">...</x-essentials::modal>
            @endverbatim
        </x-docs::code>
        <x-docs::callout type="note" title="No confirm dialog">
            Ask for confirmation with the browser's <code>confirm()</code>. Do not build a confirm modal.
        </x-docs::callout>
            <x-docs::example title="Live: a static backdrop, and confirming with confirm()">
            <div x-data class="flex flex-wrap gap-1.5">
                <button type="button" class="btn btn-primary btn-sm" x-on:click="$dispatch('open-modal', 'docs-static')">Open the static modal</button>
                <button type="button" class="btn btn-primary btn-sm" x-on:click="if (confirm('Raise a warning toast?')) toastr.warning('You confirmed it.')">Ask first</button>
            </div>

            <x-essentials::modal name="docs-static" title="Static modal" :dismissable="false" :close-button="false">
                <p class="p-4">Escape and an outside click do nothing here. Only the button closes it.</p>
                <x-slot:action>
                    <div class="flex justify-end gap-1.5 p-3 lg:p-0">
                        <button type="button" class="btn btn-primary btn-sm" x-on:click="$dispatch('close-modal', 'docs-static')">Close</button>
                    </div>
                </x-slot:action>
            </x-essentials::modal>
        </x-docs::example>
    </x-docs::section>

    <x-docs::section id="padding" title="Pad the content">
        <x-docs::rule title="Modal content is always padded">
            <p>The component does not pad the body, so the content you put in it must be padded: wrap the default slot in an element with at least <code>p-4</code>, or put the class on the form. A fragment loaded with <code>src</code> pads its own root element. No text, input, table or button may touch the edge of the panel. Only a deliberate full-bleed image or table may skip it. The <code>action</code> footer is padded by the component.</p>
            <x-docs::code lang="blade" title="resources/views/customers/edit.blade.php">
                @verbatim
                <x-essentials::modal title="Edit customer" name="edit-customer">
                    <form method="POST" action="{{ route('customers.update', ['customer' => $customer]) }}" class="p-4">
                        @csrf
                        @method('PUT')
                        ...
                    </form>
                </x-essentials::modal>
                @endverbatim
            </x-docs::code>
        </x-docs::rule>
    </x-docs::section>
@endsection
