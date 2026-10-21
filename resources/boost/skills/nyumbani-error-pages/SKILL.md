---
name: nyumbani-error-pages
description: "Invoke when working on HTTP error pages in a Nyumbani app: 401, 403, 404, 419, 429, 500 or 503 views, changing their wording or look, adding another status code, disabling them, or testing them. Skip for validation errors and toasts."
metadata:
  author: nyumbani
---

# Nyumbani Error Pages

The package ships a lively page for every common error, and every Nyumbani app inherits them. Do not create `resources/views/errors/*.blade.php` in an app unless the page must differ from the standard.

## What ships
`401`, `403`, `404`, `419`, `429`, `500`, `503`, all extending `essentials::layouts.error`, which carries the Nyumbani logo, a gradient status code, a bouncing icon and the actions (Take me home, Go back, Try again).

## How they load
`ViewServiceProvider::handleErrorPages()` appends the package views directory to `config('view.paths')` (Laravel rebuilds the `errors` namespace from it at render time) and calls `View::addLocation()`. An app view in `resources/views/errors/` still wins because it comes first.

Switch them off with `'defaults' => ['error_pages' => false]` in the published `config/essentials.php`.

## Wording
- 404 heading: "Page not found".
- 500 is diplomatic: "Oops, something went wrong". Never blame the user or write "Something broke on our side".
- 503 must use the word maintenance.
- Keep the tone warm and brief, with one clear next action.

## Adding or overriding a page
Extend the layout and fill the sections:
```blade
@extends('essentials::layouts.error')

@section('code', '410')
@section('heading', 'Gone for good')
@section('detail', 'This page has been removed.')
@section('icon') ...svg paths... @endsection
@section('actions') ... @endsection
```
Use `@use` for classes and never `@php`.

## Testing
In tests, view the page with `view('errors.404')`. The `errors::` namespace only exists during a real render. Assert real requests with `$this->get('/missing')->assertNotFound()->assertSee('Page not found')`.
The Essentials docs preview every page at `/nyumbani-essentials/docs/error-pages` (local environment only), so there is no need to build a page of your own to see them.
