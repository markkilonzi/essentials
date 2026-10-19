@extends('essentials::layouts.error')

@section('code', '419')
@section('heading', 'Your session took a nap')
@section('detail', 'The page sat idle for too long and expired for your safety. Reload it and try again; anything you had not saved was not sent.')
@section('icon')
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
@endsection
@section('actions')
    <button type="button" onclick="location.reload()" class="inline-flex items-center gap-2 rounded-md bg-white/10 px-5 py-2.5 font-medium text-white ring-1 ring-white/30 transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Try again</button>
@endsection
