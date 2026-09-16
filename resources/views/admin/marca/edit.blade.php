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
@endsection
