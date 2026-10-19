import ui from '@alpinejs/ui';

/**
 * Add an alpine:init event listener to the document
 */
document.addEventListener('alpine:init', () => {
    //  Register the plugin once, in case the app registered it too
    if (! window.Alpine.__essentialsUi) {
        window.Alpine.plugin(ui);
        window.Alpine.__essentialsUi = true;
    }

    /**
     * Get the state of a modal from its name, the url of its body and whether to reopen it
     */
    window.Alpine.data('essentialsModal', (config) => ({
        open: false,
        loaded: false,
        loading: false,
        failed: false,
        confirming: false,
        data: {},

        /**
         * Reopen after a failed save and load the body on the first open
         */
        init() {
            //  Load the remote body the first time the modal opens
            this.$watch('open', (isOpen) => {
                if (isOpen && config.src) this.load();

                //  Start a confirmation modal on its form every time it opens
                this.back();
            });

            //  Reopen when the page came back with errors for this modal
            if (config.reopen) this.$nextTick(() => (this.open = true));
        },

        /**
         * Move to the review once the browser accepts the form, keeping what was typed for the review to show
         */
        advance() {
            //  Find the form in the body
            const form = this.$refs.body.querySelector('form');

            //  Stay on the form when the browser rejects a field, which shows its own message
            if (! form || ! form.reportValidity()) return;

            //  Keep the values for the review
            this.data = Object.fromEntries(new FormData(form));
            this.confirming = true;
        },

        /**
         * Return to the form
         */
        back() {
            this.confirming = false;
            this.data = {};
        },

        /**
         * Treat a submit during the form step, such as the Enter key, as Continue, so the review is never skipped
         */
        submitted(event) {
            if (config.confirm && ! this.confirming) {
                event.preventDefault();
                this.advance();
            }
        },

        /**
         * Open when the event names this modal
         */
        show(name) {
            if (config.name && name === config.name) this.open = true;
        },

        /**
         * Close when the event names this modal
         */
        hide(name) {
            if (config.name && name === config.name) this.open = false;
        },

        /**
         * Fetch the body once, and again only after a failure
         */
        async load() {
            //  Skip when it is loaded or loading
            if (this.loaded || this.loading) return;

            //  Show the spinner
            this.loading = true;
            this.failed = false;

            try {
                //  Fetch the html
                const response = await fetch(config.src, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' } });

                //  Fail on an error status
                if (!response.ok) throw new Error(response.status);

                //  Put the html in the body, which Alpine then initialises
                this.$refs.remote.innerHTML = await response.text();
                this.loaded = true;
            } catch (error) {
                this.failed = true;
            }

            //  Hide the spinner
            this.loading = false;
        },
    }));
});
