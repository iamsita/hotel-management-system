<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTypeMiddleware
{
    /**
     * Allow only users with specific roles.
     * Usage: ->middleware('type:admin,manager')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Not logged in
        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        // Optional: block inactive accounts
        if ($user->status !== 'active') {
            abort(403, 'Account is inactive.');
        }

        // No roles passed => deny (safe default)
        if (empty($roles)) {
            abort(403, 'Role not allowed.');
        }

        // Role check
        if (! in_array($user->type, $roles, true)) {
            abort(403, 'Forbidden.');
        }

        return $next($request);
    }
}
