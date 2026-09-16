<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopedAdmin;
use App\Http\Controllers\Controller;
use App\Models\Sede;
use App\Support\TenantToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SedeController extends Controller
{
    use ScopedAdmin;

    public function index()
    {
        $query = Sede::where('barberia_id', $this->barberiaId());

        if (! $this->esAdminDeBarberiaCompleta()) {
            $query->where('id', $this->admin()->sede_id);
        }

        $sedes = $query->orderBy('nombre')->get();

        $barberiaToken = TenantToken::barberia($this->barberiaId());
        $links = $sedes->mapWithKeys(fn ($sede) => [
            $sede->id => route('tenant.landing', [
                'barberiaToken' => $barberiaToken,
                'sedeToken' => TenantToken::sede($sede->id),
            ]),
        ]);

        return view('admin.sedes.index', compact('sedes', 'links'));
    }

    public function create()
    {
        abort_unless($this->esAdminDeBarberiaCompleta(), 403);

        return view('admin.sedes.create');
    }

    public function store(Request $request)
    {
        abort_unless($this->esAdminDeBarberiaCompleta(), 403);

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
        ]);

        $validated['barberia_id'] = $this->barberiaId();
        $validated['slug'] = $this->slugUnico($validated['nombre']);

        Sede::create($validated);

        return redirect()->route('admin.sedes.index')->with('status', 'Sede creada correctamente.');
    }

    public function edit(Sede $sede)
    {
        $this->autorizarSede($sede);

        return view('admin.sedes.edit', compact('sede'));
    }

    public function update(Request $request, Sede $sede)
    {
        $this->autorizarSede($sede);

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
        ]);

        $sede->update($validated);

        return redirect()->route('admin.sedes.index')->with('status', 'Sede actualizada correctamente.');
    }

    public function toggle(Sede $sede)
    {
        abort_unless($this->esAdminDeBarberiaCompleta(), 403);
        abort_unless($sede->barberia_id === $this->barberiaId(), 403);

        $sede->update(['estado' => ! $sede->estado]);

        return back()->with('status', 'Estado de la sede actualizado.');
    }

    private function autorizarSede(Sede $sede): void
    {
        abort_unless($sede->barberia_id === $this->barberiaId(), 403);

        if (! $this->esAdminDeBarberiaCompleta()) {
            abort_unless($sede->id === $this->admin()->sede_id, 403);
        }
    }

    private function slugUnico(string $nombre): string
    {
        $base = Str::slug($nombre);
        $slug = $base;
        $i = 1;

        while (Sede::where('barberia_id', $this->barberiaId())->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
