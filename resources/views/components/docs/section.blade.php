@props(['id', 'title'])

<section id="{{ $id }}" class="mt-12 scroll-mt-24 first:mt-0" data-docs-section>
    <h2 class="group mb-3 flex items-center gap-2 border-b border-gray-200 pb-2 text-2xl font-semibold text-secondary-dark">
        {{ $title }}
        <a href="#{{ $id }}" class="text-primary opacity-0 transition group-hover:opacity-100" aria-label="Link to {{ $title }}">#</a>
    </h2>
    <div class="docs-prose">
        {{ $slot }}
    </div>
</section>
