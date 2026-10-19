@props(['message' => 'Nyumbani is processing your data...', 'size' => 'lg'])

<div role="status" aria-live="polite" class="flex w-full flex-col items-center justify-center gap-1 py-2">
    <img {{ $attributes->class(['h-16' => $size === 'sm', 'h-28' => $size === 'md', 'h-40' => $size === 'lg', 'w-auto select-none']) }} src="{{ route('essentials.image', 'loader.svg') }}" alt="" draggable="false">

    @if ($message)
        <p @class(['text-center font-medium text-secondary motion-safe:animate-pulse', 'text-sm' => $size === 'sm', 'text-base' => $size === 'md', 'text-lg' => $size === 'lg'])>{{ $message }}</p>
    @else
        <span class="sr-only">Loading</span>
    @endif
</div>
