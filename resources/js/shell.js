/**
 * Run the shared Nyumbani layout: open the sidebar as a drawer on small screens, collapse it to icons
 * on large screens, close the drawer on a link tap, a backdrop tap or Escape, expand a collapsed
 * sidebar from a group header, and close the user menu on an outside click or Escape. The state lives
 * in the `data-open` and `data-collapsed` attributes of the `[data-shell]` element, which the layout
 * styles read, so this is a few delegated listeners on `document` with no dependency on Alpine or any
 * Blade component.
 */

/**
 * Width (px) from which the sidebar is a fixed desktop column instead of a drawer
 */
const DESKTOP_BREAKPOINT = 1024;

/**
 * Close every open user menu, except the one that holds the given element
 */
const closeUserMenus = (inside = null) => {
    //  Loop through the open user menus
    document.querySelectorAll('details[data-menu][open]').forEach((menu) => {
        //  Keep the menu that holds the element
        if (inside && menu.contains(inside)) return;

        //  Close the menu
        menu.removeAttribute('open');
    });
};

/**
 * Add a click event listener to the document
 */
document.addEventListener('click', (event) => {
    //  Get the shell, which only exists on pages that use the shared layout
    const shell = document.querySelector('[data-shell]');

    //  Ignore pages without the shared layout
    if (!shell) return;

    //  Check whether the screen is wide enough for the desktop sidebar
    const isDesktop = window.matchMedia(`(min-width: ${DESKTOP_BREAKPOINT}px)`).matches;

    //  The menu button collapses the sidebar on desktop and opens the drawer on small screens
    if (event.target.closest('[data-shell-toggle]')) {
        shell.toggleAttribute(isDesktop ? 'data-collapsed' : 'data-open');
    }

    //  The close button and the backdrop close the drawer
    if (event.target.closest('[data-shell-close]')) {
        shell.removeAttribute('data-open');
    }

    //  Tapping a link inside the drawer closes it
    if (!isDesktop && event.target.closest('aside nav a')) {
        shell.removeAttribute('data-open');
    }

    //  A group header on the collapsed sidebar expands it, so the submenu is not hidden behind the icon
    if (isDesktop && event.target.closest('summary[data-nav-group]')) {
        shell.removeAttribute('data-collapsed');
    }

    //  Close the user menu when the click lands outside it
    closeUserMenus(event.target);
});

/**
 * Add a keydown event listener to the document
 */
document.addEventListener('keydown', (event) => {
    //  Only the Escape key closes anything
    if (event.key !== 'Escape') return;

    //  Close the drawer
    document.querySelector('[data-shell]')?.removeAttribute('data-open');

    //  Close the user menu
    closeUserMenus();
});
