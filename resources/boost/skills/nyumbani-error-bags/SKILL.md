---
name: nyumbani-error-bags
description: "Invoke when a Nyumbani page has more than one form that posts to the same route or Form Request, a form per row or record, validation errors showing under the wrong form, a modal form that must reopen on failure, or whenever @bag, bag(), _error_bag, errorBag or error-bag appear. Skip for a page with a single form."
metadata:
  author: nyumbani
---

# Nyumbani Error Bags

An error bag keeps the validation errors of one form apart from every other form on the page. Laravel already supports it: when a failed validation redirects back, it flashes the errors into the bag named by the posted `_error_bag` input. Nyumbani's `@bag` directive prints that input. There is nothing to add on the Form Request.

## The view
```blade
<form method="POST" action="{{ route('houses.update', ['house' => $house]) }}">
    @csrf
    @bag($bag = 'house-'.$house->id)

    <input name="name" value="{{ $errors->hasBag($bag) ? old('name') : $house->name }}">
    @error('name', $bag)
        <span role="alert">{{ $message }}</span>
    @enderror
</form>
```
- `@bag($name)` prints `<input type="hidden" name="_error_bag" value="...">` and escapes the name.
- Always use the directive. Older apps and `amprest/dt-tables` up to v2.15 define a global `bag()` that prints the older `_bag` input, so do not call a `bag()` function in views.
- Use a record id or row key for repeated forms, or a fixed name for a modal.
- Read errors with `@error('field', $bag)` or `$errors->getBag($bag)`. A plain `@error('field')` reads only the default bag and will miss them.
- Fill a field from `old()` only when `$errors->hasBag($bag)`, or one failed save fills every sibling form.

## The request side: nothing
No trait, no `prepareForValidation()` line and no `failedValidation()` override. It works with a Form Request and with `$request->validate()` in a controller.

Older apps copy `$this->errorBag = $this->{'_bag'} ?? $this->errorBag;` into their requests and print a `_bag` input from their own `bag()` helper. When touching such an app, switch its views to the `@bag` directive and delete that line and the helper. Leave the data table views of `amprest/dt-tables` alone: they pair `_bag` with their own request.

## Order of precedence
When validation fails, the bag is the posted `_error_bag`, then the Form Request's own `protected $errorBag`, then `default`.

## Modals
Give `<x-essentials::modal>` the same name in `error-bag` and it reopens after a failed save with the errors showing:
```blade
<x-essentials::modal name="edit-customer" error-bag="edit-customer" title="Edit customer">
    <form method="POST" action="{{ route('customers.update', ['customer' => $customer]) }}">
        @csrf
        @bag('edit-customer')
        ...
    </form>
</x-essentials::modal>
```
A modal with one fixed bag may set `protected $errorBag = 'edit-customer';` on the request instead of posting `@bag`. See the nyumbani-modal skill.

## Limits
- It applies to the redirect after a failed validation. A JSON response carries the errors without a bag.
- The bag name is user input. Use it only to choose a bag, never to look anything up.

## Testing
```php
$this->post(route('houses.update', ['house' => $house]), ['_error_bag' => 'house-1', 'name' => ''])
    ->assertSessionHasErrors('name', null, 'house-1')
    ->assertSessionDoesntHaveErrors('name', 'default');
```
