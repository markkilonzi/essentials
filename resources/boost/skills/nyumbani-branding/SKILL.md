---
name: nyumbani-branding
description: "Invoke when touching an app's layout, favicons, web manifest, theme color, logo, page title, or the shell (sidebar, navbar, auth and portal layouts) in a Nyumbani app. Skip for component-level styling."
metadata:
  author: nyumbani
---

# Nyumbani Branding and Layouts

The package owns the look and the shell. An app extends it and only supplies content and its sidebar.

## Layouts
- `essentials::layouts.app`: the base document. Includes favicons, the manifest, the theme-color meta, `@vite`, toasts (`essentials::partials.alerts`) and the `styles` and `js` stacks.
- `essentials::layouts.portal`: signed-in shell with sidebar and navbar. Sections: `title`, `content`, `portal.content` (optional, see below), `banner`, `sidebar`, and the `actions` stack.
- `essentials::layouts.auth`: authentication pages only (sign-in, forgot password, reset password), which Essentials already ships. Sections: `heading`, `description`, `content`. Not for other public pages.
- `essentials::layouts.error`: error pages, see the nyumbani-error-pages skill.

Avoid repeating the sidebar include on every page. Create one app layout and extend it from every page:
```blade
@extends('essentials::layouts.portal')

@section('sidebar', 'partials.sidebar')
```
The `sidebar` section takes the name of a view and the portal renders it; markup written inline in the section works too. A portal page with no sidebar throws a `LogicException`, and so does a name that is not a view.

## Favicons and manifest
- Never add favicon files or a favicons partial to an app. The shared set, the dark Nyumbani logo on an amber tile at each size, lives in `resources/images/favicons/` of the package and is served by the `essentials.favicon` route. The layout links them inline.
- The manifest comes from `route('essentials.manifest')` (`WebManifestController`). It uses the app name and `essentials.branding.theme_color`.
- Change the theme color with `ESSENTIALS_THEME_COLOR` or the published config, never in the layout.
- To replace an icon for one app, publish the package images, do not edit `vendor`.

## Theme
Import the shared theme once in the app's css, in place of `@import 'tailwindcss'`. The theme imports Tailwind and loads the `@tailwindcss/forms` plugin, which the package bundles in `resources/dist/forms.js`, so an app has no `@plugin` line and installs only the `tailwindcss` npm package. `nyumbani:install` swaps an existing Tailwind import and forms plugin line for the theme import.
```css
@import '../../vendor/nyumbani/essentials/resources/css/theme.css';
```
Use the tokens `primary`, `primary-dark`, `primary-darker` and `secondary*`. Do not hard-code hex colors for brand surfaces.

## Rules
- Use `@use` for any class in a view and never `@php`.
- Page titles come from `@section('title', ...)`. The layout appends the app name.

## Extending the layouts
There are four, and the Layouts page of the Essentials docs shows the code that extends each one:
- `essentials::layouts.portal`: signed-in pages. Sections `title`, `content`, `sidebar`, and optionally `portal.content`, `brand`, `navbar`, `user-menu`, `banner`; `@push('actions')` for page buttons. Define the sidebar once: the app's `layouts/app.blade.php` extends the portal and sets `@section('sidebar', 'partials.sidebar')`, the partial holds the `nav-link` and `nav-group` items, and every page extends `layouts.app` (never the portal) and fills only its own sections.
- `essentials::layouts.auth`: authentication pages only (sign-in, forgot password, reset password), which Essentials already ships; use `essentials::layouts.app` for any other public page. Sections `heading`, `description`, `content`.
- `essentials::layouts.error`: error pages. Sections `code`, `heading`, `detail`, `icon`, `actions`.
- `essentials::layouts.app`: the base document the others extend. Section `title`, `body`, `body-class`; stacks `styles` and `js`.

## Wrapping every page (portal.content)
The portal draws `portal.content` when the app layout defines it, and the page's `content` otherwise. Define it in `layouts/app.blade.php` to put every page on a card, and let a page opt out with another section the layout checks for:
```blade
@extends('essentials::layouts.portal')

@section('sidebar', 'partials.sidebar')

@section('portal.content')
    @hasSection('content.plain')
        @yield('content.plain')
    @else
        <div class="bg-white p-2 shadow-xs">
            @yield('content')
        </div>
    @endif
@endsection
```
A page then fills `content` to sit on the card, or `content.plain` to sit straight on the portal. Without `portal.content`, pages fill `content` and the portal draws it as it is.
