<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopedAdmin;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    use ScopedAdmin;

    public function index(Request $request)
    {
        $query = Client::where('barberia_id', $this->barberiaId())->with('tarjetaActiva');

        if ($busqueda = $request->query('q')) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'ilike', "%{$busqueda}%")
                    ->orWhere('cedula', 'ilike', "%{$busqueda}%");
            });
        }

        $clientes = $query->orderBy('nombre')->paginate(20)->withQueryString();

        return view('admin.clientes.index', ['clientes' => $clientes, 'q' => $busqueda ?? '']);
    }

    public function toggle(Client $client)
    {
        abort_unless($client->barberia_id === $this->barberiaId(), 403);

        $client->update(['estado' => ! $client->estado]);

        return back()->with('status', 'Estado del cliente actualizado.');
    }
}
