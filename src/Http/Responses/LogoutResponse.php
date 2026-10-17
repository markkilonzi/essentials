<?php

namespace Nyumbani\Essentials\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * Redirect to the login page after signing out.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function toResponse($request)
    {
        return redirect()->route('login');
    }
}
