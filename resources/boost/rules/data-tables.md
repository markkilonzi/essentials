---
paths:
  - 'app/DataTables/**'
---

# Data Tables

## Inline unless told otherwise
Build every table as an inline table: a class extending `Amprest\DtTables\Tables\BaseTable`, created with `php artisan make:data-table Name` and rendered with `<x-data-table :payload="...">`, so the browser searches and sorts the rows. Use a server-side table (`--server-side`, `ServerSideTable`) or an API table (`--api`, `ApiTable`) only when the request names it, such as "server-side", "paged in the database" or "from the API". If an inline table might hold thousands of rows, say so in the reply and still build it inline.

## Newest first
Order `query()` with `->latest('id')`, qualifying the column (`->latest('users.id')`) when the query joins another table.

## Never paginate in a table class
Return the unpaginated query from `query()`. Do not call `paginate()`, `simplePaginate()` or `cursorPaginate()`; `amprest/dt-tables` handles paging.

## Scripts do not run inside rows
Rows and row actions are inserted with `innerHTML`, so a `<script>` tag in them never runs, and a `@push('js')` made while rendering a row never reaches the layout's stack. For a modal or form rendered per row, use an inline `x-data="{ ... }"` object literal and `x-on:` handlers instead of `Alpine.data()` in a script, or register the component in a file in `resources/js` that the app imports once.
