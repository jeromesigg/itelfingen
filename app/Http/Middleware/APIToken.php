<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIToken
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->api_token != env('API_KEY')) {
            return response()->json('Unauthorized', 401);
        }
        return $next($request);

    }
}
