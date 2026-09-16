@extends('layouts.admin', ['title' => 'Clientes'])

@section('topbar-actions')
    <form method="GET" style="display:flex;gap:8px">
        <input type="text" name="q" placeholder="Buscar por nombre o cédula" value="{{ $q }}" style="margin:0">
        <button type="submit" class="btn-chico" style="margin:0">Buscar</button>
    </form>
@endsection

@section('content')
<div class="panel">
    @if ($clientes->isEmpty())
        <p class="vacio">No se encontraron clientes.</p>
    @else
        <table>
            <thead>
                <tr><th>Nombre</th><th>Cédula</th><th>Sellos</th><th>Estado cuenta</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($clientes as $client)
                    <tr>
                        <td>{{ $client->nombre }}</td>
                        <td>{{ $client->cedula }}</td>
                        <td>
                            @if ($client->tarjetaActiva)
                                {{ $client->tarjetaActiva->sellos_actuales }} sellos
                                @if ($client->tarjetaActiva->estado === 'completada')
                                    <span class="badge badge-activo">Corte gratis</span>
                                @endif
                            @else
                                Sin tarjeta
                            @endif
                        </td>
                        <td><span class="badge {{ $client->estado ? 'badge-activo' : 'badge-inactivo' }}">{{ $client->estado ? 'Activa' : 'Inactiva' }}</span></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('admin.clientes.toggle', $client) }}">
                                @csrf
                                <button type="submit" class="btn btn-secundario btn-chico">{{ $client->estado ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:20px">{{ $clientes->links() }}</div>
    @endif
</div>
@endsection
