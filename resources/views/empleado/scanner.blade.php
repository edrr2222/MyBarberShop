@extends('layouts.app')

@section('header-actions')
    <div style="display:flex;gap:12px;align-items:center">
        <a href="{{ route('empleado.perfil') }}" style="opacity:.85">Perfil</a>
        <form method="POST" action="{{ route('empleado.logout') }}">
            @csrf
            <button type="submit" class="btn-secundario" style="margin:0;color:#fff;border-color:#fff">Salir</button>
        </form>
    </div>
@endsection

@section('content')
<div class="card" style="text-align:center">
    <h1>Escanear cliente</h1>

    <label for="servicio-select" style="text-align:left">Servicio realizado (opcional)</label>
    <select id="servicio-select" onchange="cambiarServicio(this.value)">
        <option value="">Sin especificar (aplica sello)</option>
        @foreach ($servicios as $servicio)
            <option value="{{ $servicio->id }}" @selected((string) $servicioId === (string) $servicio->id)>
                {{ $servicio->nombre }} @if(!$servicio->aplica_sello) (no aplica sello) @endif
            </option>
        @endforeach
    </select>

    <div id="empleado-scanner-app" data-servicio-id="{{ $servicioId }}" style="margin-top:20px"></div>
</div>

<script>
    function cambiarServicio(id) {
        const url = new URL(window.location.href);
        if (id) { url.searchParams.set('servicio_id', id); } else { url.searchParams.delete('servicio_id'); }
        window.location.href = url.toString();
    }
</script>
@endsection
