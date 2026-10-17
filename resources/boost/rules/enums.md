---
paths:
  - 'app/Enums/**'
---

# Enums

## String-backed, UPPER_SNAKE_CASE
Declare `enum X: string` with UPPER_SNAKE_CASE cases, kebab-case values, and the `Nyumbani\Essentials\Traits\HasEnumElements` trait, which provides `label()`, `lowerLabel()` and `random()`.

## UI data via match
Add domain methods (e.g. `badgeClasses()`) using `match ($this)` rather than constants or `switch`.

## Stored as strings
Enum-backed columns are `$table->string()` in migrations, never `->enum()`, and are cast to the enum in the model's `casts()`.
