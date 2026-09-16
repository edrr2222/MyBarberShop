@extends('layouts.app', ['barberia' => $barberia])

@section('content')
<div class="card">
    <h1>{{ $barberia->nombre }}</h1>
    <p style="color:#666;margin-top:-8px">{{ $sede->nombre }}</p>

    @if ($errors->any())
        <div class="errores">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="tabs">
        <button type="button" class="activo" onclick="mostrarTab('login')" id="tab-login">Ya tengo cuenta</button>
        <button type="button" onclick="mostrarTab('registro')" id="tab-registro">Soy nuevo</button>
    </div>

    <form method="POST" action="{{ route('client.login') }}" id="form-login">
        @csrf
        <label for="login-cedula">Cédula</label>
        <input type="text" id="login-cedula" name="cedula" value="{{ old('cedula') }}" required>

        <label for="login-password">Contraseña</label>
        <input type="password" id="login-password" name="password" required>

        <button type="submit">Entrar</button>
    </form>

    <form method="POST" action="{{ route('client.register') }}" id="form-registro" style="display:none">
        @csrf
        <label for="reg-nombre">Nombre completo</label>
        <input type="text" id="reg-nombre" name="nombre" value="{{ old('nombre') }}" required>

        <label for="reg-cedula">Cédula</label>
        <input type="text" id="reg-cedula" name="cedula" value="{{ old('cedula') }}" required>

        <label for="reg-password">Contraseña</label>
        <input type="password" id="reg-password" name="password" required minlength="6">

        <label for="reg-password-confirm">Confirmar contraseña</label>
        <input type="password" id="reg-password-confirm" name="password_confirmation" required minlength="6">

        <button type="submit">Crear cuenta</button>
    </form>
</div>

<script>
    function mostrarTab(tab) {
        document.getElementById('form-login').style.display = tab === 'login' ? 'block' : 'none';
        document.getElementById('form-registro').style.display = tab === 'registro' ? 'block' : 'none';
        document.getElementById('tab-login').classList.toggle('activo', tab === 'login');
        document.getElementById('tab-registro').classList.toggle('activo', tab === 'registro');
    }
</script>
@endsection
