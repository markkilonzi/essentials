@props(['labels'])

<div x-data="{ active: @js($labels[0]) }" class="my-5">
    {{--  Tab buttons  --}}
    <div class="flex flex-wrap gap-1 border-b border-gray-200" role="tablist">
        @foreach ($labels as $label)
            <button type="button" role="tab" x-on:click="active = @js($label)" x-bind:aria-selected="active === @js($label)" x-bind:class="active === @js($label) ? 'border-primary font-semibold text-primary-darker' : 'border-transparent text-gray-500 hover:text-secondary-dark'" class="-mb-px border-b-2 px-3 py-2 text-sm transition">{{ $label }}</button>
        @endforeach
    </div>

    {{--  The panel of the chosen tab  --}}
    <div>
        {{ $slot }}
    </div>
</div>
