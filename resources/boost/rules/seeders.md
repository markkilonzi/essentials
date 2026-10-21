---
paths:
  - 'database/seeders/**'
---

# Seeders

## Seed data comes from JSON
Seed data lives in `database/seeders/data/*.json`, such as `permissions.json` (role, then group, then permission names), `system-admins.json` and `organizations.json`. Passwords in these files are stored as bcrypt hashes. Roles and permissions are synced from `permissions.json` by the package's `PermissionSeeder`, run with `php artisan nyumbani:permissions`, which `nyumbani:install` also runs once the permission tables exist. Run it on every deploy.

## Permission names
Permissions are kebab-case verb-noun: plural for a list (`view-users`), singular for one record (`create-user`).

## Assign defaults through the user
Give a user their role and its permissions with `$user->assignDefaultPermissions($role)`. Never call `syncPermissions()` or `givePermissionTo()` directly to set defaults.
