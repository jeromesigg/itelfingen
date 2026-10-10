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
        // config() statt env(): env() liefert bei gecachter Config null, und null === null liess jeden durch.
        $user = (string) config('services.deploy.user');
        $pass = (string) config('services.deploy.password');

        if (
            $user === '' || $pass === '' ||
            ! hash_equals($user, (string) $request->getUser()) ||
            ! hash_equals($pass, (string) $request->getPassword())
        ) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Deploy"',
            ]);
        }

        return $next($request);
    }
}
