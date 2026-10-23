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

It copies the rules, publishes the config files and migrations of the packages, adds the missing keys to `.env.example`, the discord log channel and the shared Vite aliases, imports the theme and scripts and starts Livewire in `resources/js/app.js`, creates a starter `permissions.json`, optionally sets up a basic portal (dashboard, layout and sidebar), runs `npm run build`, optionally runs the migrations and then syncs the roles and permissions, links storage, formats the files it published with Pint, installs and sets up Boost (requiring `laravel/boost --dev` first if it is missing), and ends with a checklist of what is left for you. The two optional steps are questions that default to no; `--no-interaction` never asks, and `--portal` sets up the portal without asking. If you did not run the migrations, run `php artisan migrate` afterwards and run the installer again. Run the same command whenever a package changes: it replaces the rules, guidelines and skills with the newest ones and only adds what is missing. Then follow the Quick start.

## Docs

The full docs are served at `/nyumbani-essentials/docs`, in the `local` environment only, by `nyumbani/docs`, which Essentials requires. It brings its own styles and scripts. The same switcher opens the docs of `nyumbani/daraja` and `nyumbani/messaging`, which register their pages with it.

Start with Installation, then Quick start, then Anatomy of a feature.

## Configuration

`php artisan nyumbani:install` publishes `config/essentials.php`; to publish it again by hand, run `php artisan vendor:publish --tag=essentials-config`. Every key has a working default; see the Configuration docs page.
