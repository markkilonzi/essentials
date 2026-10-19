---
paths:
  - 'resources/views/**'
---

# Views

## Audience-first folders
Views are grouped by audience: `admins/{pages,partials}`, `system-admins/{pages,partials}`, with shared `layouts` and `components`. Modal components live in `components/{audience}/modal`.

## Pages use the shared layout
Signed-in pages `@extends('essentials::layouts.portal')`, and sign-in pages `@extends('essentials::layouts.guest')`; both extend the common `essentials::layouts.app`. A portal page fills its sections: `title`, `sidebar`, `content`, plus optional `brand`, `navbar`, `user-menu` and `banner`, and `@push('actions')`. The page content section is always called `content`. The layout sends a guest to login as a safety net; routes are still protected with the `auth` middleware. While a user is impersonated, the layout shows a bar under the page header; its Leave button posts to the package's `essentials.impersonation.leave` route, which returns the user to their home route. Build the sidebar from `<x-essentials::nav-link>` and `<x-essentials::nav-group>` in the app's own partial.

## Flash messages are toastr toasts
The shared layout raises each flash message with toastr. The app installs `toastr` with npm and imports the package setup once in its js: `import '../../vendor/nyumbani/essentials/resources/js/toastr';`. Do not write your own toast setup.

## The shell script comes from the package
The sidebar, drawer and user menu are run by the package's `shell.js`, which the app imports once in its js after toastr: `import '../../vendor/nyumbani/essentials/resources/js/shell';`. It uses no Alpine. The double-submit guard is `submit.js`, imported the same way: `import '../../vendor/nyumbani/essentials/resources/js/submit';`.

## Formatting helpers and the file input come from the package
The app imports `helpers.js` once in its js (`import '../../vendor/nyumbani/essentials/resources/js/helpers';`) and installs `pluralize` with npm; it defines `money`, `numeric`, `parseNumber`, `pluralize`, `prettify`, `formatDate` and `formatDateTime` on `window`, mirroring the PHP helpers, so do not write your own `$store.helpers`. For file uploads use `<x-essentials::file-input>`, with `file-input.js` imported the same way: `import '../../vendor/nyumbani/essentials/resources/js/file-input';`.

## Auth pages come from the package
Login, forgot-password and reset-password are the same in every Nyumbani app and are provided by `nyumbani/essentials`. Do not build your own.

## Anonymous components
Build reusable markup as anonymous `<x-*>` components with `@props()`. Never create class-based components in `app/View/Components`. Reserve `@include` for layout glue (sidebar, navbar, alerts). A component that needs a short script carries it at the bottom of its file in `@pushOnce('js')`, so it works wherever it is used. A component with a long script keeps it in a file in `resources/js`, registered with `Alpine.data` inside `alpine:init`, which the app imports once.

## No inline PHP
Never write `@php ... @endphp` blocks to compute data. Move it to the controller, a view composer (registered in `ViewServiceProvider`), or the owning DataTable class. Reading `$errors` or `old()` inline is fine. Never inline a fully-qualified class name; add `@use('Vendor\Class')` at the top of the file.

## Plain English copy
Blade view copy is written as plain English text, with no `__()` or `@lang()` wrapping. Only flash messages go through the `alerts.*` translation keys.

## Blade directives for formatting
Use `@date`, `@time`, `@datetime`, `@dateforhumans`, `@numeric`, `@currency`, `@percentage`, `@prettify`, `@bag` and `@money` instead of raw `format()`, `date()` or `number_format()`. Never write a literal "KES" or "Ksh", even in placeholders (use `e.g. 1000`).

## Blade syntax
Use `@class()` for conditional classes, `@selected` / `@checked` for form state, `:$var` shorthand when names match, self-closing tags for slot-less components, `@js()` to pass PHP data to Alpine, and `@push('js')` for inline scripts. Nest content one level deeper than the enclosing directive. Pluralize with `str()->plural()` and build links with `route()`.

## Forms
Every text-like input has a helpful placeholder: an example, the expected format, or what a blank value means, never just the label. Required labels carry `aria-required="true"`. Forms never carry a hidden tenant id input.

## Double-submit guard and confirmations
Every submit button is disabled and shows a spinner once its form is submitted, by the package's `submit.js`; do not add your own guard. Opt a button out with `data-allow-resubmit` only when something else runs its busy state (the package has none). Dispatch `reset-loading` on the form to restore a button after an AJAX submit. Destructive actions ask for confirmation, with `x-on:submit="if (! confirm('...')) $event.preventDefault()"` on delete forms. Never call `form.submit()`; use `requestSubmit()`, and never from inside a `submit` listener on the same form.

## Alpine
Keep inline `x-data="{ ... }"` for trivial state only (a couple of keys). Register anything larger with `Alpine.data('name', () => ({ ... }))` inside `document.addEventListener('alpine:init', ...)` in a `@push('js')` block at the bottom of the same file, then use `x-data="name"`. Components are functions taking a `data` param with destructured defaults, use `init()`, `$watch()`, `$store.helpers`, getters, and `x-on:` instead of `@`.

## Design tokens
Import the shared theme from the package in the app's Tailwind css (`@import '../../vendor/nyumbani/essentials/resources/css/theme.css';`) and use its primary and secondary colour scales. Do not define your own palette. Buttons use `.btn` with `.btn-primary` or `.btn-secondary` from the shared theme (plus `.btn-sm` or `.btn-lg`), never raw colour utilities. Design mobile-first and meet WCAG AA.
