---
paths:
  - 'lang/**'
  - 'app/Http/**'
---

# Lang

## Short dotted language keys
Write user-facing and log messages as short dotted keys from dedicated `lang/en/*.php` files, such as `trans('alerts.success.saved')` or `trans('logs.login')`. Do not use `__('Full sentence')` JSON-style keys.
