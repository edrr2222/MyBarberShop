<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopedAdmin;
use App\Http\Controllers\Controller;
use App\Models\Barberia;
use App\Models\BarberiaRedSocial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarberiaRedSocialController extends Controller
{
    use ScopedAdmin;

    public function store(Request $request)
    {
        abort_unless($this->esAdminDeBarberiaCompleta(), 403);

        $validated = $request->validate([
            'plataforma' => ['required', Rule::in(array_keys(BarberiaRedSocial::PLATAFORMAS))],
            'url' => 'required|url|max:255',
        ]);

        $barberia = Barberia::findOrFail($this->barberiaId());
        $validated['barberia_id'] = $barberia->id;
        $validated['orden'] = $barberia->redesSociales()->max('orden') + 1;

        BarberiaRedSocial::create($validated);

        return back()->with('status', 'Red social agregada.');
    }

    public function destroy(BarberiaRedSocial $redSocial)
    {
        abort_unless($this->esAdminDeBarberiaCompleta(), 403);
        abort_unless($redSocial->barberia_id === $this->barberiaId(), 403);

        $redSocial->delete();

        return back()->with('status', 'Red social eliminada.');
    }
}
