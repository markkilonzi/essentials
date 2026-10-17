---
paths:
  - 'app/Policies/**'
---

# Policies

## Policies only
All authorization lives in model policies, auto-discovered by model. Do not use `Gate::define`. Register `Gate::policy()` in `AuthServiceProvider` only for gates that are not model-backed.

## Permission-name checks
Check Spatie permission names with `$user->hasPermissionTo('verb-noun')`. Do not branch on roles inside policies.

## Scope by tenant
Users who are not system admins may only act on records of their own tenant; system admins reach every tenant.
