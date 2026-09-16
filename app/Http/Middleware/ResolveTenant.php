<?php

namespace App\Http\Middleware;

use App\Models\Barberia;
use App\Models\Sede;
use Closure;
use Illuminate\Http\Request;

/**
 * Resuelve barbería + sede a partir de la ruta /b/{barberiaSlug}/{sedeSlug}
 * y las deja disponibles en el request (y en sesión) para el resto del flujo.
 * No da pistas si el slug no existe: solo 404 genérico.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $barberiaSlug = $request->route('barberiaSlug');
        $sedeSlug = $request->route('sedeSlug');

        $barberia = Barberia::where('slug', $barberiaSlug)->where('estado', true)->first();

        if (! $barberia) {
            abort(404);
        }

        $sede = Sede::where('barberia_id', $barberia->id)
            ->where('slug', $sedeSlug)
            ->where('estado', true)
            ->first();

        if (! $sede) {
            abort(404);
        }

        $request->attributes->set('barberia', $barberia);
        $request->attributes->set('sede', $sede);

        session([
            'barberia_id' => $barberia->id,
            'sede_id' => $sede->id,
        ]);

        return $next($request);
    }
}
