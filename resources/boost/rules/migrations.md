---
paths:
  - 'database/migrations/**'
---

# Migrations

## ULID route key
New tables use `$table->id()` immediately followed by `$table->ulid()->unique()`, paired with the model's `HasUlids` trait.

## Foreign keys via foreignIdFor()
Use `$table->foreignIdFor(Model::class)->constrained()`, adding `->nullable()` when optional. Pivot and child tables follow the same rule; never `foreignId('x_id')` or manual `foreign()->references()`.

## Enum-like columns are strings
Store enum-backed values with `$table->string()` and cast to a backed PHP enum in the model. Never use `->enum()`.

## Always implement down()
Every migration has a real reversing `down()` (typically `Schema::dropIfExists`).
