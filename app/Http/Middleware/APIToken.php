<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIToken
{
    /**
     * Handle an incoming request.
     *
     * Ohne API_TOKEN in der Konfiguration ist die API gesperrt.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = (string) config('app.api_token');

        if ($token === '' || ! hash_equals($token, (string) $request->route('api_token'))) {
            return response()->json('Token is not valid', 401);
        }

        return $next($request);
    }
}
