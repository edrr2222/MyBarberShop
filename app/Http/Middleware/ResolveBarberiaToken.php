<?php

namespace App\Http\Middleware;

use App\Models\Barberia;
use App\Support\TenantToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Resuelve la barbería a partir del token de la URL (/{barberiaToken}/staff/...)
 * y la deja disponible en el request. No da pistas si el token no existe o
 * no decodifica a nada: solo 404 genérico.
 */
class ResolveBarberiaToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->route('barberiaToken');
        $barberiaId = TenantToken::decodeBarberia($token);

        $barberia = $barberiaId
            ? Barberia::where('id', $barberiaId)->where('estado', true)->first()
            : null;

        if (! $barberia) {
            abort(404);
        }

        $request->attributes->set('barberia', $barberia);

        // Para que route('empleado.scanner') etc. no necesiten pasar
        // 'barberiaToken' manualmente en cada vista/controller.
        URL::defaults(['barberiaToken' => $token]);

        return $next($request);
    }
}
