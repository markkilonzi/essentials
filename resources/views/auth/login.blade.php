@extends('essentials::layouts.auth')

@section('title', 'Sign in')

@section('content')
    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="{{ config('fortify.username', 'email') }}">{{ prettify(config('fortify.username', 'email')) }}</label>
        <input id="{{ config('fortify.username', 'email') }}" type="text" name="{{ config('fortify.username', 'email') }}" value="{{ old(config('fortify.username', 'email')) }}" placeholder="e.g. jane@example.com" required autofocus>
        @error(config('fortify.username', 'email'))
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password">Password</label>
        <input id="password" type="password" name="password" placeholder="Your password" required>
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <label><input type="checkbox" name="remember" value="1"> Remember me</label>

        <button type="submit" data-disable-on-submit>Sign in</button>
    </form>

    <p class="links"><a href="{{ route('password.request') }}">Forgot your password?</a></p>
@endsection
