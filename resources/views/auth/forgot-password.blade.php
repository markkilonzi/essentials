@extends('essentials::layouts.auth')

@section('title', 'Forgot your password')
@section('heading', 'Get a password reset link')
@section('description', 'Enter your email address to receive a password reset link.')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <x-essentials::field name="email" label="Email address" type="email" icon="mail" :value="old('email')" placeholder="Enter your email address" autocomplete="email" required autofocus />

        <div class="mt-7">
            <x-essentials::submit label="Get Reset Link" busy="Sending..." />
        </div>

        <p class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary-dark underline-offset-2 hover:text-primary hover:underline">Back to sign in</a>
        </p>
    </form>
@endsection
