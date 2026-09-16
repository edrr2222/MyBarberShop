<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'MyBarberShop') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-primario: {{ $barberia->color_primario ?? '#111111' }};
            --color-secundario: {{ $barberia->color_secundario ?? '#f4f4f4' }};
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background: var(--color-secundario);
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background: var(--color-primario);
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header a { color: #fff; text-decoration: none; font-weight: 600; }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 32px 16px;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 28px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
        }
        .card h1 { margin-top: 0; font-size: 1.4rem; }
        label { display: block; margin: 14px 0 6px; font-size: .9rem; font-weight: 600; }
        input, select {
            width: 100%; padding: 10px 12px; border: 1px solid #ddd;
            border-radius: 8px; font-size: 1rem;
        }
        button, .btn {
            display: inline-block; margin-top: 20px; padding: 10px 18px;
            background: var(--color-primario); color: #fff; border: none;
            border-radius: 8px; font-size: 1rem; cursor: pointer; text-decoration: none;
        }
        button:hover, .btn:hover { opacity: .9; }
        .btn-secundario { background: transparent; color: var(--color-primario); border: 1px solid var(--color-primario); }
        .errores { background: #fdecea; color: #b00020; padding: 10px 14px; border-radius: 8px; margin-bottom: 12px; font-size: .9rem; }
        .tabs { display: flex; gap: 8px; margin-bottom: 18px; }
        .tabs button { margin: 0; flex: 1; background: #eee; color: #333; }
        .tabs button.activo { background: var(--color-primario); color: #fff; }
    </style>
</head>
<body>
    <header>
        <span>{{ $barberia->nombre ?? config('app.name', 'MyBarberShop') }}</span>
        @yield('header-actions')
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
