---
paths:
  - 'app/Providers/**'
---

# Providers

## Provider responsibilities
The package applies the app-wide defaults itself: lazy-loading prevention, the destructive-command guard, unwrapped JSON resources (`JsonResource::withoutWrapping()`), the default password rules (`Password::min(8)`), the log context middleware, the redirect `alert()` and request `period()` macros, the Fortify setup, the Blade directives and the portal login guard. Each is switchable in `essentials.defaults`, so do not repeat them in the app.

`AppServiceProvider` holds only what is specific to the app: the morph map and app-only macros. `FortifyServiceProvider` holds only the app's own Fortify overrides, such as role-based login and logout responses. `ViewServiceProvider` holds only the app's own view composers and directives. `AuthServiceProvider` holds only non-model gates.

## Lazy loading is prevented outside production
The package turns on `Model::preventLazyLoading()` in every non-production environment, so an unloaded relation throws `LazyLoadingViolationException` locally and in tests. Always eager-load what you access.

## Morph map covers every model
Every model is registered in `Relation::enforceMorphMap()` with a kebab-case key.
