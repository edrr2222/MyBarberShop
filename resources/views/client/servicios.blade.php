@extends('layouts.app', ['barberia' => $barberia])

@section('header-actions')
    <nav style="display:flex;gap:18px;align-items:center">
        <a href="{{ route('client.qr') }}" style="opacity:.85">Mi QR</a>
        <a href="{{ route('client.servicios') }}" style="opacity:1;font-weight:600">Servicios</a>
        <form method="POST" action="{{ route('client.logout') }}">
            @csrf
            <button type="submit" class="btn-secundario" style="margin:0;color:#fff;border-color:#fff">Salir</button>
        </form>
    </nav>
@endsection

@section('content')
<div class="card" style="max-width:560px">
    <h1>Nuestros servicios</h1>

    @if ($servicios->isEmpty())
        <p style="color:#777">Todavía no hay servicios cargados.</p>
    @else
        <div class="tabs" id="menu-categorias">
            @foreach (\App\Models\Servicio::CATEGORIAS as $clave => $etiqueta)
                @if ($servicios->has($clave))
                    <button type="button" class="{{ $loop->first ? 'activo' : '' }}" onclick="mostrarCategoria('{{ $clave }}', this)">{{ $etiqueta }}</button>
                @endif
            @endforeach
        </div>

        @foreach (\App\Models\Servicio::CATEGORIAS as $clave => $etiqueta)
            @if ($servicios->has($clave))
                <div class="seccion-servicios" data-categoria="{{ $clave }}" style="{{ $loop->first ? '' : 'display:none' }}">
                    @foreach ($servicios[$clave] as $servicio)
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:14px 0;border-bottom:1px solid #f1e6d6">
                            <div>
                                <strong>{{ $servicio->nombre }}</strong>
                                @if ($servicio->descripcion)
                                    <p style="margin:4px 0 0;color:#777;font-size:.88rem">{{ $servicio->descripcion }}</p>
                                @endif
                                @if ($servicio->duracion_minutos)
                                    <p style="margin:4px 0 0;color:#a99a89;font-size:.8rem">{{ $servicio->duracion_minutos }} min</p>
                                @endif
                            </div>
                            <div style="white-space:nowrap;font-weight:700;color:var(--color-primario)">
                                ${{ number_format($servicio->precio, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif
</div>

<script>
    function mostrarCategoria(categoria, boton) {
        document.querySelectorAll('.seccion-servicios').forEach(function (el) {
            el.style.display = el.dataset.categoria === categoria ? 'block' : 'none';
        });
        document.querySelectorAll('#menu-categorias button').forEach(function (b) {
            b.classList.toggle('activo', b === boton);
        });
    }
</script>
@endsection
