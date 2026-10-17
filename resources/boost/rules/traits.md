---
paths:
  - 'app/Traits/**'
---

# Traits

## Prefer the shared traits
Use the traits from `Nyumbani\Essentials\Traits` (`HasUlids`, `HasUploads`, `HasProfileImage`, `HasEnumElements`, `HasNumberIdentifiers`, `HasMoney`...) instead of keeping a local copy. Only app-specific traits live in `app/Traits`.

## Model mixins as HasX traits
Model mixins are `HasX` traits using `bootHasX()` / `initializeHasX()` hooks. `HasUlids` is the default public-key strategy; `HasNumberIdentifiers` is for sequential business-number columns. Controller helpers such as `StoresProfileImages` are traits too.

## Events only for message dispatch
Reserve event dispatch for decoupling notification delivery (SMS / email) from the workflow that triggers it. Call services and models directly for all other business logic. No model observers or `booted()` closures for business events.
