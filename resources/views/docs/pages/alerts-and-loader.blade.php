@extends('docs::layout')

@section('page', 'Alerts and loader')
@section('lead', 'Tell the user what happened, and show them something while they wait.')

@section('content')
    <x-docs::section id="macro" title="The alert() macro">
        <p>The package adds <code>alert($type, $message)</code> to redirects. It works on <code>back()</code>, on <code>redirect()->route(...)</code> and on any other redirect response, and flashes the toast for the next page.</p>
        <x-docs::code title="app/Http/Controllers/InvoiceController.php">
            return back()->alert('success', trans('alerts.invoices.saved'));

            return redirect()->route('invoices.index')->alert('error', trans('alerts.invoices.failed'));
        </x-docs::code>
        <ul>
            <li>The types are <code>success</code>, <code>error</code>, <code>warning</code> and <code>info</code>.</li>
            <li><code>redirect()->alert(...)</code> with no route goes back to the previous page, the same as <code>back()->alert(...)</code>.</li>
            <li>The toast is flashed to the session under <code>alert</code> (<code>type</code> and <code>message</code>), and the layout turns it into a toast. A Laravel <code>status</code> flash, such as the one Fortify sets, also shows as a success toast.</li>
            <li>Put the text in a lang file, never in the controller.</li>
        </ul>
    </x-docs::section>

    <x-docs::section id="toasts" title="Toasts">
        <p>Flash from a controller and the layout shows it. Every type uses the same colour, so pass the honest type for the meaning.</p>
        <x-docs::code>
            return back()->alert('success', __('alerts.customer_saved'));
        </x-docs::code>
        <p>From JavaScript, the toastr global is available:</p>
        <x-docs::code lang="js">
            toastr.success('Saved');
            toastr.error('Could not save');
        </x-docs::code>
        <p>A Laravel <code>status</code> flash, such as the one Fortify sets, also shows as a success toast.</p>
    </x-docs::section>

    <x-docs::section id="try" title="Try the toasts">
        <p>These buttons raise the same toasts that <code>back()->alert()</code> shows after a redirect. Change the text and press a type.</p>
        <div x-data="{ message: 'The customer Jane Wanjiru was saved.' }" class="not-prose my-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <label for="toast-message">Message</label>
            <input id="toast-message" type="text" x-model="message" placeholder="e.g. The customer Jane Wanjiru was saved.">
            <div class="mt-3 flex flex-wrap gap-1.5">
                @foreach (['success', 'error', 'warning', 'info'] as $type)
                    <button type="button" class="btn btn-primary btn-sm" x-on:click="toastr.{{ $type }}(message)">{{ $type }}</button>
                @endforeach
            </div>
        </div>
        <p>Every type has the same colour, so the word you choose is the meaning: pick the honest one.</p>
    </x-docs::section>

    <x-docs::section id="examples" title="Which type, when">
        <x-docs::table :columns="['Type', 'Use it when', 'Example text']" :rows="[
            ['success', 'The action did what the user asked.', 'The customer Jane Wanjiru was saved.'],
            ['error', 'The action failed and nothing changed.', 'The payment could not be started. Please try again.'],
            ['warning', 'It worked, but the user should look again, or it only partly worked.', 'The invoice was saved, but 2 tenants have no phone number.'],
            ['info', 'Something to know that is not a result.', 'Reminders are sent every morning at 8:00.'],
        ]" />
    </x-docs::section>

    <x-docs::section id="controller" title="From a controller">
        <p>Each type, as a controller would send it. The text lives in <code>lang/en/alerts.php</code>, with <code>:placeholder</code> values filled by the second argument of <code>trans()</code>.</p>
        <x-docs::tabs :labels="['success', 'error', 'warning', 'info']">
            <x-docs::tab label="success">
                <x-docs::code title="app/Http/Controllers/Admins/CustomerController.php">
                    return back()->alert('success', trans('alerts.customers.saved', ['name' => $customer->name]));
                </x-docs::code>
            </x-docs::tab>
            <x-docs::tab label="error">
                <x-docs::code title="app/Http/Controllers/Admins/PaymentController.php">
                    use Nyumbani\Daraja\Daraja;
                    use Nyumbani\Daraja\Exceptions\DarajaException;

                    try {
                        $response = Daraja::for($shortCode)->stkPush(...);
                    } catch (DarajaException) {
                        return back()->alert('error', trans('alerts.payments.unreachable'));
                    }
                </x-docs::code>
            </x-docs::tab>
            <x-docs::tab label="warning">
                <x-docs::code title="app/Http/Controllers/Admins/InvoiceController.php">
                    return redirect()->route('invoices.index')
                        ->alert('warning', trans('alerts.invoices.saved_without_phones', ['count' => $missing]));
                </x-docs::code>
            </x-docs::tab>
            <x-docs::tab label="info">
                <x-docs::code title="app/Http/Controllers/Admins/ReminderController.php">
                    return back()->alert('info', trans('alerts.reminders.schedule'));
                </x-docs::code>
            </x-docs::tab>
        </x-docs::tabs>
        <x-docs::code title="lang/en/alerts.php">
            return [
                'customers' => ['saved' => 'The customer :name was saved.'],
                'payments' => ['unreachable' => 'The payment could not be started. Please try again.'],
                'invoices' => ['saved_without_phones' => 'The invoice was saved, but :count tenants have no phone number.'],
                'reminders' => ['schedule' => 'Reminders are sent every morning at 8:00.'],
            ];
        </x-docs::code>
    </x-docs::section>

    <x-docs::section id="loader" title="Loader">
        <p>The animated Nyumbani mark. Use it for any wait the user can see. Do not invent another spinner.</p>
        <div class="not-prose my-5 flex flex-wrap items-center justify-around gap-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <x-essentials::loader size="md" message="Nyumbani is loading this for you..." />
        </div>
        <x-docs::code lang="blade">
            @verbatim
            <x-essentials::loader />
            <x-essentials::loader size="md" message="Nyumbani is loading this for you..." />
            <x-essentials::loader message="" />
            @endverbatim
        </x-docs::code>
        <x-docs::table :columns="['Prop', 'Default', 'Use']" :rows="[
            ['message', 'Nyumbani is processing your data...', 'The pulsing text. Pass an empty string to hide it, because null falls back to the default.'],
            ['size', 'lg', 'sm, md or lg.'],
        ]" />
    </x-docs::section>
@endsection
