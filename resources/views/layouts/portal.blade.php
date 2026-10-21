@extends('essentials::layouts.app')

@section('body-class', 'h-full bg-gray-200 text-sm font-light text-gray-700 antialiased')

@section('body')
    <div data-shell class="group/shell relative min-h-full">

        @include('essentials::partials.sidebar')

        <div class="min-w-0 transition-all duration-300 ease-in-out lg:ml-44 group-data-collapsed/shell:lg:ml-16">

            @include('essentials::partials.navbar')

            {{--  Page header and the impersonation bar  --}}
            <div class="sticky top-16 z-10">
                <div class="relative min-h-14 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ route('essentials.image', ['image' => 'shell-header.jpeg']) }}')">
                    <div class="absolute inset-0 bg-black/70"></div>
                    <div class="relative flex min-h-14 flex-wrap items-center justify-between gap-x-4 gap-y-1 px-4 py-2 text-white lg:px-6">
                        <div>
                            <h1 class="text-xl">@yield('title')</h1>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @stack('actions')
                        </div>
                    </div>
                </div>

                @if (method_exists(user(), 'isImpersonated') && user()->isImpersonated())
                    <div class="flex h-9 items-center justify-center gap-x-4 bg-primary-dark px-4 text-center text-xs text-white">
                        <span>Impersonating <span class="font-semibold">{{ user()->name }}</span></span>

                        <form method="POST" action="{{ route('essentials.impersonation.leave') }}">
                            @csrf
                            <button type="submit" class="font-medium underline hover:text-gray-200">Leave</button>
                        </form>
                    </div>
                @endif
            </div>

            {{--  Content  --}}
            <main class="px-2 pb-3.5 pt-1.5">
                @yield('banner')

                {{--  The card every page sits in  --}}
                <div class="relative bg-gray-100 shadow-xs">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
@endsection
