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
@endsection
