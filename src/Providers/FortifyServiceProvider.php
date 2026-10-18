<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;
use Nyumbani\Essentials\Actions\Fortify\ResetUserPassword;
use Nyumbani\Essentials\Http\Responses\LoginResponse;
use Nyumbani\Essentials\Http\Responses\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register the default login and logout responses and the login throttle
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function register(): void
    {
        //  Define the login response
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        //  Define the logout response
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);

        //  Apply the login throttle; Fortify reads it before boot
        config(['fortify.limiters.login' => config('fortify.limiters.login') ?? 'login']);
    }

    /**
     * Set the password rules, reset action, login throttle and auth views
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Set the default password rules
        Password::defaults(fn () => Password::min(8));

        //  Define the password reset action
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        //  Throttle login attempts per username and IP address
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(config('essentials.fortify.max_attempts', 5))->by(
            str($request->input(Fortify::username()))->lower().'|'.$request->ip()
        ));

        //  Get the folder of the auth views
        $views = config('essentials.fortify.views', 'auth.pages');

        //  Resolve a view from the app's folder, falling back to the package's plain one
        $view = fn (string $name) => view()->exists("{$views}.{$name}") ? "{$views}.{$name}" : "essentials::auth.{$name}";

        //  Define the login view
        Fortify::loginView(fn () => view($view('login')));

        //  Define the forgot password view
        Fortify::requestPasswordResetLinkView(fn () => view($view('forgot-password')));

        //  Define the reset password view
        Fortify::resetPasswordView(fn (Request $request) => view($view('reset-password'), [
            'request' => $request,
        ]));
    }
}
