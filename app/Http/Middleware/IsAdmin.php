<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::user()->hasRole(Role::ADMIN)) {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

        return $next($request);
    }
}
