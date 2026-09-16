@extends('layouts.admin', ['title' => 'Marca'])

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.marca.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="nombre">Nombre de la barbería</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $barberia->nombre) }}" required>

        <div class="grid-2">
            <div>
                <label for="color_primario">Color primario</label>
                <input type="color" id="color_primario" name="color_primario" value="{{ old('color_primario', $barberia->color_primario) }}">
            </div>
            <div>
                <label for="color_secundario">Color secundario</label>
                <input type="color" id="color_secundario" name="color_secundario" value="{{ old('color_secundario', $barberia->color_secundario) }}">
            </div>
        </div>

        <label for="color_terciario">Color terciario (opcional)</label>
        <input type="color" id="color_terciario" name="color_terciario" value="{{ old('color_terciario', $barberia->color_terciario ?? '#c0392b') }}">

        <label for="logo">Logo</label>
        @if ($barberia->logo_url)
            <div style="margin-bottom:10px">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($barberia->logo_url) }}" alt="Logo actual" style="height:64px;border-radius:50%">
            </div>
        @endif
        <input type="file" id="logo" name="logo" accept="image/*">

        <button type="submit">Guardar cambios</button>
    </form>
</div>

<div class="panel" style="margin-top:24px">
    <h2 style="margin-top:0;font-size:1.3rem">Redes sociales de la barbería</h2>
    <p style="color:#8a7c6c;font-size:.85rem;margin-top:-8px">
        Se muestran como íconos al final de las páginas que ven tus clientes — úsalas para tu publicidad.
    </p>

    @if ($barberia->redesSociales->isEmpty())
        <p class="vacio" style="padding:12px 0">Todavía no hay redes sociales agregadas.</p>
    @else
        <table style="margin-bottom:10px">
            <tbody>
                @foreach ($barberia->redesSociales as $red)
                    <tr>
                        <td style="width:120px">{{ \App\Models\BarberiaRedSocial::PLATAFORMAS[$red->plataforma] ?? $red->plataforma }}</td>
                        <td><a href="{{ $red->url }}" target="_blank" rel="noopener">{{ $red->url }}</a></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('admin.marca.redes-sociales.destroy', $red) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secundario btn-chico">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <form method="POST" action="{{ route('admin.marca.redes-sociales.store') }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
        @csrf
        <div>
            <label for="plataforma" style="margin-top:0">Plataforma</label>
            <select id="plataforma" name="plataforma">
                @foreach (\App\Models\BarberiaRedSocial::PLATAFORMAS as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:200px">
            <label for="url" style="margin-top:0">URL</label>
            <input type="url" id="url" name="url" placeholder="https://instagram.com/tubarberia">
        </div>
        <button type="submit" class="btn-chico">Agregar</button>
    </form>
</div>
@endsection
