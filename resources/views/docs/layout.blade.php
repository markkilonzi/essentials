@extends('essentials::layouts.app')

@section('title', trim($__env->yieldContent('page')).' · Nyumbani '.$docs->name.' docs')
@section('body-class', 'h-full bg-gray-50 text-gray-700 antialiased')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
@endpush

@section('body')
    {{--  Top bar  --}}
    <header class="sticky top-0 z-30 flex h-16 items-center gap-3 bg-secondary-dark px-4 text-white shadow-md lg:px-6">
        <button type="button" data-docs-menu class="-ml-1 rounded p-2 hover:bg-white/10 lg:hidden" aria-label="Open the menu">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" /></svg>
        </button>
        <a href="{{ $docs->url() }}" class="flex items-center gap-3">
            <img src="{{ route('essentials.image', 'icon-secondary.png') }}" alt="Nyumbani" class="h-8 w-8 object-contain">
            <span class="text-sm font-bold uppercase tracking-[.2rem]">Nyumbani</span>
            <span class="rounded-full bg-primary px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wider text-white">{{ $docs->name }} docs</span>
        </a>

        {{--  Switch to the docs of another package  --}}
        <nav class="ml-4 hidden items-center gap-1 text-xs md:flex" aria-label="Packages">
            @foreach ($packages as $package)
                <a href="{{ $package->url() }}" @class(['rounded px-2.5 py-1 transition', 'bg-white/15 font-semibold text-white' => $package->slug === $docs->slug, 'text-gray-300 hover:bg-white/10 hover:text-white' => $package->slug !== $docs->slug])>{{ $package->name }}</a>
            @endforeach
        </nav>
        <div class="ml-auto flex items-center gap-4 text-xs">
            <a href="{{ url('/') }}" class="rounded border border-white/20 px-3 py-1.5 transition hover:bg-white/10">Back to {{ config('app.name') }}</a>
        </div>
    </header>

    <div class="mx-auto flex max-w-screen-2xl">
        {{--  Sidebar: a drawer on small screens  --}}
        <aside data-docs-sidebar class="fixed inset-y-16 left-0 z-20 hidden w-72 overflow-y-auto border-r border-gray-200 bg-white px-4 pb-10 pt-5 shadow-xl lg:sticky lg:top-16 lg:block lg:h-[calc(100vh-4rem)] lg:shadow-none">
            <input type="search" data-docs-filter placeholder="Filter the docs..." class="mb-5 w-full" aria-label="Filter the docs">

            <nav class="space-y-6 text-sm">
                @foreach ($docs->sections() as $section)
                    <div data-docs-group>
                        <p class="mb-1.5 px-2 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ $section['title'] }}</p>
                        <ul class="space-y-0.5">
                            @foreach ($section['pages'] as $slug => $title)
                                <li data-docs-link>
                                    <a href="{{ $docs->url($slug) }}" @class(['block rounded-md border-l-2 px-3 py-1.5 transition', 'border-primary bg-primary/10 font-semibold text-primary-darker' => $slug === $current, 'border-transparent text-gray-600 hover:bg-gray-100 hover:text-secondary-dark' => $slug !== $current])>{{ $title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>
        </aside>

        {{--  Page  --}}
        <main class="min-w-0 flex-1 px-4 py-10 sm:px-8 lg:px-12">
            <article class="mx-auto max-w-3xl">
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-primary-dark">{{ $docs->sections()->first(fn ($section) => array_key_exists($current, $section['pages']))['title'] }}</p>
                <h1 class="text-4xl font-bold tracking-tight text-secondary-dark">@yield('page')</h1>
                <p class="mt-3 text-lg leading-relaxed text-gray-500">@yield('lead')</p>
                <div class="mt-4 h-1 w-16 rounded-full bg-linear-to-r from-primary-light to-primary"></div>

                <div class="mt-10">
                    @yield('content')
                </div>

                {{--  Previous and next page  --}}
                <nav class="mt-16 grid gap-4 border-t border-gray-200 pt-6 sm:grid-cols-2">
                    @foreach (['previous' => 'Previous', 'next' => 'Next'] as $direction => $label)
                        @if ($neighbours[$direction])
                            <a href="{{ $docs->url(array_key_first($neighbours[$direction])) }}" @class(['group rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-primary', 'sm:col-start-2 sm:text-right' => $direction === 'next'])>
                                <span class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</span>
                                <span class="mt-0.5 block font-semibold text-secondary-dark group-hover:text-primary-dark">{{ reset($neighbours[$direction]) }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>

                <p class="mt-10 text-center text-xs text-gray-400">Nyumbani {{ $docs->name }} docs · built for the developers of Nyumbani apps</p>
            </article>
        </main>

        {{--  On this page  --}}
        <aside class="sticky top-16 hidden h-[calc(100vh-4rem)] w-60 shrink-0 overflow-y-auto py-10 pr-6 xl:block">
            <p class="mb-2 text-[11px] font-bold uppercase tracking-widest text-gray-400">On this page</p>
            <ul data-docs-toc class="space-y-1.5 border-l border-gray-200 text-sm"></ul>
        </aside>
    </div>
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            //  Colour the code, when the highlighter could be loaded
            if (window.hljs) document.querySelectorAll('pre code').forEach((block) => window.hljs.highlightElement(block));

            //  Copy a code block
            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-docs-copy]');
                if (!button) return;
                navigator.clipboard.writeText(button.closest('figure').querySelector('code').innerText).then(() => {
                    button.textContent = 'Copied';
                    setTimeout(() => (button.textContent = 'Copy'), 1500);
                });
            });

            //  Open and close the menu on small screens
            const sidebar = document.querySelector('[data-docs-sidebar]');
            document.querySelector('[data-docs-menu]').addEventListener('click', () => sidebar.classList.toggle('hidden'));

            //  Filter the sidebar
            document.querySelector('[data-docs-filter]').addEventListener('input', (event) => {
                const term = event.target.value.toLowerCase();
                document.querySelectorAll('[data-docs-link]').forEach((item) => item.classList.toggle('hidden', !item.textContent.toLowerCase().includes(term)));
                document.querySelectorAll('[data-docs-group]').forEach((group) => group.classList.toggle('hidden', !group.querySelector('[data-docs-link]:not(.hidden)')));
            });

            //  List the sections of the page
            const toc = document.querySelector('[data-docs-toc]');
            document.querySelectorAll('[data-docs-section]').forEach((section) => {
                const item = document.createElement('li');
                item.innerHTML = '<a class="-ml-px block border-l border-transparent py-0.5 pl-3 text-gray-500 hover:border-primary hover:text-primary-dark"></a>';
                item.firstChild.href = '#' + section.id;
                item.firstChild.textContent = section.querySelector('h2').firstChild.textContent.trim();
                toc.appendChild(item);
            });
        });
    </script>
@endpush
