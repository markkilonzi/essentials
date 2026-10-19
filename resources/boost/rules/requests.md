---
paths:
  - 'app/Http/Requests/**'
---

# Requests

## Audience folders with shared bases
Requests are split by plural audience folder (`Admins/`, `SystemAdmins/`, `Customers/`, `Vendors/`). Rules shared by more than one audience live in a base request at the root of `app/Http/Requests`, which each audience class extends, merging extra rules through `parent::rules()`. Do not put shared rules on the model.

## Messages and attributes live on the request
Define `messages()` and `attributes()` on the Form Request, never in lang files. Any wildcard rule (e.g. `items.*.quantity`) needs a matching `attributes()` entry. The same applies to inline `validateWithBag()` calls, which take the attributes as the fourth argument.

## Named error bags
A form that must keep its errors apart from other forms on the page, such as a modal or one form per row, posts `@bag($name)`, which prints Laravel's own `_error_bag` input. Nothing is needed on the Form Request. Do not read a `_bag` field in `prepareForValidation()` and do not add a trait or a `failedValidation()` override for it.

## Model classes in Rule::exists / unique
Pass the model class (`Rule::unique(User::class)`), never a table name. Assign each rule to a named variable with a one-line `//  Define the ...` comment before using it in `rules()`, even when there is only one.

## Never validate the tenant id
The tenant comes from the signed-in user in the controller, so a Form Request has no `organization_id` rule or message. Foreign keys to tenant-owned records use `Rule::exists(Model::class, 'id')` scoped to the current tenant with `->where('organization_id', $tenant->id)`, so a user cannot reference another tenant's record.

## Retrieve validated input
Use `$request->safe()->field` (or `$this->field` inside the request's own methods), never `$request->input()` or typed getters.

## No empty authorize()
Do not define an `authorize()` that just returns `true`.

## Typed accessors only for boolean, date and enum input
Read boolean, date and enum request input with `->boolean()`, `->date()` and `->enum()`. Use plain property or array access for strings and numbers, not `->string()` or `->integer()`.
