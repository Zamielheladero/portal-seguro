<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SCRUM-24 (CMS-06) - Autorización por rol.
 *
 * Uso en rutas:  ->middleware('role:admin,vendedor')
 *
 * Si el usuario autenticado no tiene ninguno de los roles indicados,
 * responde 403. Debe ir DESPUÉS de 'auth' para que siempre haya usuario.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
