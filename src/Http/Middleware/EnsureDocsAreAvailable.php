<?php

namespace Nyumbani\Essentials\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDocsAreAvailable
{
    /**
     * Show the docs only in the local environment
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handle(Request $request, Closure $next): Response
    {
        //  Answer as if the page does not exist anywhere but a developer's machine
        abort_unless(app()->isLocal(), 404);

        return $next($request);
    }
}
