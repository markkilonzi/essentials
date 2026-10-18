<?php

namespace Nyumbani\Essentials\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class AddContext
{
    /**
     * Request fields kept out of the log context.
     *
     * @var list<string>
     */
    protected const SENSITIVE_FIELDS = ['password', 'password_confirmation', 'current_password', '_token'];

    /**
     * Add the user, organization and request to the log context.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handle(Request $request, Closure $next): Response
    {
        //  Filter the request to only include serializable, non-sensitive data
        $serializableData = $request->collect()
            ->except(self::SENSITIVE_FIELDS)
            ->filter(fn ($value) => ! ($value instanceof UploadedFile) && ! is_resource($value))
            ->toArray();

        //  Get the user
        $user = $request->user();

        //  Get the organization
        $organization = $user->organization ?? null;

        //  Add the logged in user
        Context::add('name', $user->name ?? 'Guest');

        //  Add the logged in organization
        Context::add('organization', $organization->name ?? 'Guest');

        //  Add the request payload to the context
        Context::add('request', $serializableData);

        //  Proceed with the request
        return $next($request);
    }
}
