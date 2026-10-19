<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @if (class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body-class', 'h-full bg-gray-100 text-sm text-gray-700 antialiased')">
    @yield('body')

    @if (class_exists(\Livewire\Livewire::class))
        @livewireScriptConfig
    @endif

    @include('essentials::partials.alerts')

    @stack('scripts')
</body>
</html>
