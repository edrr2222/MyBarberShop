@extends('layouts.admin', ['title' => 'Editar empleado'])

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.empleados.update', $empleado) }}">
        @csrf
        @method('PUT')

        <label for="sede_id">Sede</label>
        <select id="sede_id" name="sede_id" required>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" @selected(old('sede_id', $empleado->sede_id) == $sede->id)>{{ $sede->nombre }}</option>
            @endforeach
        </select>

        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" required>

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" value="{{ old('usuario', $empleado->usuario) }}" required>

        <label for="password">Nueva contraseña (dejar vacío para no cambiarla)</label>
        <input type="password" id="password" name="password" minlength="6">

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $empleado->descripcion) }}</textarea>

        <button type="submit">Guardar cambios</button>
        <a href="{{ route('admin.empleados.index') }}" class="btn btn-secundario">Cancelar</a>
    </form>
</div>

<div class="panel" style="margin-top:24px">
    <h2 style="margin-top:0;font-size:1.3rem">Redes sociales</h2>
    <p style="color:#8a7c6c;font-size:.85rem;margin-top:-8px">
        Se muestran como íconos al final del perfil público del barbero.
    </p>

    @if ($empleado->redesSociales->isEmpty())
        <p class="vacio" style="padding:12px 0">Todavía no hay redes sociales agregadas.</p>
    @else
        <table style="margin-bottom:10px">
            <tbody>
                @foreach ($empleado->redesSociales as $red)
                    <tr>
                        <td style="width:120px">{{ \App\Models\EmpleadoRedSocial::PLATAFORMAS[$red->plataforma] ?? $red->plataforma }}</td>
                        <td><a href="{{ $red->url }}" target="_blank" rel="noopener">{{ $red->url }}</a></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('admin.empleados.redes-sociales.destroy', [$empleado, $red]) }}">
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

    <form method="POST" action="{{ route('admin.empleados.redes-sociales.store', $empleado) }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
        @csrf
        <div>
            <label for="plataforma" style="margin-top:0">Plataforma</label>
            <select id="plataforma" name="plataforma">
                @foreach (\App\Models\EmpleadoRedSocial::PLATAFORMAS as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:200px">
            <label for="url" style="margin-top:0">URL</label>
            <input type="url" id="url" name="url" placeholder="https://instagram.com/...">
        </div>
        <button type="submit" class="btn-chico">Agregar</button>
    </form>
</div>
@endsection
