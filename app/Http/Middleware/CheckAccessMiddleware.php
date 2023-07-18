<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $authorized_routes = get_authorized_routes($user);

        if (in_array($request->route()->getName(), $authorized_routes)) {
            return $next($request);
        }

        return abort(403, 'Unauthorized action.');
    }
}
