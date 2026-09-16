<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel admin' }} · {{ $barberia->nombre ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-primario: {{ $barberia->color_primario ?? '#241a14' }};
            --color-secundario: {{ $barberia->color_secundario ?? '#f4ead9' }};
            --color-acento: #c0392b;
            --color-acento-2: #2c3e50;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Poppins', system-ui, sans-serif;
            background: var(--color-secundario); color: #241a14; min-height: 100vh;
        }
        h1, h2, .display { font-family: 'Bebas Neue', sans-serif; letter-spacing: .04em; font-weight: 400; }
        .layout { display: flex; min-height: 100vh; }
        aside {
            width: 230px; flex-shrink: 0; background: var(--color-primario); color: #fff;
            display: flex; flex-direction: column; padding: 22px 0;
        }
        aside .marca { display: flex; align-items: center; gap: 10px; padding: 0 20px 20px; font-family: 'Bebas Neue', sans-serif; font-size: 1.4rem; letter-spacing: .05em; border-bottom: 1px solid rgba(255,255,255,.15); margin-bottom: 10px; }
        aside .marca img { height: 32px; width: 32px; border-radius: 50%; object-fit: cover; }
        aside nav { display: flex; flex-direction: column; gap: 2px; flex: 1; }
        aside nav a {
            color: rgba(255,255,255,.8); text-decoration: none; padding: 12px 20px;
            font-size: .95rem; font-weight: 500; border-left: 3px solid transparent;
        }
        aside nav a:hover { background: rgba(255,255,255,.06); color: #fff; }
        aside nav a.activo { background: rgba(255,255,255,.1); color: #fff; border-left-color: var(--color-acento); font-weight: 600; }
        aside .salir { padding: 16px 20px 0; border-top: 1px solid rgba(255,255,255,.15); margin-top: 10px; }
        aside .salir button { width: 100%; background: transparent; border: 1px solid rgba(255,255,255,.4); color: #fff; padding: 9px; border-radius: 999px; cursor: pointer; font-family: inherit; }
        .barber-stripe-v { width: 5px; background: repeating-linear-gradient(0deg, var(--color-acento), var(--color-acento) 12px, #fff 12px, #fff 24px, var(--color-acento-2) 24px, var(--color-acento-2) 36px, #fff 36px, #fff 48px); }
        .content { flex: 1; padding: 32px 40px; max-width: 980px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .topbar h1 { margin: 0; font-size: 2rem; color: var(--color-primario); }
        .panel {
            background: #fff; border-radius: 24px 8px 24px 8px; padding: 28px;
            box-shadow: 0 8px 24px rgba(36,26,20,.08); border-top: 4px solid var(--color-acento);
        }
        label { display: block; margin: 14px 0 6px; font-size: .82rem; font-weight: 600; color: #55483f; text-transform: uppercase; letter-spacing: .03em; }
        input, select, textarea {
            width: 100%; padding: 11px 14px; border: 1.5px solid #e4d9c8; border-radius: 12px;
            font-size: .95rem; font-family: inherit; background: #fffdfa;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--color-primario); }
        input[type=color] { padding: 4px; height: 44px; cursor: pointer; }
        input[type=checkbox] { width: auto; }
        button, .btn {
            display: inline-block; margin-top: 20px; padding: 11px 24px; background: var(--color-primario);
            color: #fff; border: none; border-radius: 999px; font-size: .95rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
        }
        .btn-secundario { background: transparent; color: var(--color-primario); border: 1.5px solid var(--color-primario); }
        .btn-chico { padding: 7px 16px; font-size: .82rem; margin-top: 0; }
        .btn-peligro-tenue { background: transparent; color: #b00020; border: 1.5px solid #f0c2c8; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #8a7c6c; padding: 10px 12px; border-bottom: 2px solid #eee1d0; }
        table td { padding: 12px; border-bottom: 1px solid #f1e6d6; font-size: .92rem; vertical-align: middle; }
        .badge { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: .72rem; font-weight: 600; }
        .badge-activo { background: #e8f6ee; color: #1e7e42; }
        .badge-inactivo { background: #f4f0ea; color: #8a7c6c; }
        .exito { background: #e8f6ee; color: #1e7e42; padding: 12px 16px; border-radius: 14px; margin-bottom: 18px; font-size: .9rem; }
        .errores { background: #fdecea; color: #b00020; padding: 12px 16px; border-radius: 14px; margin-bottom: 18px; font-size: .9rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        .vacio { color: #a99a89; padding: 30px 0; text-align: center; }
        .scissors-divider { display: flex; align-items: center; gap: 10px; color: #c9b79c; }
        .scissors-divider::before, .scissors-divider::after { content: ''; flex: 1; height: 1px; background: #e4d9c8; }
        @media (max-width: 720px) {
            .layout { flex-direction: column; }
            aside { width: 100%; }
            .barber-stripe-v { display: none; }
            .content { padding: 24px 18px; }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside>
        <div class="marca">
            @if (!empty($barberia->logo_url))
                <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($barberia->logo_url) }}" alt="">
            @endif
            {{ $barberia->nombre ?? 'Admin' }}
        </div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'activo' : '' }}">Resumen</a>
            @if (auth('admin')->user()->esAdminDeBarberiaCompleta())
                <a href="{{ route('admin.marca.edit') }}" class="{{ request()->routeIs('admin.marca.*') ? 'activo' : '' }}">Marca</a>
            @endif
            <a href="{{ route('admin.sedes.index') }}" class="{{ request()->routeIs('admin.sedes.*') ? 'activo' : '' }}">Sedes</a>
            <a href="{{ route('admin.empleados.index') }}" class="{{ request()->routeIs('admin.empleados.*') ? 'activo' : '' }}">Empleados</a>
            <a href="{{ route('admin.servicios.index') }}" class="{{ request()->routeIs('admin.servicios.*') ? 'activo' : '' }}">Servicios</a>
            <a href="{{ route('admin.clientes.index') }}" class="{{ request()->routeIs('admin.clientes.*') ? 'activo' : '' }}">Clientes</a>
        </nav>
        <div class="salir">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Salir</button>
            </form>
        </div>
    </aside>
    <div class="barber-stripe-v"></div>
    <div class="content">
        <div class="topbar">
            <h1>{{ $title ?? '' }}</h1>
            @yield('topbar-actions')
        </div>

        @if (session('status'))
            <div class="exito">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="errores">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>
</div>
</body>
</html>
