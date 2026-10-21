@use('Livewire\Livewire')
@if (class_exists(Livewire::class))
    @livewireScriptConfig
@endif

@include('essentials::partials.alerts')
