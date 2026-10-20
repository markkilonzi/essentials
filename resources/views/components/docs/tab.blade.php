@props(['label'])

<div x-show="active === @js($label)" x-cloak role="tabpanel">
    {{ $slot }}
</div>
