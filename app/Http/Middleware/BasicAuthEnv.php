<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthEnv
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = env('DEPLOY_USER');
        $pass = env('DEPLOY_PASS');

        if (
            $request->getUser() !== $user ||
            $request->getPassword() !== $pass
        ) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Deploy"',
            ]);
        }

        return $next($request);
    }
}
