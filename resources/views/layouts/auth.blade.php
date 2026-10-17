<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f4f5; margin: 0; display: grid; place-items: center; min-height: 100vh; }
        main { background: #fff; border-radius: .5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, .15); padding: 2rem; width: min(24rem, 90vw); }
        h1 { font-size: 1.25rem; margin: 0 0 1.5rem; }
        label { display: block; font-size: .875rem; margin-bottom: .25rem; }
        input[type=email], input[type=text], input[type=password] { box-sizing: border-box; width: 100%; padding: .5rem; margin-bottom: 1rem; border: 1px solid #a1a1aa; border-radius: .25rem; }
        button { width: 100%; padding: .6rem; border: 0; border-radius: .25rem; background: #18181b; color: #fff; cursor: pointer; }
        .error { color: #b91c1c; font-size: .8rem; margin: -.75rem 0 1rem; }
        .status { color: #15803d; font-size: .875rem; margin-bottom: 1rem; }
        .links { margin-top: 1rem; font-size: .875rem; text-align: center; }
    </style>
</head>
<body>
    <main>
        <h1>@yield('title')</h1>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>
