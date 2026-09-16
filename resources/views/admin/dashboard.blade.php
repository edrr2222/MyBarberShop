@extends('layouts.admin', ['title' => 'Resumen'])

@section('content')
<div class="panel">
    <h2 style="margin-top:0">Hola, {{ $admin->nombre }}</h2>
    <p style="color:#666">
        {{ $admin->esAdminDeBarberiaCompleta() ? 'Administras toda la barbería.' : 'Administras una sede específica.' }}
    </p>

    <label style="margin-top:18px">Link para tus barberos</label>
    <input type="text" readonly
        value="{{ route('empleado.login', ['barberiaToken' => \App\Support\TenantToken::barberia($admin->barberia_id)]) }}"
        onclick="this.select()" style="max-width:420px">

    <div class="scissors-divider" style="margin:24px 0"></div>
    <div class="grid-2" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px">
        @if ($admin->esAdminDeBarberiaCompleta())
            <a href="{{ route('admin.marca.edit') }}" class="btn btn-secundario" style="margin:0;text-align:center">Editar marca</a>
        @endif
        <a href="{{ route('admin.sedes.index') }}" class="btn btn-secundario" style="margin:0;text-align:center">Sedes</a>
        <a href="{{ route('admin.empleados.index') }}" class="btn btn-secundario" style="margin:0;text-align:center">Empleados</a>
        <a href="{{ route('admin.servicios.index') }}" class="btn btn-secundario" style="margin:0;text-align:center">Servicios</a>
        <a href="{{ route('admin.clientes.index') }}" class="btn btn-secundario" style="margin:0;text-align:center">Clientes</a>
    </div>
</div>
@endsection
