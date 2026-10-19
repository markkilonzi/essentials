---
paths:
  - 'routes/**'
---

# Routes

## Role-based route files wired in bootstrap/app.php
Routes are split by audience (`web.php` for the root redirect, `portal.php` for shared authenticated routes, then one file per audience). Prefix, name and middleware, including the `role:` guard and the active-account check, are set once in `bootstrap/app.php`, never inside the route files.

## Explicit verbs with array callables
Define routes as `Route::get('x', [Controller::class, 'method'])->name('x')`. No closures (except the trivial root redirect), no `Route::resource` / `apiResource`, no group-level `->controller()`.

## Fortify owns auth routes
Login, logout and password reset routes come from Fortify. Do not redefine them.

## Implicit route-model binding
Type-hint models in controller methods; do not use `Route::bind()` or manual `findOrFail()`.

## Comments and unused routes
Route-file comments use the `//  ` format, and each group's label must match its controller. Only declare routes whose controller method is implemented.

## Name the parameters in route()
Pass route parameters as a keyed array, never a bare model: `route('customers.show', ['customer' => $customer])`, not `route('customers.show', $customer)`. This applies in views, controllers, tests and anywhere else `route()` is called.
