---
paths:
  - 'tests/**'
---

# Tests

## Every Pest test, hook and helper gets a PHPDoc + @author tag
Put a PHPDoc block before every `it()`, `test()`, `beforeEach()` / `afterEach()`, helper function and fake class method in `tests/**`: a one-line summary of what is being proven, a blank line, then the `@author` tag (see the app rules for the default). Tests are written in Pest (`it()` / `test()` closures with `expect()`), not PHPUnit classes.

## Never use @param or @return in doc blocks
Doc blocks hold a one-line summary and the `@author` tag only. Do not write `@param` or `@return` tags; the type hints already describe them.
