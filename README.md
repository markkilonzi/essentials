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

It copies the rules, publishes the config files and migrations of the packages, adds the missing keys to `.env.example`, adds the shared Vite aliases, imports the theme and scripts and starts Livewire in `resources/js/app.js`, creates a starter `permissions.json` and syncs the roles and permissions from it once the tables exist, links storage, installs and sets up Boost (requiring `laravel/boost --dev` first if it is missing), and ends with a checklist of what is left for you. Run `php artisan migrate` afterwards. Run the same command again whenever a package changes: it replaces the rules, guidelines and skills with the newest ones and only adds what is missing. Then import the theme and scripts in the app (see the Installation docs page), and follow the Quick start.

## Docs

The full docs are served at `/nyumbani-essentials/docs`, in the `local` environment only, by `nyumbani/docs`, which Essentials requires. It brings its own styles and scripts. The same switcher opens the docs of `nyumbani/daraja` and `nyumbani/messaging`, which register their pages with it.

Start with Installation, then Quick start, then Anatomy of a feature.

## Configuration

`php artisan nyumbani:install` publishes `config/essentials.php`; to publish it again by hand, run `php artisan vendor:publish --tag=essentials-config`. Every key has a working default; see the Configuration docs page.
