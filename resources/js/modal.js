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
     * Get the state of a modal from its name, the url of its body and whether to reopen it.
     *
     * The state lives under one key, modal, so an app that puts its own x-data and x-model around the modal can
     * never clash with it: the only other names are data, which the review step reads, and init, which Alpine calls.
     */
    window.Alpine.data('essentialsModal', (config) => {
        //  The component, once Alpine has created it, so the methods below can reach $refs, $watch and data
        let component;

        return {
            //  What the form held when the review opened, read by the review as data.name
            data: {},

            modal: {
                open: false,
                loaded: false,
                loading: false,
                failed: false,
                confirming: false,

                /**
                 * Move to the review once the browser accepts the form, keeping what was typed for the review to show
                 */
                advance() {
                    //  Find the form in the body
                    const form = component.$refs.body.querySelector('form');

                    //  Stay on the form when the browser rejects a field, which shows its own message
                    if (! form || ! form.reportValidity()) return;

                    //  Keep the values for the review
                    component.data = Object.fromEntries(new FormData(form));

                    //  Show the review the form carries, when the page gave none in the slot
                    this.fillReview();
                    this.confirming = true;
                },

                /**
                 * Copy the content of the form's data-review block into the review step, where Alpine initialises it
                 */
                fillReview() {
                    //  Find the block in the body, which may be a form loaded from a remote url
                    const block = component.$refs.body.querySelector('[data-review]');

                    //  Leave a review the slot gave alone, which the review step marks with data-slotted
                    const review = component.$refs.review;

                    if (! block || ! review || review.dataset.slotted === 'true') return;

                    review.innerHTML = block.innerHTML;
                },

                /**
                 * Return to the form
                 */
                back() {
                    //  Remember whether the review was showing, since only then is there a copy to clear
                    const wasConfirming = this.confirming;

                    this.confirming = false;
                    component.data = {};

                    //  Clear a review copied from the form, so the next one starts fresh
                    if (wasConfirming && component.$refs.review && component.$refs.review.dataset.slotted !== 'true') component.$refs.review.innerHTML = '';
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
                        component.$refs.remote.innerHTML = await response.text();
                        this.loaded = true;
                    } catch (error) {
                        this.failed = true;
                    }

                    //  Hide the spinner
                    this.loading = false;
                },
            },

            /**
             * Reopen after a failed save and load the body on the first open
             */
            init() {
                //  Keep the component for the methods
                component = this;

                //  Load the remote body the first time the modal opens
                this.$watch('modal.open', (isOpen) => {
                    if (isOpen && config.src) this.modal.load();

                    //  Start a confirmation modal on its form every time it opens
                    this.modal.back();
                });

                //  Reopen when the page came back with errors for this modal
                if (config.reopen) this.$nextTick(() => (this.modal.open = true));
            },
        };
    });
});
