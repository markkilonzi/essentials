---
name: nyumbani-branding
description: "Invoke when touching an app's layout, favicons, web manifest, theme color, logo, page title, or the shell (sidebar, navbar, guest and portal layouts) in a Nyumbani app. Skip for component-level styling."
metadata:
  author: nyumbani
---

# Nyumbani Branding and Layouts

The package owns the look and the shell. An app extends it and only supplies content and its sidebar.

## Layouts
- `essentials::layouts.app`: the base document. Includes favicons, the manifest, the theme-color meta, `@vite`, toasts (`essentials::partials.alerts`) and the `styles` and `js` stacks.
- `essentials::layouts.portal`: signed-in shell with sidebar and navbar. Sections: `title`, `content`, `banner`, `sidebar`, and the `actions` stack.
- `essentials::layouts.guest`: sign-in style pages. Sections: `heading`, `description`, `content`.
- `essentials::layouts.error`: error pages, see the nyumbani-error-pages skill.

Avoid repeating the sidebar include on every page. Create one app layout and extend it from every page:
```blade
@extends('essentials::layouts.portal')

@section('sidebar')
    @include('partials.sidebar')
@endsection
```

## Favicons and manifest
- Never add favicon files or a favicons partial to an app. The shared house-in-hand set lives in `resources/images/` of the package and is served by the `essentials.image` route. The layout links them inline.
- The manifest comes from `route('essentials.manifest')` (`WebManifestController`). It uses the app name and `essentials.branding.theme_color`.
- Change the theme color with `ESSENTIALS_THEME_COLOR` or the published config, never in the layout.
- To replace an icon for one app, publish the package images, do not edit `vendor`.

## Theme
Import the shared theme once in the app's Tailwind css:
```css
@import 'tailwindcss';
@import '../../vendor/nyumbani/essentials/resources/css/theme.css';
```
Use the tokens `primary`, `primary-dark`, `primary-light`, `primary-darker`, `primary-darkest` and `secondary*`. Do not hard-code hex colors for brand surfaces.

## Rules
- Use `@use` for any class in a view and never `@php`.
- Page titles come from `@section('title', ...)`. The layout appends the app name.
