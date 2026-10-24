/**
 * Guard every submit button against double submission. Opt out with `data-allow-resubmit`.
 * The button is never disabled, since a disabled button drops its name and value from the form.
 */
document.addEventListener('submit', (event) => {
    //  Skip a cancelled submission
    if (event.defaultPrevented) return;

    //  Get the form
    const form = event.target;

    //  Block a repeat submission of a busy form
    if ('submitting' in form.dataset) {
        event.preventDefault();
        return;
    }

    //  Skip a form that opens in a new tab
    if (form.target === '_blank') return;

    //  Get the clicked button
    const submitter = event.submitter;

    //  Skip no button, an opt-out and a disabled button
    if (!submitter || 'allowResubmit' in submitter.dataset || submitter.disabled) return;

    //  Mark the form busy
    form.dataset.submitting = '';

    //  Get the button size
    const { width, height } = submitter.getBoundingClientRect();

    //  Keep the original style and content
    const originalStyle = {
        position: submitter.style.position,
        width: submitter.style.width,
        height: submitter.style.height,
        pointerEvents: submitter.style.pointerEvents,
    };
    const originalHtml = submitter.innerHTML;

    //  Lock the size and ignore the pointer
    submitter.style.position = 'relative';
    submitter.style.width = `${width}px`;
    submitter.style.height = `${height}px`;
    submitter.style.pointerEvents = 'none';

    //  Look disabled to assistive tech and the keyboard
    submitter.setAttribute('aria-busy', 'true');
    submitter.setAttribute('aria-disabled', 'true');
    submitter.setAttribute('tabindex', '-1');

    //  Show the spinner
    submitter.innerHTML = `
        <span class="absolute inset-0 flex items-center justify-center">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </span>
    `;

    //  Restore the button and the form
    const resetButtonState = () => {
        delete form.dataset.submitting;
        submitter.removeAttribute('aria-busy');
        submitter.removeAttribute('aria-disabled');
        submitter.removeAttribute('tabindex');
        submitter.style.position = originalStyle.position;
        submitter.style.width = originalStyle.width;
        submitter.style.height = originalStyle.height;
        submitter.style.pointerEvents = originalStyle.pointerEvents;
        submitter.innerHTML = originalHtml;
    };

    //  Restore on a validation block
    form.addEventListener('invalid', resetButtonState, { capture: true, once: true });

    //  Restore on reset-loading
    form.addEventListener('reset-loading', resetButtonState, { once: true });

    //  Restore on back/forward cache
    window.addEventListener('pageshow', (e) => {
        if (e.persisted) resetButtonState();
    }, { once: true });
});
