import { defineConfig } from 'vite';
import { resolve } from 'node:path';

/**
 * Bundle every Nyumbani script, as one file (essentials.js) and one file each, with toastr, jQuery, Alpine UI and pluralize inside, into resources/dist,
 * so an app imports one file and installs no npm package. Alpine itself is not bundled: the page's own
 * Alpine (Livewire's) loads the plugin through the alpine:init event.
 */
export default defineConfig({
    build: {
        outDir: 'resources/dist',
        emptyOutDir: true,
        lib: {
            entry: Object.fromEntries(
                ['essentials', 'toastr', 'shell', 'submit', 'file-input', 'modal', 'helpers', 'forms']
                    .map((name) => [name, resolve(import.meta.dirname, `resources/js/${name}.js`)])
            ),
            formats: ['es'],
            fileName: (format, name) => `${name}.js`,
            cssFileName: 'essentials',
        },
    },
});
