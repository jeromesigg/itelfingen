<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Zugriff auf den Admin-Bereich nach Stufe: role:team, role:verwalter oder role:admin.
 * Gäste werden zum Login geschickt, eingeloggte Benutzer ohne Recht bekommen 403.
 */
class Role
{
    public function handle(Request $request, Closure $next, string $level): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        abort_unless(Auth::user()->hasRoleLevel($level), 403);

        return $next($request);
    }
}
