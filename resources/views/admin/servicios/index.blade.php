@extends('layouts.admin', ['title' => 'Servicios'])

@section('topbar-actions')
    <a href="{{ route('admin.servicios.create') }}" class="btn btn-chico">+ Nuevo servicio</a>
@endsection

@section('content')
<div class="panel">
    @if ($servicios->isEmpty())
        <p class="vacio">Todavía no hay servicios registrados.</p>
    @else
        <table>
            <thead>
                <tr><th>Nombre</th><th>Precio</th><th>Duración</th><th>Aplica sello</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($servicios as $servicio)
                    <tr>
                        <td>{{ $servicio->nombre }}</td>
                        <td>${{ number_format($servicio->precio, 0, ',', '.') }}</td>
                        <td>{{ $servicio->duracion_minutos ? $servicio->duracion_minutos.' min' : '—' }}</td>
                        <td>{{ $servicio->aplica_sello ? 'Sí' : 'No' }}</td>
                        <td><span class="badge {{ $servicio->estado ? 'badge-activo' : 'badge-inactivo' }}">{{ $servicio->estado ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.servicios.edit', $servicio) }}" class="btn btn-secundario btn-chico">Editar</a>
                            <form method="POST" action="{{ route('admin.servicios.toggle', $servicio) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-secundario btn-chico">{{ $servicio->estado ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
