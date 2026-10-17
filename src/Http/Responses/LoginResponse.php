<?php

namespace Nyumbani\Essentials\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Redirect to where the user was headed, otherwise to the user's homeRoute() when the model has one, otherwise to Fortify's home.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function toResponse($request)
    {
        //  Work out the user's home
        $home = method_exists($request->user(), 'homeRoute')
            ? $request->user()->homeRoute()
            : config('fortify.home', '/');

        //  Redirect to the intended page or the home
        return redirect()->intended($home);
    }
}
