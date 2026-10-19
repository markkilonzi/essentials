@props(['label', 'busy'])

{{--  Used inside a form that holds x-data="{ busy: false }" x-on:submit="busy = true"  --}}
<button type="submit" data-submit x-bind:disabled="busy" {{ $attributes->merge(['class' => 'btn btn-primary btn-lg w-full uppercase tracking-wide']) }}>
    <svg x-show="busy" x-cloak class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
    <span x-show="! busy" data-idle>{{ $label }}</span>
    <span x-show="busy" x-cloak>{{ $busy }}</span>
</button>
