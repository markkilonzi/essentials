<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Nyumbani\Essentials\Actions\Fortify\ResetUserPassword;
use Nyumbani\Essentials\Http\Responses\LoginResponse;
use Nyumbani\Essentials\Http\Responses\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register the default responses, the login throttle and the password reset feature
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function register(): void
    {
        //  Define the login response
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        //  Define the logout response
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);

        //  Offer password reset only: no registration, verification, 2FA or passkeys
        config(['fortify.features' => [Features::resetPasswords()]]);

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

        //  Define the login view
        Fortify::loginView(fn () => view('essentials::auth.login'));

        //  Define the forgot password view
        Fortify::requestPasswordResetLinkView(fn () => view('essentials::auth.forgot-password'));

        //  Define the reset password view
        Fortify::resetPasswordView(fn (Request $request) => view('essentials::auth.reset-password', [
            'request' => $request,
        ]));
    }
}
