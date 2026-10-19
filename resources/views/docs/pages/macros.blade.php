@extends('essentials::docs.layout')

@section('page', 'Macros')
@section('lead', 'Two small additions to Laravel: toast alerts on redirects and a period on requests.')

@section('content')
    <x-essentials::docs.section id="alert" title="Alert on a redirect">
        <p><code>alert()</code> flashes a toast. It works on <code>back()</code>, <code>redirect()</code> and any redirect response. Put the text in a lang file, not the controller.</p>
        <x-essentials::docs.code title="app/Http/Controllers/InvoiceController.php">
            return back()->alert('success', __('alerts.invoice_saved'));

            return redirect()->route('invoices.index')->alert('error', __('alerts.invoice_failed'));
        </x-essentials::docs.code>
        <p>The types are <code>success</code>, <code>error</code>, <code>warning</code> and <code>info</code>. See <a href="{{ $docs->url('alerts-and-loader') }}">Alerts and loader</a>.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="period" title="Period on a request">
        <p>Reports are scoped by a <code>period</code> input. <code>$request->period()</code> turns it into the dates to query.</p>
        <x-essentials::docs.code>
            use App\Models\Invoice;

            $period = $request->period();          // defaults to the current month
            $period = $request->period('year');    // or 'day'

            Invoice::whereBetween('created_at', [$period->from, $period->to])->get();
        </x-essentials::docs.code>
        <x-essentials::docs.table :columns="['Input', 'Meaning']" :rows="[
            ['2026', 'The whole year.'],
            ['2026-03', 'The month.'],
            ['2026-03-14', 'The day.'],
            ['2026-01-01 to 2026-03-31', 'A range, which may not end before it starts.'],
        ]" />
        <p>The result has <code>date</code>, <code>year</code>, <code>month</code>, <code>from</code>, <code>to</code> and <code>formatted</code>. Anything it cannot read, including a date like 2026-02-31, falls back to the default period.</p>
    </x-essentials::docs.section>
@endsection
