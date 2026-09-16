<?php

namespace App\Http\Middleware;

use App\Models\Barberia;
use App\Models\Sede;
use App\Support\TenantToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Resuelve barbería + sede a partir de la ruta /{barberiaToken}/{sedeToken}
 * y las deja disponibles en el request (y en sesión) para el resto del flujo.
 * No da pistas si el token no existe o no decodifica a nada: solo 404
 * genérico.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $barberiaToken = $request->route('barberiaToken');
        $sedeToken = $request->route('sedeToken');

        $barberiaId = TenantToken::decodeBarberia($barberiaToken);
        $barberia = $barberiaId
            ? Barberia::where('id', $barberiaId)->where('estado', true)->first()
            : null;

        if (! $barberia) {
            abort(404);
        }

        $sedeId = TenantToken::decodeSede($sedeToken);
        $sede = $sedeId
            ? Sede::where('id', $sedeId)->where('barberia_id', $barberia->id)->where('estado', true)->first()
            : null;

        if (! $sede) {
            abort(404);
        }

        $request->attributes->set('barberia', $barberia);
        $request->attributes->set('sede', $sede);

        session([
            'barberia_id' => $barberia->id,
            'sede_id' => $sede->id,
        ]);

        // Para que route('client.login')/route('client.register') no
        // necesiten pasar los tokens manualmente desde la vista.
        URL::defaults([
            'barberiaToken' => $barberiaToken,
            'sedeToken' => $sedeToken,
        ]);

        return $next($request);
    }
}
