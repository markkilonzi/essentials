@use('Livewire\Livewire')
{{--  The app's own styles and scripts, which the live examples of these docs (modals, toasts, forms) need  --}}
<link rel="manifest" href="{{ route('essentials.manifest') }}">
<meta name="theme-color" content="{{ config('essentials.branding.theme_color') }}">
@if (class_exists(Livewire::class))
    @livewireStyles
@endif
@vite(['resources/css/app.css', 'resources/js/app.js'])
