<?php

namespace Nyumbani\Essentials\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use LogicException;
use Nyumbani\Essentials\Contracts\HasHomeRoute;

class LoginResponse implements LoginResponseContract
{
    /**
     * Redirect to the intended page, else the user's home route. A user model without one fails outside production.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function toResponse($request)
    {
        //  Get the signed-in user
        $user = $request->user();

        //  Send users who know their home there
        if ($user instanceof HasHomeRoute) {
            return redirect()->intended($user->homeRoute());
        }

        //  Describe the problem
        $message = sprintf('[%s] must implement [%s] so users can be sent home after login.', $user::class, HasHomeRoute::class);

        //  Fail loudly where a developer will see it
        throw_unless(app()->isProduction(), new LogicException($message));

        //  Never break sign-in in production: warn and use Fortify's home
        logger()->warning($message);

        //  Send the user home
        return redirect()->intended(config('fortify.home', '/'));
    }
}
