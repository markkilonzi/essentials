@extends('essentials::layouts.app')

@section('body')
    <div class="min-h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('{{ route('essentials.image', 'auth-background.jpg') }}')">
        <div class="flex min-h-screen items-center justify-center bg-secondary-dark/60 p-4 backdrop-blur-[2px]">
            <div class="grid w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl lg:grid-cols-5">

                {{--  Brand panel  --}}
                <aside class="relative hidden flex-col justify-between overflow-hidden bg-linear-to-br from-secondary to-secondary-dark p-10 text-white lg:col-span-2 lg:flex">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-primary/20 blur-2xl"></div>
                    <div class="pointer-events-none absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-primary-dark/30 blur-3xl"></div>

                    <div class="relative flex h-14 w-14 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                        <img src="{{ route('essentials.image', 'icon-secondary.png') }}" alt="{{ config('app.name') }}" class="h-8 w-8">
                    </div>

                    <div class="relative">
                        <h2 class="text-2xl font-semibold leading-snug">{{ config('app.name') }}</h2>
                        @if (config('essentials.branding.description'))
                            <p class="mt-3 text-sm leading-relaxed text-white/70">{{ config('essentials.branding.description') }}</p>
                        @endif
                        <span class="mt-6 block h-1 w-12 rounded-full bg-primary"></span>
                    </div>
                </aside>

                {{--  Form panel  --}}
                <main class="px-6 py-10 sm:px-12 lg:col-span-3">
                    <div class="mb-8">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-secondary-dark lg:hidden">
                            <img src="{{ route('essentials.image', 'icon-secondary.png') }}" alt="{{ config('app.name') }}" class="h-8 w-8">
                        </div>
                        <h1 class="text-3xl font-semibold text-secondary-dark">@yield('heading')</h1>
                        <p class="mt-2 text-gray-500">@yield('description')</p>
                    </div>

                    @yield('content')
                </main>
            </div>
        </div>
    </div>
@endsection
