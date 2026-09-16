<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmpleadoAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['usuario' => 'required|string', 'password' => 'required|string']);

        $empleado = Empleado::where('usuario', $request->usuario)->first();

        if (! $empleado || ! Hash::check($request->password, $empleado->password)) {
            return back()->withErrors(['usuario' => 'Usuario o contraseña incorrecta.']);
        }

        $barberia = $request->attributes->get('barberia');
        if ($empleado->sede->barberia_id !== $barberia->id) {
            return back()->withErrors(['usuario' => 'Ese usuario no pertenece a esta barbería.']);
        }

        auth('empleado')->login($empleado);

        return redirect()->route('empleado.scanner');
    }

    public function logout(Request $request)
    {
        auth('empleado')->logout();
        $request->session()->invalidate();

        return redirect()->route('empleado.login');
    }
}
