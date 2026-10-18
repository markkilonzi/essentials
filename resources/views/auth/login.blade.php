@extends('essentials::layouts.auth')

@section('title', 'Login to continue')
@section('heading', 'Login to continue')
@section('description', 'Please enter your login credentials to continue')

@section('content')
    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <x-essentials::field :name="config('fortify.username', 'email')" :label="prettify(config('fortify.username', 'email'))" :type="config('fortify.username', 'email') === 'email' ? 'email' : 'text'" icon="mail" :value="old(config('fortify.username', 'email'))" :placeholder="'Enter your '.strtolower(prettify(config('fortify.username', 'email')))" autocomplete="username" required autofocus />

        <x-essentials::field name="password" label="Password" type="password" icon="lock" toggle placeholder="Enter your password" autocomplete="current-password" required />

        <div class="mb-7 flex items-center justify-between">
            <label class="inline-flex cursor-pointer items-center" for="remember">
                <input id="remember" name="remember" type="checkbox" value="1" class="rounded border-gray-300 text-primary focus:ring-primary" @checked(old('remember'))>
                <span class="ml-2 text-sm font-medium text-gray-700">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-dark underline-offset-2 hover:text-primary hover:underline">Forgot password?</a>
        </div>

        <x-essentials::submit label="Sign In" busy="Signing in..." />
    </form>
@endsection
