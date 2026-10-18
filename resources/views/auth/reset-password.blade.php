@extends('essentials::layouts.auth')

@section('title', 'Reset your password')
@section('heading', 'Reset your password')
@section('description', 'Set a new password to regain access to the system')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-5">
            <label for="email">Email address</label>
            <div class="relative">
                <span data-icon class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg></span>
                <input aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" id="email" name="email" type="email" value="{{ old('email', $request->email) }}" placeholder="Enter your email address" autocomplete="email" required autofocus>
            </div>
            @error('email')
                <span class="mt-1 block text-xs text-red-600" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-5">
            <label for="password">New password</label>
            <div class="relative">
                <span data-icon class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg></span>
                <input aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" id="password" name="password" type="password" placeholder="At least 8 characters" autocomplete="new-password" required>
                <button type="button" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-sm text-gray-400 transition-colors hover:text-primary-dark focus:outline-none focus-visible:text-primary-dark" aria-controls="password" aria-pressed="false" aria-label="Show or hide password" data-password-toggle="password"><svg data-eye-open class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><svg data-eye-closed class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></button>
            </div>
            @error('password')
                <span class="mt-1 block text-xs text-red-600" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-5">
            <label for="password_confirmation">Confirm password</label>
            <div class="relative">
                <span data-icon class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg></span>
                <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat the new password" autocomplete="new-password" required>
                <button type="button" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-sm text-gray-400 transition-colors hover:text-primary-dark focus:outline-none focus-visible:text-primary-dark" aria-controls="password_confirmation" aria-pressed="false" aria-label="Show or hide password" data-password-toggle="password_confirmation"><svg data-eye-open class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><svg data-eye-closed class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></button>
            </div>
        </div>

        <div class="mt-7">
            <x-essentials::submit label="Reset Password" busy="Resetting..." />
        </div>

        <p class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary-dark underline-offset-2 hover:text-primary hover:underline">Back to sign in</a>
        </p>
    </form>
@endsection
