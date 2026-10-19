@extends('essentials::layouts.error')

@section('code', '429')
@section('heading', 'Easy there, speedy')
@section('detail', 'You made a lot of requests in a short time. Take a breath, wait a moment, then try again.')
@section('icon')
    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
@endsection
@section('actions')
    <button type="button" onclick="location.reload()" class="inline-flex items-center gap-2 rounded-md bg-white/10 px-5 py-2.5 font-medium text-white ring-1 ring-white/30 transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Try again</button>
@endsection
