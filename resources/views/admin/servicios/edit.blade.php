@extends('layouts.admin', ['title' => 'Editar servicio'])

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.servicios.update', $servicio) }}">
        @csrf
        @method('PUT')

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $servicio->nombre) }}" required autofocus>

        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" required>
            @foreach (\App\Models\Servicio::CATEGORIAS as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('categoria', $servicio->categoria) === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $servicio->descripcion) }}</textarea>

        <div class="grid-2">
            <div>
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" value="{{ old('precio', $servicio->precio) }}" min="0" step="100" required>
            </div>
            <div>
                <label for="duracion_minutos">Duración (min)</label>
                <input type="number" id="duracion_minutos" name="duracion_minutos" value="{{ old('duracion_minutos', $servicio->duracion_minutos) }}" min="0">
            </div>
        </div>

        <label for="orden">Orden de aparición</label>
        <input type="number" id="orden" name="orden" value="{{ old('orden', $servicio->orden) }}" min="0">

        <label style="display:flex;align-items:center;gap:8px;text-transform:none;margin-top:16px">
            <input type="checkbox" name="aplica_sello" value="1" @checked(old('aplica_sello', $servicio->aplica_sello))>
            Este servicio suma sello de fidelidad
        </label>

        <button type="submit">Guardar cambios</button>
        <a href="{{ route('admin.servicios.index') }}" class="btn btn-secundario">Cancelar</a>
    </form>
</div>
@endsection
