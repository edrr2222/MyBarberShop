@extends('layouts.admin', ['title' => 'Nueva sede'])

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.sedes.store') }}">
        @csrf
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required autofocus>

        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}">

        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}">

        <button type="submit">Crear sede</button>
        <a href="{{ route('admin.sedes.index') }}" class="btn btn-secundario">Cancelar</a>
    </form>
</div>
@endsection
