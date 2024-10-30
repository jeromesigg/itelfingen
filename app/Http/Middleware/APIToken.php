<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->api_token != config('app.api_token')) {
            return response()->json('Token '.$request->api_token.' is not valid', 401);
        }

        return $next($request);
    }
}
