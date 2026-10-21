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
- `essentials::layouts.portal`: signed-in shell with sidebar and navbar. Sections: `title`, `content` (drawn inside the layout's card, `bg-gray-100 shadow-xs`; add no outer card), `banner` (above the card), `sidebar`, and the `actions` stack.
- `essentials::layouts.auth`: authentication pages only (sign-in, forgot password, reset password), which Essentials already ships. Sections: `heading`, `description`, `content`. Not for other public pages.
- `essentials::layouts.error`: error pages, see the nyumbani-error-pages skill.

Avoid repeating the sidebar include on every page. Create one app layout and extend it from every page:
```blade
@extends('essentials::layouts.portal')

@section('sidebar', 'partials.sidebar')
```
The `sidebar` section takes the name of a view and the portal renders it; markup written inline in the section works too. A portal page with no sidebar throws a `LogicException`, and so does a name that is not a view.

## Favicons and manifest
- Never add favicon files or a favicons partial to an app. The shared set, the white Nyumbani logo at each size, lives in `resources/images/` of the package and is served by the `essentials.image` route. The layout links them inline.
- The manifest comes from `route('essentials.manifest')` (`WebManifestController`). It uses the app name and `essentials.branding.theme_color`.
- Change the theme color with `ESSENTIALS_THEME_COLOR` or the published config, never in the layout.
- To replace an icon for one app, publish the package images, do not edit `vendor`.

## Theme
Import the shared theme once in the app's Tailwind css:
```css
@import 'tailwindcss';
@import '../../vendor/nyumbani/essentials/resources/css/theme.css';
```
Use the tokens `primary`, `primary-dark`, `primary-darker` and `secondary*`. Do not hard-code hex colors for brand surfaces.

## Rules
- Use `@use` for any class in a view and never `@php`.
- Page titles come from `@section('title', ...)`. The layout appends the app name.

## Extending the layouts
There are four, and the Layouts page of the Essentials docs shows the code that extends each one:
- `essentials::layouts.portal`: signed-in pages. Sections `title`, `content`, `sidebar`, and optionally `brand`, `navbar`, `user-menu`, `banner`; `@push('actions')` for page buttons. Define the sidebar once: the app's `layouts/app.blade.php` extends the portal and sets `@section('sidebar', 'partials.sidebar')`, the partial holds the `nav-link` and `nav-group` items, and every page extends `layouts.app` (never the portal) and fills only its own sections.
- `essentials::layouts.auth`: authentication pages only (sign-in, forgot password, reset password), which Essentials already ships; use `essentials::layouts.app` for any other public page. Sections `heading`, `description`, `content`.
- `essentials::layouts.error`: error pages. Sections `code`, `heading`, `detail`, `icon`, `actions`.
- `essentials::layouts.app`: the base document the others extend. Section `title`, `body`, `body-class`; stacks `styles` and `js`.
