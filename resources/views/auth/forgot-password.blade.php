@extends('essentials::layouts.auth')

@section('title', 'Forgot your password?')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="e.g. jane@example.com" required autofocus>
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <button type="submit" data-disable-on-submit>Email me a reset link</button>
    </form>

    <p class="links"><a href="{{ route('login') }}">Back to sign in</a></p>
@endsection
