# nyumbani/essentials

The opinionated foundation of the Nyumbani ecosystem. It decides how every internal Nyumbani app looks and is structured, so a developer who knows one app can work in any other.

It covers five areas:

1. Helpers (`src/Utils`): `money()`, `numeric()`, the date helpers and the Blade directives built on them
2. Service classes (`src/Services`) and traits (`src/Traits`)
3. UIs (`resources/views`, `resources/js`, `resources/css`): layouts, modal, toasts, loader, error pages and the theme
4. Packages it standardises and requires: Fortify, Spatie permissions, activity log and impersonation, plus `nyumbani/daraja` (M-Pesa) and `nyumbani/messaging` (SMS)
5. AI skills and rules (`resources/boost`)

## Install

```
composer require nyumbani/essentials
php artisan nyumbani:install
```

Then import the theme and scripts in the app (see the Installation docs page), and follow the Quick start.

## Docs

The full docs are served by the package at `/nyumbani-essentials/docs`, in the `local` environment only. The same switcher opens the docs of `nyumbani/daraja` and `nyumbani/messaging` when they are installed.

Start with Installation, then Quick start, then Anatomy of a feature.

## Configuration

Publish `config/essentials.php` with `php artisan vendor:publish --tag=essentials-config`. Every key has a working default; see the Configuration docs page.
