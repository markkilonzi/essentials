---
paths:
  - 'app/Http/Resources/**'
---

# Resources

## Use API Resources for JSON responses
Wrap models and collections returned from consumer-facing API endpoints in a `JsonResource` or `toResourceCollection()`, not `response()->json()` or raw models and arrays. Resources are returned without the `data` wrapper (the package turns wrapping off). Keep `response()->json()` for acknowledging third-party webhooks and callbacks.
