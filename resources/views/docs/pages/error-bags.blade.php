@extends('essentials::docs.layout')

@section('page', 'Error bags')
@section('lead', 'Keep the errors of one form apart from the others on the same page, with one directive and nothing on the request.')

@section('content')
    <x-essentials::docs.section id="problem" title="The problem">
        <p>Laravel puts validation errors in one place. When a page holds a form per row, or a modal over a form, a failed save shows its errors under every form that shares a field name. An error bag gives each form its own box of errors.</p>
        <p>Laravel already supports bags. When validation fails and the response is a redirect, it flashes the errors into the bag named by a posted <code>_error_bag</code> input. The <code>@@bag</code> directive prints that input for you.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="use" title="Use it">
        <p>Add <code>@@bag</code> inside the form and read the errors from the same bag. That is the whole feature.</p>
        <x-essentials::docs.code lang="blade" title="resources/views/customers/edit.blade.php">
            @verbatim
            <form method="POST" action="{{ route('customers.update', ['customer' => $customer]) }}">
                @csrf
                @bag($bag = 'customer-'.$customer->id)

                <input name="name" value="{{ $errors->hasBag($bag) ? old('name') : $customer->name }}">
                @error('name', $bag)
                    <span role="alert">{{ $message }}</span>
                @enderror
            </form>
            @endverbatim
        </x-essentials::docs.code>
        <ol>
            <li><strong>Name the bag.</strong> Pass any string to <code>@@bag</code>: a record id so each row has its own bag, or a fixed name for a modal.</li>
            <li><strong>Read from that bag.</strong> Use <code>@@error('field', $bag)</code> or <code>$errors->getBag($bag)</code>. A plain <code>@@error('field')</code> reads only the default bag and shows nothing.</li>
            <li><strong>Fill from <code>old()</code> with care.</strong> Only use <code>old()</code> when <code>$errors->hasBag($bag)</code>, otherwise a failed save fills every other form on the page too.</li>
        </ol>
        <x-essentials::docs.callout type="tip" title="Nothing on the Form Request">
            The Form Request needs no trait, no <code>prepareForValidation()</code> line and no <code>failedValidation()</code> override. It also works with <code>$request->validate()</code> in a controller.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="how" title="How it works">
        <p>The directive prints one hidden input:</p>
        <x-essentials::docs.code lang="blade" title="What @bag('customer-5') prints">
            <input type="hidden" name="_error_bag" value="customer-5">
        </x-essentials::docs.code>
        <p>When validation fails, Laravel's exception handler redirects back with the errors and chooses the bag like this:</p>
        <ol>
            <li>The posted <code>_error_bag</code>.</li>
            <li>The <code>protected $errorBag</code> of the Form Request, if it sets one.</li>
            <li>The <code>default</code> bag.</li>
        </ol>
        <p>The hidden input is not in your rules, so <code>$request->validated()</code> never contains it.</p>
        <x-essentials::docs.callout type="warning" title="Use the directive, not bag()">
            Older apps, and <code>amprest/dt-tables</code> up to v2.15, define a global <code>bag()</code> function that prints the older <code>_bag</code> input. Essentials does not define one, so use <code>@@bag</code> in views.
        </x-essentials::docs.callout>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="modal" title="With a modal">
        <p>Give the modal the same name in <code>error-bag</code>. After a failed save the page returns with the modal open and the errors showing. See <a href="{{ $docs->url('modal') }}">Modal</a>.</p>
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
        <p>A modal that always uses one bag can set it on the Form Request instead, and skip <code>@@bag</code>:</p>
        <x-essentials::docs.code title="app/Http/Requests/CustomerRequest.php">
            protected $errorBag = 'edit-customer';
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="testing" title="Testing">
        <x-essentials::docs.code title="tests/Feature/CustomerTest.php">
            it('keeps the errors in the form that failed', function () {
                $this->post(route('customers.update', ['customer' => $customer]), ['_error_bag' => 'customer-5', 'name' => ''])
                    ->assertSessionHasErrors('name', null, 'customer-5')
                    ->assertSessionDoesntHaveErrors('name', 'default');
            });
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="limits" title="Good to know">
        <ul>
            <li>Bags apply to the redirect after a failed validation. A JSON response carries the errors without a bag.</li>
            <li>The bag name is user input. Use it only to choose a bag, never to look anything up.</li>
            <li>Keep names short and predictable, such as <code>customer-5</code>, so a view and a test can build the same one.</li>
        </ul>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="migrating" title="Coming from an older app">
        <p>Older Nyumbani apps print a <code>_bag</code> input from their own <code>bag()</code> helper and copy this line into every Form Request:</p>
        <x-essentials::docs.code title="Remove this">
            protected function prepareForValidation(): void
            {
                $this->errorBag = $this->{'_bag'} ?? $this->errorBag;
            }
        </x-essentials::docs.code>
        <p>To move an app over:</p>
        <ol>
            <li>Use the <code>@@bag</code> directive, which prints <code>_error_bag</code>. Delete the app's own <code>bag()</code> helper and any directive that calls it.</li>
            <li>Delete the <code>errorBag</code> line from each <code>prepareForValidation()</code>, and the method itself if it was the only line.</li>
            <li>Run the tests. Views already use <code>@@bag</code>, so they need no change.</li>
        </ol>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="reference" title="Reference">
        <x-essentials::docs.table :columns="['Piece', 'What it is']" :rows="[
            ['@bag($name)', 'A Blade directive that prints the hidden _error_bag input.'],
            ['_error_bag', 'The input Laravel reads to choose the bag the errors go into.'],
            ['$errorBag', 'An optional property on a Form Request, used when no bag is posted.'],
            ['$errors->getBag($name)', 'Laravel API to read one bag in a view or a test.'],
        ]" />
    </x-essentials::docs.section>
@endsection
