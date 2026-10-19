@props(['title', 'href'])

<a href="{{ $href }}" class="group flex flex-col no-underline! rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary hover:shadow-md">
    <span class="mb-1 flex items-center justify-between text-base font-semibold text-secondary-dark">
        {{ $title }}
        <span class="text-primary transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
    </span>
    <span class="text-sm font-normal leading-relaxed text-gray-500 no-underline">{{ $slot }}</span>
</a>
