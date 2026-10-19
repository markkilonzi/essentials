<?php

namespace Nyumbani\Essentials\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Lab404\Impersonate\Services\ImpersonateManager;
use Nyumbani\Essentials\Contracts\HasHomeRoute;

class LeaveImpersonationController
{
    /**
     * End the impersonation and send the restored user to their home route.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __invoke(ImpersonateManager $manager): RedirectResponse
    {
        //  Sign back in as the impersonator
        $manager->leave();

        //  Send them home, or to the root when their user has no home route
        return redirect()->to(user() instanceof HasHomeRoute ? user()->homeRoute() : url('/'));
    }
}
