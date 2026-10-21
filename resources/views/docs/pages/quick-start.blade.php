@extends('docs::layout')

@section('page', 'Quick start')
@section('lead', 'Build one real page, a customers list with a create modal, and meet most of Essentials on the way.')

@section('content')
    <x-docs::section id="goal" title="What we will build">
        <p>A signed-in <strong>Customers</strong> page. It sits in the shell with a sidebar link, lists customers with formatted figures, opens a modal to add one, shows errors inside that modal if the form is wrong, and toasts when it saves.</p>
        <p>Do <a href="{{ $docs->url('installation') }}">Installation</a> first. Each step below names the page that explains it in full.</p>
        <x-docs::table :columns="['Step', 'You write', 'Essentials gives you']" :rows="[
            ['1', 'A route', 'The sign-in redirect for guests.'],
            ['2', 'A controller method', 'back()->alert() toasts and the request->period() macro.'],
            ['3', 'A page that extends the portal layout', 'The shell, sidebar, navbar and page header.'],
            ['4', 'A sidebar link', 'nav-link and nav-group.'],
            ['5', 'A modal with a form', 'The dialog, the error bag and the double-submit guard.'],
            ['6', 'Figures and dates', 'Formatting directives that match the JavaScript helpers.'],
        ]" />
    </x-docs::section>

    <x-docs::section id="route" title="1. Add the route">
        <x-docs::code title="routes/portal.php">
            use App\Http\Controllers\Admins\CustomerController;
            use Illuminate\Support\Facades\Route;

            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
        </x-docs::code>
        <p>The route sits behind the <code>auth</code> middleware like every portal route. The portal layout also sends a guest to <code>login</code>, but that is only a safety net. More in <a href="{{ $docs->url('http-layer') }}#routes">HTTP layer</a>.</p>
    </x-docs::section>

    <x-docs::section id="controller" title="2. Write the controller">
        <x-docs::code title="app/Http/Controllers/Admins/CustomerController.php">
            use App\Http\Requests\Admins\StoreCustomerRequest;
            use App\Models\Customer;
            use Illuminate\Contracts\View\View;
            use Illuminate\Http\RedirectResponse;
            use Illuminate\Support\Facades\Gate;

            class CustomerController
            {
                public function index(): View
                {
                    Gate::authorize('viewAny', Customer::class);

                    return view('admins.pages.customers.index', [
                        'customers' => Customer::query()->latest('id')->get(),
                    ]);
                }

                public function store(StoreCustomerRequest $request): RedirectResponse
                {
                    Gate::authorize('create', Customer::class);

                    $customer = Customer::create($request->validated());

                    return back()->alert('success', trans('alerts.customers.created', ['name' => $customer->name]));
                }
            }
        </x-docs::code>
        <x-docs::code title="lang/en/alerts.php">
            return [
                'customers' => [
                    'created' => 'The customer :name was added.',
                ],
            ];
        </x-docs::code>
        <p><code>alert()</code> flashes a toast that the layout shows after the redirect. See <a href="{{ $docs->url('alerts-and-loader') }}#macro">Alerts and loader</a>.</p>
    </x-docs::section>

    <x-docs::section id="page" title="3. Build the page">
        <x-docs::code lang="blade" title="resources/views/admins/pages/customers/index.blade.php">
            @verbatim
            @extends('layouts.app')

            @section('title', 'Customers')

            @push('actions')
                <button type="button" class="btn btn-primary btn-sm" x-data x-on:click="$dispatch('open-modal', 'new-customer')">Add customer</button>
            @endpush

            @section('content')
                <table class="w-full bg-white text-left">
                    <thead>
                        <tr><th class="p-3">Name</th><th class="p-3">Balance</th><th class="p-3">Joined</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="p-3">{{ $customer->name }}</td>
                                <td class="p-3">@money($customer->balance)</td>
                                <td class="p-3">@date($customer->created_at)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endsection
            @endverbatim
        </x-docs::code>
        <p><code>layouts.app</code> is your one app layout, which extends <code>essentials::layouts.portal</code> and includes the sidebar. See <a href="{{ $docs->url('installation') }}#layout">Installation</a> and <a href="{{ $docs->url('layouts') }}">Layouts</a>. <code>@@money</code> and <code>@@date</code> print <em>KES 1,500.00</em> and <em>12 Mar 2026</em>; see <a href="{{ $docs->url('helpers') }}">Helpers</a>.</p>
    </x-docs::section>

    <x-docs::section id="sidebar" title="4. Link it in the sidebar">
        <x-docs::code lang="blade" title="resources/views/partials/sidebar.blade.php">
            @verbatim
            <x-essentials::nav-link href="{{ route('customers.index') }}" label="Customers" :active="request()->routeIs('customers.*')">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z" />
            </x-essentials::nav-link>
            @endverbatim
        </x-docs::code>
        <p>The slot holds the icon's SVG paths. Group related links with <code>nav-group</code>; the <a href="{{ $docs->url('layouts') }}#sidebar">Layouts</a> page shows one.</p>
    </x-docs::section>

    <x-docs::section id="modal" title="5. Add the modal">
        <p>Add this to the <code>content</code> section. The button in step 3 opens it by name. If validation fails, the page returns with the modal open and the errors inside it.</p>
        <x-docs::code lang="blade">
            @verbatim
            <x-essentials::modal name="new-customer" error-bag="new-customer" title="Add customer">
                <form id="new-customer-form" method="POST" action="{{ route('customers.store') }}" class="p-4">
                    @csrf
                    @bag('new-customer')

                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="{{ $errors->hasBag('new-customer') ? old('name') : '' }}" placeholder="e.g. Jane Wanjiru" aria-required="true" required>
                    @error('name', 'new-customer')
                        <span class="text-xs text-red-600" role="alert">{{ $message }}</span>
                    @enderror
                </form>

                <x-slot:action>
                    <button type="submit" form="new-customer-form" class="btn btn-primary">Save</button>
                </x-slot:action>
            </x-essentials::modal>
            @endverbatim
        </x-docs::code>
        <p>Three Essentials pieces cooperate here. <code>error-bag</code> plus <code>@@bag</code> keep this form's errors separate and reopen the modal. The submit button is protected from double clicks and shows a spinner on its own. See <a href="{{ $docs->url('modal') }}">Modal</a> and <a href="{{ $docs->url('error-bags') }}">Error bags</a>.</p>
    </x-docs::section>

    <x-docs::section id="try" title="6. Try it">
        <ol>
            <li>Open the Customers page. You are in the shell, with the page header holding the <em>Add customer</em> button.</li>
            <li>Press it, leave the name empty and save. The browser stops you. Turn off <code>required</code> briefly and the server rejects it: the modal returns open with the error inside.</li>
            <li>Enter a name and save. The page reloads with a toast and the new row.</li>
        </ol>
        <x-docs::callout type="tip" title="Where next?">
            The <a href="{{ $docs->url('anatomy') }}">Anatomy of a feature</a> page walks the same Customers example from the database to the view with the Nyumbani conventions applied to every layer.
        </x-docs::callout>
    </x-docs::section>
@endsection
