@extends('essentials::layouts.auth')

@section('title', 'Reset your password')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="e.g. jane@example.com" required>
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password">New password</label>
        <input id="password" type="password" name="password" placeholder="At least 8 characters" required>
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat the new password" required>

        <button type="submit" data-disable-on-submit>Reset password</button>
    </form>
@endsection
