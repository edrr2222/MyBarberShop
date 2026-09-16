<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopedAdmin;
use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    use ScopedAdmin;

    public function index()
    {
        $servicios = Servicio::where('barberia_id', $this->barberiaId())
            ->orderBy('orden')
            ->get();

        return view('admin.servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('admin.servicios.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validado($request);
        $validated['barberia_id'] = $this->barberiaId();

        Servicio::create($validated);

        return redirect()->route('admin.servicios.index')->with('status', 'Servicio creado correctamente.');
    }

    public function edit(Servicio $servicio)
    {
        $this->autorizar($servicio);

        return view('admin.servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $this->autorizar($servicio);

        $servicio->update($this->validado($request));

        return redirect()->route('admin.servicios.index')->with('status', 'Servicio actualizado correctamente.');
    }

    public function toggle(Servicio $servicio)
    {
        $this->autorizar($servicio);

        $servicio->update(['estado' => ! $servicio->estado]);

        return back()->with('status', 'Estado del servicio actualizado.');
    }

    private function validado(Request $request): array
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'nullable|integer|min:0',
            'orden' => 'nullable|integer|min:0',
        ]);

        $validated['aplica_sello'] = $request->boolean('aplica_sello');

        return $validated;
    }

    private function autorizar(Servicio $servicio): void
    {
        abort_unless($servicio->barberia_id === $this->barberiaId(), 403);
    }
}
