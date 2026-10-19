@extends('essentials::docs.layout')

@section('page', 'Helpers')
@section('lead', 'The same formatting in PHP, Blade and JavaScript, so a figure looks identical everywhere.')

@section('content')
    <x-essentials::docs.section id="php" title="PHP helpers">
        <x-essentials::docs.table :columns="['Helper', 'Returns', 'Example']" :rows="[
            ['user()', 'The signed-in user or null', 'user()->name'],
            ['money($amount, $showCurrency = true)', 'Formatted money', 'money(1500) gives KES 1,500.00'],
            ['numeric($number, $decimals = 2)', 'A number with separators', 'numeric(1200.5) gives 1,200.50'],
            ['percentage($value, $total)', 'The share of a total, or 0', 'percentage(25, 200) gives 12.5'],
            ['currency()', 'The currency symbol', 'KES'],
            ['prettify($text)', 'Title Case from snake or kebab case', 'prettify(\'order_status\') gives Order Status'],
            ['format_date($date)', 'A date', '12 Mar 2026'],
            ['format_time($date, 12)', 'A time, 12 or 24 hour', '02:30 pm'],
            ['format_date_time($date, 12)', 'A date and time', 'Thu, 12 Mar 2026, 02:30 pm'],
            ['format_humans($date)', 'A relative time', '2 hours ago'],
            ['format_minutes($minutes)', 'Hours and minutes', '1h 30m'],
        ]" />
        <p>Dates that cannot be parsed are returned as they came, so a bad value shows up instead of crashing a page.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="blade" title="Blade directives">
        <p>Each helper has a directive, so views never need <code>@@php</code>.</p>
        <x-essentials::docs.code lang="blade">
            @verbatim
            @money($invoice->total)
            @numeric($count, 0)
            @percentage($paid, $total)
            @date($invoice->due_at)
            @time($visit->starts_at)
            @datetime($order->created_at)
            @dateforhumans($order->created_at)
            @prettify($status)
            @currency
            @bag('row-1')
            @endverbatim
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="js" title="JavaScript helpers">
        <p>The same names exist on <code>window</code> once <code>helpers.js</code> is loaded, and read the currency from the <code>data-currency</code> attribute the layout sets on <code>&lt;html&gt;</code>.</p>
        <x-essentials::docs.code lang="js">
            money(1500);            // KES 1,500.00
            numeric(1200.5, 2);     // 1,200.50
            parseNumber('1,200.50'); // 1200.5
            pluralize('invoice', 3); // invoices
            prettify('order_status');
            formatDate(order.created_at);
            formatDateTime(order.created_at);
        </x-essentials::docs.code>
    </x-essentials::docs.section>
@endsection
