<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles  One or more roles.
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect('/login'); // Redirect to login if not authenticated
        }

        // Get the authenticated user's role
        $user = Auth::user();

        // Check if the user's role matches one of the allowed roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized'); // Show 403 if the user doesn't have the required role
        }

        return $next($request);
    }
}
