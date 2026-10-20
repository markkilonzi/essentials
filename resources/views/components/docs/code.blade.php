<figure class="group my-5 overflow-hidden rounded-lg bg-secondary-dark shadow-sm ring-1 ring-black/5">
    <figcaption class="flex items-center justify-between gap-3 border-b border-white/10 bg-secondary px-4 py-2 text-xs text-gray-300">
        <span class="flex items-center gap-2">
            <span class="flex gap-1" aria-hidden="true"><span class="h-2.5 w-2.5 rounded-full bg-red-400/70"></span><span class="h-2.5 w-2.5 rounded-full bg-primary/80"></span><span class="h-2.5 w-2.5 rounded-full bg-green-400/70"></span></span>
            <span class="ml-1 font-medium">{{ $title ?? strtoupper($lang) }}</span>
        </span>
        <button type="button" data-docs-copy class="rounded bg-white/10 px-2 py-0.5 text-xs text-white transition hover:bg-white/20">Copy</button>
    </figcaption>
    <pre class="overflow-x-auto text-[13px] leading-relaxed"><code class="language-{{ $language() }} block p-4">{{ $dedent($slot) }}</code></pre>
</figure>
