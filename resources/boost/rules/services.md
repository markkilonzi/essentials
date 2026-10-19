---
paths:
  - 'app/Services/**'
---

# Services

## Invocation shape
A service with one responsibility takes its inputs (domain objects and scalars) in a promoted-property constructor, is created with `new` at the call site and exposes `handle()` as its entry point. A service that offers several independent read or report operations exposes a static `make()` factory and descriptively named public methods, called as `Service::make()->method()`.
