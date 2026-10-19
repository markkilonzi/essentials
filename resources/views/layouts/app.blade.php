@use('Livewire\Livewire')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-currency="{{ config('essentials.currency') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    {{--  The Nyumbani favicons, served by the package so every app inherits them  --}}
    <link rel="icon" type="image/x-icon" href="{{ route('essentials.image', ['image' => 'favicon.ico']) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ route('essentials.image', ['image' => 'favicon-32x32.png']) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ route('essentials.image', ['image' => 'favicon-16x16.png']) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ route('essentials.image', ['image' => 'apple-touch-icon.png']) }}">
    <link rel="manifest" href="{{ route('essentials.manifest') }}">
    <meta name="theme-color" content="{{ config('essentials.branding.theme_color') }}">
    @if (class_exists(Livewire::class))
        @livewireStyles
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body-class', 'h-full bg-gray-100 text-sm text-gray-700 antialiased')">
    @yield('body')

    @if (class_exists(Livewire::class))
        @livewireScriptConfig
    @endif

    @include('essentials::partials.alerts')

    @stack('js')
</body>
</html>
