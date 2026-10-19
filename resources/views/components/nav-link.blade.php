@props(['href', 'label', 'active' => false, 'sub' => false])

<a href="{{ $href }}" title="{{ $label }}" {{ $attributes }} @class([
    'mb-0.5 flex w-full items-center gap-2.5 rounded-sm px-3 py-2.5 text-[13px] transition duration-200 ease-linear hover:bg-white/10 hover:text-white',
    'text-white group-data-collapsed/shell:lg:justify-center group-data-collapsed/shell:lg:px-0' => ! $sub,
    'pl-9 text-gray-400' => $sub,
    'bg-gray-700 text-white' => $active,
])>
    @unless ($sub)
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{{ $slot }}</svg>
    @endunless
    <span @class(['group-data-collapsed/shell:lg:hidden' => ! $sub])>{{ $label }}</span>
</a>
