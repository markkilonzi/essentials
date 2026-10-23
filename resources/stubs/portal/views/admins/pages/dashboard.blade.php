@use('Nyumbani\Docs\DocsRegistry')

@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="p-6">
        {{--  Greeting  --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold text-secondary-dark">Good to see you, {{ str(user()->name)->before(' ') }}</h2>
                <p class="mt-1 text-gray-500">Your portal is ready. Pick up where the packages leave off.</p>
            </div>
            <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-medium capitalize text-primary-dark">{{ app()->environment() }} environment</span>
        </div>

        {{--  Quick links to the docs, which the packages serve only locally  --}}
        <h3 class="mb-3 mt-8 text-xs font-semibold uppercase tracking-wide text-gray-500">Quick links</h3>

        @if (app()->isLocal())
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (app(DocsRegistry::class)->all() as $docs)
                    <a href="{{ $docs->url() }}" target="_blank" rel="noopener" class="group flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-xs transition hover:border-primary hover:shadow-sm">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-semibold text-secondary-dark">{{ $docs->name }}</span>
                            <span class="mt-0.5 block text-xs text-gray-500">{{ $docs->pages()->count() }} pages with examples you can run</span>
                        </span>

                        <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-hover:translate-x-0.5 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        @else
            <p class="rounded-lg border border-gray-200 bg-white p-4 text-gray-500 shadow-xs">The docs are served only in the local environment.</p>
        @endif
    </div>
@endsection
