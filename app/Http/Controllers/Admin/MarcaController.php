<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopedAdmin;
use App\Http\Controllers\Controller;
use App\Models\Barberia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    use ScopedAdmin;

    public function edit()
    {
        abort_unless($this->esAdminDeBarberiaCompleta(), 403, 'Solo el administrador de la barbería puede editar la marca.');

        $barberia = Barberia::findOrFail($this->barberiaId());

        return view('admin.marca.edit', compact('barberia'));
    }

    public function update(Request $request)
    {
        abort_unless($this->esAdminDeBarberiaCompleta(), 403, 'Solo el administrador de la barbería puede editar la marca.');

        $barberia = Barberia::findOrFail($this->barberiaId());

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'color_primario' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'color_secundario' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'color_terciario' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($barberia->logo_url) {
                Storage::disk('public')->delete($barberia->logo_url);
            }
            $validated['logo_url'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);

        $barberia->update($validated);

        return back()->with('status', 'Marca actualizada correctamente.');
    }
}
