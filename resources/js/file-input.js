/**
 * Register the `fileInput` Alpine component used by `<x-essentials::file-input>`: it previews the chosen
 * file (name, size and, for an image, a thumbnail) and falls back to the file the record already has.
 */

/**
 * Add an alpine:init event listener to the document
 */
document.addEventListener('alpine:init', () => {
    /**
     * Get the state of a file input from the file the record already has
     */
    Alpine.data('fileInput', (config) => ({
        currentUrl: config.currentUrl,
        currentName: config.currentName,
        currentSize: config.currentSize,
        currentIsImage: config.currentIsImage,
        previewUrl: null,
        fileName: null,
        fileSize: null,
        isImage: false,
        fileSelected: false,

        /**
         * Get the name to show
         */
        get displayName() {
            return this.fileName || this.currentName;
        },

        /**
         * Get the size to show
         */
        get displaySize() {
            return this.fileSize || this.currentSize;
        },

        /**
         * Get whether to show the thumbnail
         */
        get showImage() {
            return this.isImage || (!this.fileSelected && this.currentIsImage);
        },

        /**
         * Get the thumbnail source
         */
        get imageSrc() {
            return this.previewUrl || this.currentUrl;
        },

        /**
         * Show the chosen file, or the current one when the choice is cleared
         */
        onFileSelected(event) {
            //  Get the chosen file
            const file = event.target.files[0];

            //  Revoke any previous object URL to avoid leaking memory
            if (this.previewUrl) {
                URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = null;
            }

            //  Fall back to the current file when the choice is cleared
            if (!file) {
                this.fileSelected = false;
                this.fileName = null;
                this.fileSize = null;
                this.isImage = false;
                return;
            }

            //  Show the chosen file
            this.fileSelected = true;
            this.fileName = file.name;
            this.fileSize = this.formatBytes(file.size);
            this.isImage = file.type.startsWith('image/');

            //  Only build a preview URL for previewable image types
            if (this.isImage) this.previewUrl = URL.createObjectURL(file);
        },

        /**
         * Turn a byte count into "1.5 MB"
         */
        formatBytes(bytes) {
            //  Return zero for an empty file
            if (!bytes) return '0 B';

            //  Pick the unit
            const units = ['B', 'KB', 'MB', 'GB'];
            const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);

            //  Format the size
            return `${(bytes / (1024 ** exponent)).toFixed(exponent ? 1 : 0)} ${units[exponent]}`;
        },
    }));
});
