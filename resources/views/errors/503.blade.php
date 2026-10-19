@extends('essentials::layouts.error')

@section('code', '503')
@section('heading', 'Back in a moment')
@section('detail', 'We are carrying out scheduled maintenance and will be right back. Thanks for your patience; give it a minute and try again.')
@section('icon')
    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085"/>
@endsection
@section('actions')
    <button type="button" onclick="location.reload()" class="inline-flex items-center gap-2 rounded-md bg-white/10 px-5 py-2.5 font-medium text-white ring-1 ring-white/30 transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Try again</button>
@endsection
