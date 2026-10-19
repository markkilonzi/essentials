/**
 * Run the sign-in pages until Alpine is ready: disable a submit button once its form is submitted and show its busy state. Alpine runs the same states afterwards (the form's `busy` flag), so this steps aside as soon as Alpine reports it is ready. It is a single delegated listener on `document` with no dependency on Alpine or any Blade component.
 */

/**
 * Remember when Alpine is ready, so the stopgap below steps aside
 */
document.addEventListener('alpine:initialized', () => window.__alpineReady = true);

/**
 * Add a submit event listener to all forms
 */
document.addEventListener('submit', (event) => {
    //  Get the submit button of the form, which only the sign-in pages mark with `data-submit`
    const button = event.target.querySelector('[data-submit]');

    //  Ignore forms without one, and everything once Alpine runs the busy state
    if (window.__alpineReady || !button) return;

    //  Disable the button
    button.disabled = true;

    //  Show the busy label and spinner, which stay hidden until Alpine would reveal them
    button
        .querySelectorAll('[x-cloak]')
        .forEach((element) => element.removeAttribute('x-cloak'));

    //  Hide the idle label
    button.querySelector('[data-idle]').style.display = 'none';
});
