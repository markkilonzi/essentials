{{--  Navbar  --}}
<header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-3 bg-white px-5 shadow-xl">
    <div class="flex items-center">
        <button type="button" data-shell-toggle aria-label="Menu" class="mr-4 cursor-pointer text-secondary transition-colors hover:text-secondary-dark">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="hidden text-lg font-normal uppercase text-gray-700 lg:inline-block">@hasSection('brand')@yield('brand')@else{{ config('app.name') }}@endif</span>
    </div>

    <div class="flex min-w-0 items-center gap-3">
        @yield('navbar')

        <details data-menu class="relative">
            <summary class="flex cursor-pointer list-none items-center marker:hidden hover:text-gray-900">
                <span class="mr-2 whitespace-nowrap text-base">{{ user()->name }}</span>
                <svg class="h-4 w-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a1 1 0 0 1 1.41 0L10 10.59l3.36-3.38a1 1 0 1 1 1.41 1.42l-4.06 4.08a1 1 0 0 1-1.41 0L5.23 8.63a1 1 0 0 1 0-1.42z"/></svg>
            </summary>

            <div class="absolute right-0 top-full z-50 mt-2 w-48 rounded-md border border-gray-200 bg-white p-0.5 drop-shadow-xl">
                @yield('user-menu')

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-1.5 text-left text-base capitalize text-gray-800 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </details>
    </div>
</header>
