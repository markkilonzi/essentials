@props(['label', 'active' => false, 'icon'])

<details class="group/nav mb-0.5" @if ($active) open @endif>
    <summary data-nav-group @class([
        'flex cursor-pointer list-none items-center gap-2.5 rounded-sm px-3 py-2.5 text-[13px] text-white transition duration-200 ease-linear marker:hidden hover:bg-white/10 hover:text-white group-data-collapsed/shell:lg:justify-center group-data-collapsed/shell:lg:px-0',
        'bg-gray-700' => $active,
    ])>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{{ $icon }}</svg>
        <span class="flex-1 group-data-collapsed/shell:lg:hidden">{{ $label }}</span>
        <svg class="h-3 w-3 shrink-0 transition-transform group-open/nav:rotate-180 group-data-collapsed/shell:lg:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
    </summary>

    <div class="mt-0.5 group-data-collapsed/shell:lg:hidden">
        {{ $slot }}
    </div>
</details>
