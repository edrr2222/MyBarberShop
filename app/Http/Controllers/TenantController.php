<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Landing del cliente al entrar por el QR fijo de la sede.
 * La barbería/sede ya vienen resueltas en $request (ver ResolveTenant).
 */
class TenantController extends Controller
{
    public function show(Request $request)
    {
        $barberia = $request->attributes->get('barberia');
        $sede = $request->attributes->get('sede');

        // Si el cliente ya tiene sesión activa (guard 'client'), lo mandamos
        // directo a la pantalla del QR de fidelidad. Si no, a login/registro.
        if (auth('client')->check()) {
            return redirect()->route('client.qr');
        }

        return view('client.landing', compact('barberia', 'sede'));
    }
}
