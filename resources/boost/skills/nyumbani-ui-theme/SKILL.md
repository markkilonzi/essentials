---
name: nyumbani-ui-theme
description: "Invoke when styling buttons, showing toast alerts, adding a loading state or spinner, or building forms, file inputs and navigation in a Nyumbani app. Covers btn classes, back()->alert(), <x-essentials::loader>, file-input and nav components."
metadata:
  author: nyumbani
---

# Nyumbani UI Theme

## Buttons
Every button is primary. Use `btn btn-primary`, with `btn-sm` or `btn-lg` for size. There is no danger or outline variant, and `btn-secondary` renders the same as primary. Never choose a color per button. A destructive action is still a primary button guarded by native `confirm()`.

## Toasts
Flash with the `alert` macro and let the layout show it:
```php
return back()->alert('success', trans('alerts.customers.saved', ['name' => $customer->name]));
```
Every toast type (success, error, info, warning) renders in the one success color. The type still sets the icon and screen-reader meaning, so pass the honest type: `success` when it did what was asked, `error` when it failed and nothing changed, `warning` when it worked but needs another look, `info` for something to know. Put the text in `lang/alerts.php` as full sentences with `:placeholder` values. `redirect()->alert(...)` with no route goes back, like `back()->alert(...)`, and a Laravel `status` flash also shows as a success toast. From JavaScript, call `toastr.success('...')`.

## Loader
```blade
<x-essentials::loader />
<x-essentials::loader size="md" message="Nyumbani is loading this for you..." />
<x-essentials::loader message="" />
```
Props: `message` (default "Nyumbani is processing your data...") and `size` (`sm`, `md`, `lg`). Pass `message=""` to hide the text, because `null` falls back to the default. It is the animated Nyumbani mark, served from the package. Use it for any wait the user can see, and do not invent another spinner.

## Components
- `<x-essentials::modal>`: see the nyumbani-modal skill.
- `<x-essentials::file-input name="..." label="..." accept="..." :current="$upload" />`, where `$upload` is the saved Upload model (it supplies `url`, `title`, `formattedSize` and `isImage()`). The form needs `enctype="multipart/form-data"`.
- `<x-essentials::nav-group>` and `<x-essentials::nav-link>` build the sidebar. Implement the `HasHomeRoute` contract for the home link.

## Rules
- Components that need logic are classes in `Nyumbani\Essentials\Views\Components`, registered under the `essentials` namespace. Simple ones are anonymous Blade components.
- No `@php` in views. Prepare data in a controller or component class.
- Use `@use` for every class a view references.
- After adding Tailwind classes in a package view, run `npm run build` in the app.
