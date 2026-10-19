{{--  Sidebar: a drawer below lg, a fixed column from lg that collapses to icons  --}}
<aside class="fixed inset-y-0 left-0 z-40 w-44 -translate-x-full overflow-y-auto overflow-x-hidden bg-secondary-dark text-gray-400 shadow-2xl transition-all duration-300 ease-in-out group-data-open/shell:translate-x-0 lg:translate-x-0 lg:shadow-none group-data-collapsed/shell:lg:w-16">
    <div class="relative flex h-16 items-center justify-center bg-secondary px-3 lg:justify-center">
        <a href="{{ user() instanceof \Nyumbani\Essentials\Contracts\HasHomeRoute ? user()->homeRoute() : url('/') }}" class="flex items-center gap-x-2">
            <img src="{{ route('essentials.image', 'icon-secondary.png') }}" alt="{{ config('app.name') }}" class="h-8 w-8 object-contain">
            <span class="text-xs font-bold uppercase tracking-[.15rem] text-white group-data-collapsed/shell:lg:hidden">{{ config('app.name') }}</span>
        </a>
    </div>

    <div class="relative mb-1 flex h-14 items-center justify-center bg-cover bg-center px-4 text-white group-data-collapsed/shell:lg:px-0" style="background-image: url('{{ route('essentials.image', 'shell-sidebar.jpeg') }}')">
        <div class="absolute inset-0 bg-primary-dark/70"></div>
        <button type="button" data-shell-close aria-label="Close menu" class="absolute right-2 top-2 z-10 flex h-6 w-6 items-center justify-center rounded-full bg-primary-dark text-white lg:hidden">
            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="relative h-10 w-10 rounded-full bg-white p-1">
            <img src="{{ user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(user()->name).'&size=128&rounded=true' }}" alt="{{ user()->name }}" class="h-full w-full rounded-full object-cover">
        </div>
    </div>

    <nav class="px-1 pb-4">
        @yield('sidebar')
    </nav>
</aside>

{{--  Backdrop: tapping it closes the drawer on phones and tablets  --}}
<div data-shell-close class="fixed inset-0 z-30 hidden bg-black/40 group-data-open/shell:block lg:hidden"></div>
