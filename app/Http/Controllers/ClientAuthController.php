<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientAuthController extends Controller
{
    public function register(Request $request)
    {
        $barberiaId = session('barberia_id');

        $validator = Validator::make($request->all(), [
            'cedula' => 'required|string|max:20',
            'nombre' => 'required|string|max:150',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $existe = Client::where('barberia_id', $barberiaId)
            ->where('cedula', $request->cedula)
            ->exists();

        if ($existe) {
            return back()->withErrors(['cedula' => 'Ya existe una cuenta con esta cédula en esta barbería.']);
        }

        $client = Client::create([
            'barberia_id' => $barberiaId,
            'cedula' => $request->cedula,
            'nombre' => $request->nombre,
            'password' => Hash::make($request->password),
        ]);

        auth('client')->login($client);

        return redirect()->route('client.qr');
    }

    public function login(Request $request)
    {
        $barberiaId = session('barberia_id');

        $request->validate([
            'cedula' => 'required|string',
            'password' => 'required|string',
        ]);

        $client = Client::where('barberia_id', $barberiaId)
            ->where('cedula', $request->cedula)
            ->first();

        if (! $client || ! Hash::check($request->password, $client->password)) {
            return back()->withErrors(['cedula' => 'Cédula o contraseña incorrecta.']);
        }

        auth('client')->login($client);

        return redirect()->route('client.qr');
    }

    public function logout(Request $request)
    {
        auth('client')->logout();
        $request->session()->invalidate();

        return redirect('/');
    }
}
