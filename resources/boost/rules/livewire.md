---
paths:
  - 'resources/views/components/**'
  - 'resources/views/livewire/**'
---

# Livewire

## Multi-file components
This project runs Livewire 4. Build every component as a multi-file component, created with `php artisan make:livewire name --mfc`. It lives in its own folder, such as `resources/views/components/post/⚡create/`, holding `create.php` (the class), `create.blade.php` (the view) and, when needed, `create.js`. Do not write single-file, Volt or class-based components.

## Alpine plugins come with Livewire
Livewire ships Alpine with every plugin except `@alpinejs/ui`, which Essentials registers for the modal. Do not install or start another Alpine.
