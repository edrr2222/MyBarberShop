<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopedAdmin;
use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    use ScopedAdmin;

    public function index()
    {
        $empleados = Empleado::whereHas('sede', function ($q) {
            $q->where('barberia_id', $this->barberiaId());
            if (! $this->esAdminDeBarberiaCompleta()) {
                $q->where('id', $this->admin()->sede_id);
            }
        })->with('sede')->orderBy('nombre')->get();

        return view('admin.empleados.index', compact('empleados'));
    }

    public function create()
    {
        $sedes = $this->sedesDisponibles();

        return view('admin.empleados.create', compact('sedes'));
    }

    public function store(Request $request)
    {
        $sedeIds = $this->sedesDisponibles()->pluck('id');

        $validated = $request->validate([
            'sede_id' => ['required', 'integer', function ($attr, $value, $fail) use ($sedeIds) {
                if (! $sedeIds->contains((int) $value)) {
                    $fail('La sede seleccionada no es válida.');
                }
            }],
            'nombre' => 'required|string|max:150',
            'usuario' => ['required', 'string', 'max:50', function ($attr, $value, $fail) {
                if (Empleado::where('usuario', $value)->exists()) {
                    $fail('Ese usuario ya está en uso.');
                }
            }],
            'password' => 'required|string|min:6',
            'descripcion' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Empleado::create($validated);

        return redirect()->route('admin.empleados.index')->with('status', 'Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        $this->autorizarEmpleado($empleado);
        $sedes = $this->sedesDisponibles();

        return view('admin.empleados.edit', compact('empleado', 'sedes'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $this->autorizarEmpleado($empleado);
        $sedeIds = $this->sedesDisponibles()->pluck('id');

        $validated = $request->validate([
            'sede_id' => ['required', 'integer', function ($attr, $value, $fail) use ($sedeIds) {
                if (! $sedeIds->contains((int) $value)) {
                    $fail('La sede seleccionada no es válida.');
                }
            }],
            'nombre' => 'required|string|max:150',
            'usuario' => ['required', 'string', 'max:50', function ($attr, $value, $fail) use ($empleado) {
                if (Empleado::where('usuario', $value)->where('id', '!=', $empleado->id)->exists()) {
                    $fail('Ese usuario ya está en uso.');
                }
            }],
            'password' => 'nullable|string|min:6',
            'descripcion' => 'nullable|string',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $empleado->update($validated);

        return redirect()->route('admin.empleados.index')->with('status', 'Empleado actualizado correctamente.');
    }

    public function toggle(Empleado $empleado)
    {
        $this->autorizarEmpleado($empleado);

        $empleado->update(['estado' => ! $empleado->estado]);

        return back()->with('status', 'Estado del empleado actualizado.');
    }

    private function sedesDisponibles()
    {
        $query = Sede::where('barberia_id', $this->barberiaId())->where('estado', true);

        if (! $this->esAdminDeBarberiaCompleta()) {
            $query->where('id', $this->admin()->sede_id);
        }

        return $query->orderBy('nombre')->get();
    }

    private function autorizarEmpleado(Empleado $empleado): void
    {
        abort_unless($empleado->sede->barberia_id === $this->barberiaId(), 403);

        if (! $this->esAdminDeBarberiaCompleta()) {
            abort_unless($empleado->sede_id === $this->admin()->sede_id, 403);
        }
    }
}
