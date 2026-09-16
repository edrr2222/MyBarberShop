@extends('layouts.admin', ['title' => 'Nuevo empleado'])

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.empleados.store') }}">
        @csrf
        <label for="sede_id">Sede</label>
        <select id="sede_id" name="sede_id" required>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" @selected(old('sede_id') == $sede->id)>{{ $sede->nombre }}</option>
            @endforeach
        </select>

        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>

        <label for="usuario">Usuario (para iniciar sesión)</label>
        <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required minlength="6">

        <label for="descripcion">Descripción (opcional)</label>
        <textarea id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>

        <button type="submit">Crear empleado</button>
        <a href="{{ route('admin.empleados.index') }}" class="btn btn-secundario">Cancelar</a>
    </form>
</div>
@endsection
