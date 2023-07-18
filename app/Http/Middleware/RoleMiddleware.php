<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ... $roles): Response
    {
        foreach($roles as $role) {
            if ($request->user() && $request->user()->role == $role) {
                return $next($request);
            }
        }

        if (! $request->user()) {
            return redirect()->route('login');
        }

        abort(403, 'Unauthorized');
    }
}
