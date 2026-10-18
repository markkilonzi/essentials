@extends('essentials::layouts.auth')

@section('title', 'Reset your password')
@section('heading', 'Reset your password')
@section('description', 'Set a new password to regain access to the system')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-essentials::field name="email" label="Email address" type="email" icon="mail" :value="old('email', $request->email)" placeholder="Enter your email address" autocomplete="email" required autofocus />

        <x-essentials::field name="password" label="New password" type="password" icon="lock" toggle placeholder="At least 8 characters" autocomplete="new-password" required />

        <x-essentials::field name="password_confirmation" label="Confirm password" type="password" icon="lock" toggle placeholder="Repeat the new password" autocomplete="new-password" required />

        <div class="mt-7">
            <x-essentials::submit label="Reset Password" busy="Resetting..." />
        </div>

        <p class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary-dark underline-offset-2 hover:text-primary hover:underline">Back to sign in</a>
        </p>
    </form>
@endsection
