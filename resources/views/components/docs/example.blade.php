@props(['title' => 'Live example'])

<div class="my-5 overflow-hidden rounded-lg border border-primary/40 bg-white shadow-sm">
    <p class="flex items-center gap-2 bg-primary/10 px-4 py-2 text-sm font-semibold text-primary-darker">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
        {{ $title }}
    </p>
    <div {{ $attributes->class('p-4') }}>
        {{ $slot }}
    </div>
</div>
