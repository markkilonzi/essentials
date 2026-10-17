---
paths:
  - 'app/Providers/**'
---

# Providers

## Provider responsibilities
`AppServiceProvider` holds password defaults, the destructive-command guard, lazy-loading prevention, the morph map and redirect macros. `FortifyServiceProvider` holds the Fortify views, the login rate limiter and the role-based login and logout responses. `ViewServiceProvider` holds Blade directives and view composers. `AuthServiceProvider` holds only non-model gates.

## Lazy loading is prevented outside production
`Model::preventLazyLoading()` is on in every non-production environment, so an unloaded relation throws `LazyLoadingViolationException` locally and in tests. Always eager-load what you access.

## Morph map covers every model
Every model is registered in `Relation::enforceMorphMap()` with a kebab-case key.
