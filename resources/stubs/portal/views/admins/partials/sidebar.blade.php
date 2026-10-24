@use('Nyumbani\Docs\DocsRegistry')

{{--  A plain link: active on its own page  --}}
<x-essentials::nav-link :href="user()->homeRoute()" :active="request()->url() === user()->homeRoute()" label="Dashboard">
    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
</x-essentials::nav-link>

{{--  The docs of each package, which the packages serve only locally  --}}
@if (app()->isLocal() && class_exists(DocsRegistry::class))
    <x-essentials::nav-group label="Docs" :active="request()->is('nyumbani-*/docs*')">
        <x-slot:icon>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </x-slot:icon>

        @foreach (app(DocsRegistry::class)->all() as $docs)
            <x-essentials::nav-link sub target="_blank" rel="noopener" :href="$docs->url()" :active="request()->is($docs->path().'*')" :label="$docs->name" />
        @endforeach
    </x-essentials::nav-group>
@endif
