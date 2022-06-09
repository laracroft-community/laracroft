<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class Permission
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $ability)
    {
        if (!$request->user()->hasPermission($ability)) {
            return response(['unauthorise access'], 401);
        }
        return $next($request);
    }
}
