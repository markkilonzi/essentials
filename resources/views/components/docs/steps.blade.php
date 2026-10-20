@props(['steps'])

<div x-data="{ current: 0, count: {{ count($steps) }} }" class="my-5 grid gap-1.5 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
    {{--  The steps, in order  --}}
    <ol class="space-y-1">
        @foreach ($steps as $step)
            <li>
                <button type="button" x-on:click="current = {{ $loop->index }}" x-bind:class="current === {{ $loop->index }} ? 'bg-primary/10 text-primary-darker' : 'text-gray-600 hover:bg-gray-50'" class="flex w-full items-start gap-3 rounded-md px-3 py-2 text-left text-sm transition">
                    <span x-bind:class="current >= {{ $loop->index }} ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500'" class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-semibold">{{ $loop->iteration }}</span>
                    <span>
                        <span class="block font-semibold">{{ $step['title'] }}</span>
                        <span class="block text-xs text-gray-400">{{ $step['actor'] }}</span>
                    </span>
                </button>
            </li>
        @endforeach
    </ol>

    {{--  The detail of the chosen step  --}}
    <div class="flex min-w-0 flex-col rounded-md bg-gray-50 p-4">
        @foreach ($steps as $step)
            <div x-show="current === {{ $loop->index }}" x-cloak class="flex-1">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Step {{ $loop->iteration }} of {{ count($steps) }}</p>
                <p class="mt-1 text-sm leading-relaxed text-gray-700">{{ $step['detail'] }}</p>
                @isset($step['code'])
                    <pre class="mt-3 overflow-x-auto rounded-md bg-secondary-dark p-3 text-xs leading-relaxed text-gray-200"><code>{{ $step['code'] }}</code></pre>
                @endisset
            </div>
        @endforeach

        <div class="mt-4 flex justify-between">
            <button type="button" class="btn btn-primary btn-sm" x-on:click="current = Math.max(0, current - 1)" x-bind:disabled="current === 0">Back</button>
            <button type="button" class="btn btn-primary btn-sm" x-on:click="current = Math.min(count - 1, current + 1)" x-bind:disabled="current === count - 1">Next</button>
        </div>
    </div>
</div>
