<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user || (! empty($roles) && ! in_array($user->rol, $roles, true))) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return $next($request);
    }
}
