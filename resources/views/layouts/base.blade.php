<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @vite(config('essentials.assets'))
</head>
<body class="h-full bg-gray-100 text-sm text-gray-700 antialiased">
    @yield('body')

    <script>
        //  Show or hide a password when its toggle is clicked
        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-password-toggle]');

            if (! toggle) {
                return;
            }

            const input = document.getElementById(toggle.dataset.passwordToggle);
            const reveal = input.type === 'password';

            input.type = reveal ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', reveal);
            toggle.querySelector('[data-eye-open]').classList.toggle('hidden', reveal);
            toggle.querySelector('[data-eye-closed]').classList.toggle('hidden', ! reveal);
        });

        //  Disable a submit button once its form is submitted, and show the busy state
        document.addEventListener('submit', (event) => {
            const button = event.target.querySelector('[data-submit]');

            if (! button) {
                return;
            }

            button.disabled = true;
            button.querySelector('[data-spinner]').classList.remove('hidden');
            button.querySelector('[data-label]').textContent = button.dataset.busy;
        });
    </script>
</body>
</html>
