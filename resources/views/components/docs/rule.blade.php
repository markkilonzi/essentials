@props(['title'])

<div {{ $attributes->class('my-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm') }}>
    <h3 class="mb-1 text-base font-semibold text-secondary-dark">{{ $title }}</h3>
    <div class="docs-prose text-sm text-gray-600">
        {{ $slot }}
    </div>
</div>
