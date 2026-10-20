@props(['demo', 'fields', 'title' => 'Try it', 'manual' => null])

<div x-data="docsPlayground({ url: @js(route('essentials.docs.demo', ['package' => request()->route('package'), 'demo' => $demo])), fields: @js($fields), manual: @js($manual !== null) })" class="my-5 overflow-hidden rounded-lg border border-primary/40 bg-white shadow-sm">
    {{--  Heading  --}}
    <div class="flex items-center justify-between gap-3 bg-primary/10 px-4 py-2">
        <p class="flex items-center gap-2 text-sm font-semibold text-primary-darker">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
            {{ $title }}
        </p>
        <p class="text-xs text-primary-darker/70">@if ($manual) Runs only when you press the button. @else Runs the package's real code. Nothing is saved or sent. @endif</p>
    </div>

    <div class="space-y-4 p-4">
        {{--  What the reader types  --}}
        @if (count($fields) > 0)
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($fields as $field)
                    <div class="md:[&:last-child:nth-child(odd)]:col-span-2">
                        <label for="{{ $demo }}-{{ $field['name'] }}">{{ $field['label'] }}</label>
                        @if (($field['type'] ?? 'text') === 'select')
                            <select id="{{ $demo }}-{{ $field['name'] }}" x-model="values.{{ $field['name'] }}" x-on:change="schedule()">
                                @foreach ($field['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif (($field['type'] ?? 'text') === 'datetime')
                            <input id="{{ $demo }}-{{ $field['name'] }}" type="datetime-local" x-model="values.{{ $field['name'] }}" x-on:input="schedule()" x-on:change="schedule()">
                        @else
                            <input id="{{ $demo }}-{{ $field['name'] }}" type="text" x-model="values.{{ $field['name'] }}" x-on:input="schedule()" placeholder="{{ $field['placeholder'] ?? '' }}" autocomplete="off">
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{--  What the package answers, across the full width  --}}
        <div class="min-w-0 rounded-md border border-gray-200 bg-gray-50" aria-live="polite">
            <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Result</p>
                @if ($manual)
                    <button type="button" class="btn btn-primary btn-sm" x-on:click="run()">{{ $manual }}</button>
                @else
                    <button type="button" class="text-xs font-medium text-primary hover:underline" x-on:click="run()">Run again</button>
                @endif
            </div>

            <div class="space-y-3 p-3">
                <p x-show="error" x-cloak x-text="error" class="text-sm text-red-700"></p>

                <p x-show="! error && rows.length === 0" x-cloak class="text-sm text-gray-400">Nothing yet.</p>

                <template x-for="row in rows" x-bind:key="row.label">
                    <div>
                        {{--  JSON gets its own panel: coloured, scrollable and copyable  --}}
                        <div x-show="isJson(row.value)" x-cloak class="overflow-hidden rounded-md bg-secondary-dark shadow-sm ring-1 ring-black/5">
                            <div class="flex items-center justify-between gap-3 border-b border-white/10 bg-secondary px-3 py-1.5 text-xs text-gray-300">
                                <span class="flex items-center gap-2">
                                    <span class="rounded bg-white/10 px-1.5 py-0.5 font-mono text-[10px] uppercase tracking-wider text-gray-200">json</span>
                                    <span x-text="row.label"></span>
                                </span>
                                <button type="button" class="rounded bg-white/10 px-2 py-0.5 text-xs text-white transition hover:bg-white/20" x-on:click="copy(row.value, $el)">Copy</button>
                            </div>
                            <pre class="max-h-96 overflow-auto p-4 text-[13px] leading-relaxed text-gray-200"><code x-html="highlight(row.value)"></code></pre>
                        </div>

                        {{--  Anything else is a label and its value  --}}
                        <div x-show="! isJson(row.value)" class="flex flex-col gap-0.5 text-sm sm:flex-row sm:gap-4">
                            <p class="shrink-0 text-gray-500 sm:w-56" x-text="row.label"></p>
                            <p class="min-w-0 whitespace-pre-wrap wrap-break-word font-mono text-[13px]" x-bind:class="{ 'text-green-700': row.state === 'pass', 'text-red-700': row.state === 'fail', 'text-secondary-dark': ! row.state }" x-text="row.value"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@pushOnce('js')
    <script>
        document.addEventListener('alpine:init', () => {
            /**
             * Register the playground that posts what a reader types to a docs demo and shows the rows it answers
             */
            window.Alpine.data('docsPlayground', (data) => ({
                url: data.url,
                manual: data.manual,
                values: Object.fromEntries(data.fields.map((field) => [field.name, field.value ?? ''])),
                rows: [],
                error: null,
                timer: null,
                latest: 0,

                /**
                 * Run the demo once the playground appears, unless it only runs on a button press
                 */
                init() {
                    if (! this.manual) this.run();
                },

                /**
                 * Wait for the reader to stop typing, then run the demo
                 */
                schedule() {
                    //  A manual demo waits for its button
                    if (this.manual) return;

                    //  Restart the wait
                    clearTimeout(this.timer);

                    //  Run when typing pauses
                    this.timer = setTimeout(() => this.run(), 250);
                },

                /**
                 * Post the typed values and show the rows that come back
                 */
                async run() {
                    //  Number the request, so a slow answer never replaces a newer one
                    const request = ++this.latest;

                    //  Ask the demo
                    const response = await fetch(this.url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(this.values),
                    });

                    //  Ignore an answer that is no longer the latest
                    if (request !== this.latest) return;

                    //  Show the rows, or why the demo could not run
                    const answer = await response.json().catch(() => ({ error: 'The demo did not answer.' }));
                    this.error = answer.error ?? null;
                    this.rows = answer.rows ?? [];
                },

                /**
                 * Tell whether a value is a JSON object or list, which gets its own panel
                 */
                isJson(value) {
                    return /^\s*[{\[]/.test(value) && (() => {
                        try {
                            JSON.parse(value);

                            return true;
                        } catch (error) {
                            return false;
                        }
                    })();
                },

                /**
                 * Escape JSON and colour its keys, strings, numbers and literals
                 */
                highlight(value) {
                    //  Escape the text, so it cannot become markup
                    const escaped = value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

                    //  Wrap each token in a coloured span
                    return escaped.replace(/("(?:\\.|[^"\\])*")(\s*:)?|\b(true|false|null)\b|(-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?)/g, (match, text, colon, literal, number) => {
                        if (text && colon) return `<span class="text-sky-300">${text}</span>${colon}`;
                        if (text) return `<span class="text-green-300">${text}</span>`;
                        if (literal) return `<span class="text-purple-300">${literal}</span>`;
                        if (number) return `<span class="text-amber-300">${number}</span>`;

                        return match;
                    });
                },

                /**
                 * Copy a value to the clipboard and say so on the button
                 */
                copy(value, button) {
                    navigator.clipboard.writeText(value).then(() => {
                        button.textContent = 'Copied';
                        setTimeout(() => (button.textContent = 'Copy'), 1500);
                    });
                },
            }));
        });
    </script>
@endPushOnce
