---
paths:
  - 'resources/js/**'
---

# Js

## Header comment for every function
Every function, event listener and option object gets a `/** ... */` block comment above it with a one-line description, e.g. `/** Add a keydown event listener to the document */` laid out over three lines. The description says what it does, in a short phrase.

## Single-statement functions skip the inline comment
When a function body is exactly one statement (one top-level `;`), it keeps its header comment but gets no inline `//` comment above the statement. This holds however many lines the statement spans.

Functions with two or more top-level statements keep a `//  ` comment before each. Statements nested inside the single statement (e.g. in a callback) still follow the normal comment rule.
