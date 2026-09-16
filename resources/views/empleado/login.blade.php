@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Ingreso de barberos</h1>

    @if ($errors->any())
        <div class="errores">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ url('/staff/login') }}">
        @csrf
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" required autofocus>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>
</div>
@endsection
