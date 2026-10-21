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
| `confirm` | `false` | Two-step review before the form posts (see Two-step confirmation) |
| `continue-label`, `back-label` | `Continue`, `Back` | Text of the confirm buttons |
| `close-button`, `show-title` | `true` | Hide the X or the title bar |

Slots: `trigger` (opens on click), the default slot (body), `action` (footer pinned below the scrolling body), `review` (step two of a `confirm` modal).

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

Reopen after a failed save, in four steps (see the nyumbani-error-bags skill):
1. Give the modal a `name` and an `error-bag` (use the same word for both).
2. Post the bag with `@bag('same-name')` inside the form.
3. Validate as usual: a Form Request or `$request->validate()` needs no trait and no override.
4. In the modal, read errors with `@error('field', 'same-name')` and refill with `old('field')` only when `$errors->hasBag('same-name')`.

It does not reopen when the modal has no `error-bag`, when the name differs from `@bag`, or when the view reads `@error('field')` without the bag. A fixed `protected $errorBag` on the Form Request also works. Bags apply to the redirect after a failed validation, not to JSON or Livewire answers.

Remote content: pass `src` and return a Blade fragment, not a full page, from that route. The loader shows while it loads and a retry link shows on failure. It loads once per page, and again only after a failure.

## Two-step confirmation
Use `confirm` to review a form before it posts. The default slot is step one (the form, with an `id`), `review` is step two and the `action` slot holds the real submit button, pointed at the form with `form="id"`. Continue runs the browser's native validation (`reportValidity()`), so use normal `required`, `minlength` and `pattern` attributes. The review reads what was typed from `data`, for example `x-text="data.name"`. The Enter key acts as Continue, and the modal always opens on step one.
```blade
<x-essentials::modal confirm error-bag="new-customer" title="New customer">
    <form id="new-customer-form" method="POST" action="{{ route('customers.store') }}">
        @csrf
        @bag('new-customer')
        <input name="name" required minlength="3">
    </form>
    <x-slot:review>Name: <span x-text="data.name"></span></x-slot:review>
    <x-slot:action><button type="submit" form="new-customer-form" class="btn btn-primary">Save</button></x-slot:action>
</x-essentials::modal>
```
The review reads `data` keyed by each field's `name`, so every field needs a `name`. Fields with no name, values held only in Alpine state, fields removed with `x-if`, checkbox groups and `name[]` are not in `data`; a select gives its value, not its label. A submit during step one is intercepted before the form's own `x-on:submit` or `wire:submit` runs, and those handlers run only in step two. The steps animate with `x-transition`.

Do not hand-roll `confirm == true` toggles or a `type="button"` Continue that skips validation.

## Confirmations
Use native `confirm()`, for example `onclick="return confirm('Delete this record?')"` or `x-on:click="confirm('...') || $event.preventDefault()"`. Do not build a confirm modal for destructive actions.

## Rules
- The body is not padded by the component, so the content must be: wrap the default slot in an element with at least `p-4`, or put `class="p-4"` on the form. A fragment loaded with `src` pads its own root. Nothing may sit on the edge of the panel, except a deliberate full-bleed image or table. The `action` footer is padded by the component.
- Buttons inside use `btn btn-primary`. Every button is primary.
- Do not put `@php` in the view. Logic belongs in the `Nyumbani\Essentials\Views\Components\Modal` class or a controller.
- The panel wrapper uses `overflow-clip`, not `overflow-hidden`, because the focus trap otherwise scrolls the slide-over sideways while it animates. Keep that when changing the component.
- After changing the component, run `php artisan view:clear` if the compiled view reports an undefined variable.
