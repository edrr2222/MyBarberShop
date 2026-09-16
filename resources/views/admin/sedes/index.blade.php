@extends('layouts.admin', ['title' => 'Sedes'])

@section('topbar-actions')
    @if (auth('admin')->user()->esAdminDeBarberiaCompleta())
        <a href="{{ route('admin.sedes.create') }}" class="btn btn-chico">+ Nueva sede</a>
    @endif
@endsection

@section('content')
<div class="panel">
    @if ($sedes->isEmpty())
        <p class="vacio">Todavía no hay sedes registradas.</p>
    @else
        <table>
            <thead>
                <tr><th>Nombre</th><th>Dirección</th><th>Teléfono</th><th>Link del cliente</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($sedes as $sede)
                    <tr>
                        <td>{{ $sede->nombre }}</td>
                        <td>{{ $sede->direccion ?? '—' }}</td>
                        <td>{{ $sede->telefono ?? '—' }}</td>
                        <td>
                            <input type="text" readonly value="{{ $links[$sede->id] }}" onclick="this.select()" style="width:220px;font-size:.78rem;padding:6px 8px">
                        </td>
                        <td><span class="badge {{ $sede->estado ? 'badge-activo' : 'badge-inactivo' }}">{{ $sede->estado ? 'Activa' : 'Inactiva' }}</span></td>
                        <td style="text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.sedes.edit', $sede) }}" class="btn btn-secundario btn-chico">Editar</a>
                            @if (auth('admin')->user()->esAdminDeBarberiaCompleta())
                                <form method="POST" action="{{ route('admin.sedes.toggle', $sede) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-secundario btn-chico">{{ $sede->estado ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
