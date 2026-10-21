@extends('essentials::layouts.auth')

@section('title', 'Forgot your password')
@section('heading', 'Get a password reset link')
@section('description', 'Enter your email address to receive a password reset link.')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-5">
            <label for="email">Email address</label>
            <div class="relative">
                <span data-icon class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg></span>
                <input aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Enter your email address" autocomplete="email" required autofocus>
            </div>
            @error('email')
                <span class="mt-1 block text-xs text-red-600" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-7">
            <button type="submit" class="btn btn-primary btn-lg w-full uppercase tracking-wide">Get Reset Link</button>
        </div>

        <p class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary-dark underline-offset-2 hover:text-primary hover:underline">Back to sign in</a>
        </p>
    </form>
@endsection
