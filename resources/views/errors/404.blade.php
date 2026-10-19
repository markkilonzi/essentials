@extends('essentials::layouts.error')

@section('code', '404')
@section('heading', 'Page not found')
@section('detail', 'We looked in every room and could not find it. The link may be old, mistyped, or the page may have moved house.')
@section('icon')
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
@endsection

