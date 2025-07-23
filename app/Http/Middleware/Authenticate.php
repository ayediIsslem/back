<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Redirige quand l'utilisateur n'est pas authentifié.
     */
    protected function redirectTo($request)
{
    // Ne pas rediriger vers une route inexistante
    if (! $request->expectsJson()) {
        return null; // ou: abort(401, 'Unauthenticated');
    }
}

}
