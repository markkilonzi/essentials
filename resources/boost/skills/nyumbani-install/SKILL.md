---
name: nyumbani-install
description: "Invoke when setting up a Nyumbani app, running or changing php artisan nyumbani:install, reading its setup checklist, or fixing a checklist item: rules, Boost, config files, migrations, imports, the discord log channel, or the HasPermissions trait. Skip for using a feature once it is installed."
metadata:
  author: nyumbani
---

# Nyumbani Install

`php artisan nyumbani:install` is the one command that sets up and refreshes a Nyumbani app. It is safe to run again, and it never asks unless Boost needs its first-time answers. Composer runs it on `post-update-cmd` as `nyumbani:install --no-interaction --ansi`, so a `composer update` brings the newest rules and skills.

## What it does, in order
1. **Dependencies.** Installs `laravel/boost --dev` if it is missing, and requires `nyumbani/daraja` and `nyumbani/messaging` in the app's own `composer.json` if the app only gets them through Essentials. Boost reads guidelines and skills only from packages the app requires itself, so indirect ones are invisible to it. Then it runs itself again in a fresh process, because the new classes are not loaded in the first.
2. **Rules.** Copies the Nyumbani rules to `.ai/rules/nyumbani-*.md`, replaces older copies, removes ones the package no longer ships, and rebuilds `.ai/rules/index.md`. Never edit them; the next run overwrites them.
3. **Config and migrations.** Publishes `essentials`, `daraja`, `messaging`, `permission` and `activitylog` config files if absent, and the activity log migration. The permission tables come from Essentials' own migration, which already has the `group` column; an app that published Spatie's migration earlier gets a small `update_permissions_table_add_group_column` migration instead. It never publishes a migration the app already has.
4. **App files, only what is missing.** Adds blank package keys to `.env.example` (never `.env`), the theme `@import` to `resources/css/app.css`, the scripts `import` to `resources/js/app.js`, a starter `database/seeders/data/permissions.json`, the `discord` channel in `config/logging.php`, and the storage link.
5. **Boost.** The first time (no `boost.json`) it runs `boost:install`. Always, it loads the three Nyumbani packages into `boost.json`, lists their skills there (`boost:update` installs only the skills named in `boost.json`, and finds new ones only by asking, which a non-interactive run cannot), sets the agent to `claude_code` when none is set (a non-interactive first install picks none, and `boost:update` refuses to run without one), then runs `boost:update`.
6. **Checklist.** Prints a table of done, optional and to-do items.

## Reading the checklist
| Item | Fix when it is not done |
| --- | --- |
| Theme / scripts imported | Add the one line the table names to `app.css` / `app.js`. |
| User model implements `HasHomeRoute` | Implement `Nyumbani\Essentials\Contracts\HasHomeRoute` and return a URL from `homeRoute()`. |
| User model has roles and default permissions | Use `Nyumbani\Essentials\Traits\HasPermissions`. **Not** `Spatie\Permission\Traits\HasPermissions`: it has the same name but no roles and no `assignDefaultPermissions()`. |
| User model can be impersonated | Use `Lab404\Impersonate\Models\Impersonate`. |
| Migrations have run | Run `php artisan migrate`. The installer publishes migrations but never runs them. |
| Laravel Boost is installed | Run the installer; it requires Boost for you. |
| Boost is set up with the Nyumbani packages | Run `composer require nyumbani/daraja nyumbani/messaging`, then the installer again. |
| discord log channel, Daraja credentials, Connect settings | Optional: needed only if the app uses that feature. |

The installer reports; it never edits the user model or any other application code.

## Rules for changing the installer
- Every step must be idempotent: add only what is missing, and leave anything the app already has.
- Do not run `migrate`, `composer` for anything but the dependencies above, or touch `.env`.
- A step that edits a file must still leave valid PHP (the logging step checks this before saving).
- When a step is added or changed, update the Installation docs table, this skill and the tests in `InstallCommandTest.php`. The tests fake `vendor:publish`, `storage:link` and Boost's commands so nothing writes into the real project.
