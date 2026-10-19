/**
 * Prevent double-submission of a form and show a spinner on the button that triggered it. Every submit
 * button is guarded; opt a button out with `data-allow-resubmit`. It works for plain Blade forms,
 * Alpine-driven forms, and DataTable rows rendered client-side, since this is a single delegated
 * listener on `document` with no dependency on Alpine or any Blade component.
 */
document.addEventListener('submit', (event) => {
    //  Respect forms/handlers that already cancelled this submission (e.g. Alpine bulk-action guards)
    if (event.defaultPrevented) return;

    //  Get the form
    const form = event.target;

    //  A form opened in a new tab/window never navigates away on this page, so a disabled
    //  button here would never get reset
    if (form.target === '_blank') return;

    //  event.submitter is the actual button/input that triggered submission (click or Enter key)
    const submitter = event.submitter;

    //  Ignore submissions without a button, and buttons that opted out
    if (!submitter || 'allowResubmit' in submitter.dataset) return;

    //  Ignore disabled buttons
    if (submitter.disabled) return;

    //  Lock in the button's current rendered size so swapping to a spinner doesn't shrink it
    const { width, height } = submitter.getBoundingClientRect();

    //  Remember any inline styles already on the button so they can be restored exactly
    const originalStyle = {
        position: submitter.style.position,
        width: submitter.style.width,
        height: submitter.style.height,
        cursor: submitter.style.cursor,
    };

    //  Apply the locked size and a not-allowed cursor while disabled. Position is set to relative
    //  so the spinner below can be centered with absolute positioning regardless of the button's
    //  own display type, padding, or text-align
    submitter.style.position = 'relative';
    submitter.style.width = `${width}px`;
    submitter.style.height = `${height}px`;
    submitter.style.cursor = 'not-allowed';

    //  Store the original button content so it can be restored later
    const originalHtml = submitter.innerHTML;

    //  Disable the button
    submitter.disabled = true;

    //  Add the aria-busy attribute to the button
    submitter.setAttribute('aria-busy', 'true');

    //  Replace the button content with a centered spinner
    submitter.innerHTML = `
        <span class="absolute inset-0 flex items-center justify-center">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </span>
    `;

    //  Reset the button state
    const resetButtonState = () => {
        submitter.disabled = false;
        submitter.removeAttribute('aria-busy');
        submitter.style.position = originalStyle.position;
        submitter.style.width = originalStyle.width;
        submitter.style.height = originalStyle.height;
        submitter.style.cursor = originalStyle.cursor;
        submitter.innerHTML = originalHtml;
    };

    //  Restore the button if the browser blocks a later resubmission attempt via HTML5 validation
    form.addEventListener('invalid', resetButtonState, { capture: true, once: true });

    //  Escape hatch for future AJAX-driven forms that need to manually reset the button
    form.addEventListener('reset-loading', resetButtonState, { once: true });

    //  A full-page POST navigation can be restored from the back/forward cache with the button
    //  still frozen mid-spinner from before the user left — reset it if this page comes back that way
    window.addEventListener('pageshow', (e) => {
        if (e.persisted) resetButtonState();
    }, { once: true });
});
