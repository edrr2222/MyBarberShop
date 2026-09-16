<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopedAdmin;
use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\EmpleadoRedSocial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmpleadoRedSocialController extends Controller
{
    use ScopedAdmin;

    public function store(Request $request, Empleado $empleado)
    {
        $this->autorizarEmpleado($empleado);

        $validated = $request->validate([
            'plataforma' => ['required', Rule::in(array_keys(EmpleadoRedSocial::PLATAFORMAS))],
            'url' => 'required|url|max:255',
        ]);

        $validated['empleado_id'] = $empleado->id;
        $validated['orden'] = $empleado->redesSociales()->max('orden') + 1;

        EmpleadoRedSocial::create($validated);

        return back()->with('status', 'Red social agregada.');
    }

    public function destroy(Empleado $empleado, EmpleadoRedSocial $redSocial)
    {
        $this->autorizarEmpleado($empleado);
        abort_unless($redSocial->empleado_id === $empleado->id, 403);

        $redSocial->delete();

        return back()->with('status', 'Red social eliminada.');
    }

    private function autorizarEmpleado(Empleado $empleado): void
    {
        abort_unless($empleado->sede->barberia_id === $this->barberiaId(), 403);

        if (! $this->esAdminDeBarberiaCompleta()) {
            abort_unless($empleado->sede_id === $this->admin()->sede_id, 403);
        }
    }
}
