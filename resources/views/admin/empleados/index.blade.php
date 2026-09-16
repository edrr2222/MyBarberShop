@extends('layouts.admin', ['title' => 'Empleados'])

@section('topbar-actions')
    <a href="{{ route('admin.empleados.create') }}" class="btn btn-chico">+ Nuevo empleado</a>
@endsection

@section('content')
<div class="panel">
    @if ($empleados->isEmpty())
        <p class="vacio">Todavía no hay empleados registrados.</p>
    @else
        <table>
            <thead>
                <tr><th>Nombre</th><th>Usuario</th><th>Sede</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($empleados as $empleado)
                    <tr>
                        <td>{{ $empleado->nombre }}</td>
                        <td>{{ $empleado->usuario }}</td>
                        <td>{{ $empleado->sede->nombre ?? '—' }}</td>
                        <td><span class="badge {{ $empleado->estado ? 'badge-activo' : 'badge-inactivo' }}">{{ $empleado->estado ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.empleados.edit', $empleado) }}" class="btn btn-secundario btn-chico">Editar</a>
                            <form method="POST" action="{{ route('admin.empleados.toggle', $empleado) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-secundario btn-chico">{{ $empleado->estado ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
