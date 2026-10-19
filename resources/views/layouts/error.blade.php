@extends('essentials::layouts.app')

@section('title', trim($__env->yieldContent('code')).' · '.trim($__env->yieldContent('heading')))
@section('body-class', 'h-full bg-secondary-dark text-sm font-light text-white antialiased')

@section('body')
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">

        {{--  Drifting colour blobs  --}}
        <div class="pointer-events-none absolute -left-24 -top-24 h-96 w-96 animate-pulse rounded-full bg-primary/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-20 h-96 w-96 animate-pulse rounded-full bg-primary-dark/30 blur-3xl [animation-delay:1s]"></div>
        <div class="pointer-events-none absolute left-1/2 top-1/3 h-64 w-64 -translate-x-1/2 animate-pulse rounded-full bg-secondary-light/30 blur-3xl [animation-delay:2s]"></div>

        <main class="relative w-full max-w-xl text-center">

            {{--  Logo  --}}
            <a href="{{ url('/') }}" class="mb-6 inline-flex items-center gap-3" aria-label="{{ config('app.name') }}">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                    <img src="{{ route('essentials.image', 'icon-secondary.png') }}" alt="" class="h-7 w-7">
                </span>
                <span class="text-xl font-semibold">{{ config('app.name') }}</span>
            </a>

            {{--  The big code, with the icon bouncing over the middle digit  --}}
            <div class="relative mb-2 select-none">
                <p class="bg-linear-to-b from-primary-light to-primary bg-clip-text text-[9rem] font-bold leading-none tracking-tighter text-transparent sm:text-[12rem]">@yield('code')</p>
                <div class="absolute inset-x-0 top-1/2 flex -translate-y-1/2 justify-center">
                    <span class="flex h-20 w-20 animate-bounce items-center justify-center rounded-2xl bg-white/10 text-white shadow-2xl ring-1 ring-white/30 backdrop-blur-sm sm:h-24 sm:w-24">
                        <svg class="h-10 w-10 sm:h-12 sm:w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">@yield('icon')</svg>
                    </span>
                </div>
            </div>

            <h1 class="text-3xl font-semibold sm:text-4xl">@yield('heading')</h1>
            <p class="mx-auto mt-3 max-w-md text-base leading-relaxed text-white/70">@yield('detail')</p>

            {{--  Actions  --}}
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @hasSection('actions')
                    @yield('actions')
                @endif
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 font-medium text-secondary-dark shadow-lg transition hover:bg-primary-light focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                    Take me home
                </a>
                <button type="button" onclick="history.length > 1 ? history.back() : location.assign('{{ url('/') }}')" class="inline-flex items-center gap-2 rounded-md px-5 py-2.5 font-medium text-white ring-1 ring-white/30 transition hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                    Go back
                </button>
            </div>

            <p class="mt-10 text-xs text-white/40">{{ config('app.name') }}</p>
        </main>
    </div>
@endsection
