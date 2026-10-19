@props(['name', 'label' => null, 'accept' => null, 'current' => null])

<div x-data="fileInput({
    currentUrl: @js($current?->url),
    currentName: @js($current?->title),
    currentSize: @js($current?->formattedSize),
    currentIsImage: @js((bool) $current?->isImage()),
})">
    @if ($label)
        <label for="{{ $attributes->get('id', $name) }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    @endif

    <div class="flex items-center gap-4">
        <img x-show="showImage" x-cloak :src="imageSrc" class="h-16 w-16 rounded object-cover border border-gray-200" alt="">

        <div x-show="!showImage" class="h-16 w-16 flex items-center justify-center rounded border border-gray-200 bg-gray-50 text-gray-400">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
        </div>

        <div class="min-w-0">
            <p class="text-sm text-gray-900 truncate" x-text="displayName || 'No file uploaded'"></p>
            <p class="text-xs text-gray-500" x-show="displaySize" x-text="displaySize"></p>

            <label class="mt-1 inline-block text-xs font-medium text-primary cursor-pointer hover:underline">
                <span x-text="fileSelected ? 'Change file' : 'Upload file'"></span>
                <input
                    type="file"
                    name="{{ $name }}"
                    @if ($accept) accept="{{ $accept }}" @endif
                    {{ $attributes->merge(['id' => $name])->class('sr-only') }}
                    x-on:change="onFileSelected($event)"
                >
            </label>
        </div>
    </div>
</div>
