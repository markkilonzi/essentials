---
paths:
  - 'resources/views/**'
---

# Views

## Audience-first folders
Views are grouped by audience: `admins/{pages,partials}`, `system-admins/{pages,partials}`, with shared `layouts` and `components`. Modal components live in `components/{audience}/modal`.

## Auth pages come from the package
Login, forgot-password and reset-password are the same in every Nyumbani app and are provided by `nyumbani/essentials`. Do not build your own.

## Anonymous components
Build reusable markup as anonymous `<x-*>` components with `@props()`. Never create class-based components in `app/View/Components`. Reserve `@include` for layout glue (sidebar, navbar, alerts).

## No inline PHP
Never write `@php ... @endphp` blocks to compute data. Move it to the controller, a view composer (registered in `ViewServiceProvider`), or the owning DataTable class. Reading `$errors` or `old()` inline is fine. Never inline a fully-qualified class name; add `@use('Vendor\Class')` at the top of the file.

## Plain English copy
Blade view copy is written as plain English text, with no `__()` or `@lang()` wrapping. Only flash messages go through the `alerts.*` translation keys.

## Blade directives for formatting
Use `@date`, `@time`, `@datetime`, `@dateforhumans`, `@numeric`, `@currency`, `@percentage`, `@prettify`, `@bag` and `@money` instead of raw `format()`, `date()` or `number_format()`. Never write a literal "KES" or "Ksh", even in placeholders (use `e.g. 1000`).

## Blade syntax
Use `@class()` for conditional classes, `@selected` / `@checked` for form state, `:$var` shorthand when names match, self-closing tags for slot-less components, `@js()` to pass PHP data to Alpine, and `@push('js')` for inline scripts. Nest content one level deeper than the enclosing directive. Pluralize with `str()->plural()` and build links with `route()`.

## Forms
Every text-like input has a helpful placeholder: an example, the expected format, or what a blank value means, never just the label. Required labels carry `aria-required="true"`. Forms never carry a hidden tenant id input.

## Double-submit guard and confirmations
Mutating submit buttons carry `data-disable-on-submit`. Destructive actions ask for confirmation, with `x-on:submit="if (! confirm('...')) $event.preventDefault()"` on delete forms. Never call `form.submit()`; use `requestSubmit()`, and never from inside a `submit` listener on the same form.

## Alpine
Keep inline `x-data="{ ... }"` for trivial state only (a couple of keys). Register anything larger with `Alpine.data('name', () => ({ ... }))` inside `document.addEventListener('alpine:init', ...)` in a `@push('js')` block at the bottom of the same file, then use `x-data="name"`. Components are functions taking a `data` param with destructured defaults, use `init()`, `$watch()`, `$store.helpers`, getters, and `x-on:` instead of `@`.

## Design tokens
Import the shared theme from the package in the app's Tailwind css (`@import '../../vendor/nyumbani/essentials/resources/css/theme.css';`) and use its primary and secondary colour scales. Do not define your own palette. Buttons use `.btn` with `.btn-primary` or `.btn-secondary` from the shared theme (plus `.btn-sm` or `.btn-lg`), never raw colour utilities. Design mobile-first and meet WCAG AA.
