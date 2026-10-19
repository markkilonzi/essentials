@extends('essentials::layouts.error')

@section('code', '401')
@section('heading', 'Hold on, who are you?')
@section('detail', 'You need to sign in to see this page. Pop your details in and we will let you right through.')
@section('icon')
    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
@endsection
@section('actions')
    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-md bg-white/10 px-5 py-2.5 font-medium text-white ring-1 ring-white/30 transition hover:bg-white/20">Sign in</a>
@endsection
