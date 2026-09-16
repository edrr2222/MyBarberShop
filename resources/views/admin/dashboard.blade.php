@extends('layouts.app')

@section('header-actions')
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn-secundario" style="margin:0;color:#fff;border-color:#fff">Salir</button>
    </form>
@endsection

@section('content')
<div class="card">
    <h1>Hola, {{ $admin->nombre }}</h1>
    <p style="color:#666">
        {{ $admin->esAdminDeBarberiaCompleta() ? 'Administras toda la barbería.' : 'Administras una sede específica.' }}
    </p>
    <p style="color:#999;font-size:.85rem">
        El panel de gestión (sedes, empleados, servicios, colores) todavía no está implementado — ver README, sección Pendiente.
    </p>
</div>
@endsection
