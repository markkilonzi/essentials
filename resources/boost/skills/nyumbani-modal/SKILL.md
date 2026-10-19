---
name: nyumbani-modal
description: "Invoke when adding, changing or debugging any modal, dialog, slide-over, bottom sheet or popup in a Nyumbani app: the <x-essentials::modal> component, opening it by event, reopening it after a failed save, loading its body remotely, or confirming a destructive action. Skip for toasts and for pages that are not overlays."
metadata:
  author: nyumbani
---

# Nyumbani Modal

Every Nyumbani app uses the package modal, `<x-essentials::modal>`. Never write a one-off Alpine modal and never copy one from another app.

## Setup (already done in a Nyumbani app)
- `resources/js/app.js` imports `vendor/nyumbani/essentials/resources/dist/essentials.js`, a pre-bundled file that includes the modal, its `@alpinejs/ui` plugin and `essentialsModal`. The app installs no npm package for it.
- Run `npm run build` after adding classes to a view.

## Props
| Prop | Default | Use |
| --- | --- | --- |
| `title` (attribute) | none | Heading in the title bar |
| `size` | `md` | `sm`, `md`, `lg`, `xl`, `full` |
| `position` | `center` | `right` makes a slide-over |
| `name` | none | Lets events open and close it |
| `error-bag` | none | Reopens when that bag has errors |
| `dismissable` | `true` | `false` is a static backdrop: Escape and outside clicks do nothing |
| `src` | none | URL of an HTML fragment loaded on first open |
| `close-button`, `show-title` | `true` | Hide the X or the title bar |

Slots: `trigger` (opens on click), the default slot (body), `action` (footer pinned below the scrolling body).

## Patterns
Trigger and body:
```blade
<x-essentials::modal title="Add record">
    <x-slot:trigger><button type="button" class="btn btn-primary">Add</button></x-slot:trigger>
    <form>...</form>
    <x-slot:action>...</x-slot:action>
</x-essentials::modal>
```

Open or close by name from anywhere inside an Alpine scope (`x-data`):
```blade
<button type="button" x-on:click="$dispatch('open-modal', 'edit-record')">Edit</button>
```
`$dispatch` only works inside an element with `x-data`. Add one to the wrapper when the button sits outside any Alpine scope. Close with `close-modal`.

Reopen after a failed save: give the modal an `error-bag` and post `@bag('same-name')` in its form (see the nyumbani-error-bags skill). A fixed `protected $errorBag` on the Form Request also works. The modal reopens with the errors showing.

Remote content: pass `src` and return a Blade fragment, not a full page, from that route. The loader shows while it loads and a retry link shows on failure. It loads once per page, and again only after a failure.

## Confirmations
Use native `confirm()`, for example `onclick="return confirm('Delete this record?')"` or `x-on:click="confirm('...') || $event.preventDefault()"`. Do not build a confirm modal.

## Rules
- Buttons inside use `btn btn-primary`. Every button is primary.
- Do not put `@php` in the view. Logic belongs in the `Nyumbani\Essentials\Views\Components\Modal` class or a controller.
- The panel wrapper uses `overflow-clip`, not `overflow-hidden`, because the focus trap otherwise scrolls the slide-over sideways while it animates. Keep that when changing the component.
- After changing the component, run `php artisan view:clear` if the compiled view reports an undefined variable.
