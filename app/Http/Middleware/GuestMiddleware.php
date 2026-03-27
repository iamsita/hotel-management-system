<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if ($user->type === 'guest') {
            return to_route('guest.dashboard')->with('info', 'You are already logged in as a guest.');
        }

        return to_route('dashboard')->with('info', 'You are already logged in as staff.');
    }
}
