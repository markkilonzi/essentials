<?php

namespace Nyumbani\Essentials\Contracts;

interface HasHomeRoute
{
    /**
     * Get the URL the user lands on after signing in, always a URL: a dashboard, or the unauthorized page for a user with no access.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function homeRoute(): string;
}
