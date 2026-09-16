@extends('layouts.admin', ['title' => 'Editar sede'])

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.sedes.update', $sede) }}">
        @csrf
        @method('PUT')

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $sede->nombre) }}" required autofocus>

        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $sede->direccion) }}">

        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $sede->telefono) }}">

        <button type="submit">Guardar cambios</button>
        <a href="{{ route('admin.sedes.index') }}" class="btn btn-secundario">Cancelar</a>
    </form>
</div>
@endsection
