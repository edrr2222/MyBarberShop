@extends('layouts.app')

@section('header-actions')
    <div style="display:flex;gap:12px;align-items:center">
        <a href="{{ route('empleado.scanner') }}" style="opacity:.85">Scanner</a>
        <form method="POST" action="{{ route('empleado.logout') }}">
            @csrf
            <button type="submit" class="btn-secundario" style="margin:0;color:#fff;border-color:#fff">Salir</button>
        </form>
    </div>
@endsection

@section('content')
<div class="card">
    <h1>{{ $empleado->nombre }}</h1>
    <p style="color:#666">Usuario: {{ $empleado->usuario }}</p>
    @if ($empleado->descripcion)
        <p>{{ $empleado->descripcion }}</p>
    @endif
    <p style="color:#999;font-size:.85rem">Sede: {{ $empleado->sede->nombre ?? '—' }}</p>

    @if ($empleado->redesSociales->isNotEmpty())
        <div class="scissors-divider"></div>
        <div style="display:flex;gap:14px;justify-content:center">
            @foreach ($empleado->redesSociales as $red)
                <a href="{{ $red->url }}" target="_blank" rel="noopener"
                   style="width:42px;height:42px;border-radius:50%;background:var(--color-primario);color:#fff;display:flex;align-items:center;justify-content:center;text-decoration:none"
                   title="{{ \App\Models\EmpleadoRedSocial::PLATAFORMAS[$red->plataforma] ?? $red->plataforma }}">
                    @include('partials.red-social-icon', ['plataforma' => $red->plataforma])
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
