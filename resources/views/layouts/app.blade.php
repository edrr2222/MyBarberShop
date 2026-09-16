<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'MyBarberShop') }}</title>
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
            --radio-organico: 42px 14px 42px 14px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Poppins', system-ui, sans-serif;
            background: var(--color-secundario);
            background-image:
                radial-gradient(circle at 100% 0%, color-mix(in srgb, var(--color-primario) 6%, transparent) 0%, transparent 45%),
                radial-gradient(circle at 0% 100%, color-mix(in srgb, var(--color-primario) 6%, transparent) 0%, transparent 45%);
            color: #241a14;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        h1, h2, h3, .display {
            font-family: 'Bebas Neue', 'Poppins', sans-serif;
            letter-spacing: .04em;
            font-weight: 400;
        }
        .barber-stripe {
            height: 6px;
            width: 100%;
            background: repeating-linear-gradient(
                -45deg,
                var(--color-acento), var(--color-acento) 12px,
                #fff 12px, #fff 24px,
                var(--color-acento-2) 24px, var(--color-acento-2) 36px,
                #fff 36px, #fff 48px
            );
            flex-shrink: 0;
        }
        header {
            background: var(--color-primario);
            color: #fff;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,.15);
        }
        header .marca {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
            letter-spacing: .06em;
            text-decoration: none;
            color: #fff;
        }
        header .marca img { height: 36px; width: 36px; object-fit: cover; border-radius: 50%; border: 2px solid rgba(255,255,255,.5); }
        header .marca svg { flex-shrink: 0; }
        header a { color: #fff; text-decoration: none; font-weight: 500; }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 16px;
        }
        .card {
            background: #fff;
            border-radius: var(--radio-organico);
            padding: 32px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(36,26,20,.12);
            position: relative;
            border-top: 5px solid var(--color-acento);
        }
        .card h1 { margin-top: 0; font-size: 2.1rem; color: var(--color-primario); }
        label { display: block; margin: 16px 0 6px; font-size: .85rem; font-weight: 600; color: #55483f; text-transform: uppercase; letter-spacing: .03em; }
        input, select, textarea {
            width: 100%; padding: 12px 14px; border: 1.5px solid #e4d9c8;
            border-radius: 14px; font-size: 1rem; font-family: inherit;
            background: #fffdfa;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--color-primario); }
        button, .btn {
            display: inline-block; margin-top: 22px; padding: 12px 28px;
            background: var(--color-primario); color: #fff; border: none;
            border-radius: 999px; font-size: 1rem; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: transform .15s ease, opacity .15s ease;
        }
        button:hover, .btn:hover { opacity: .9; transform: translateY(-1px); }
        .btn-secundario { background: transparent; color: var(--color-primario); border: 1.5px solid var(--color-primario); }
        .btn-chico { padding: 8px 18px; font-size: .85rem; margin-top: 0; }
        .btn-peligro { background: var(--color-acento); }
        .errores { background: #fdecea; color: #b00020; padding: 12px 16px; border-radius: 14px; margin-bottom: 14px; font-size: .9rem; }
        .exito { background: #e8f6ee; color: #1e7e42; padding: 12px 16px; border-radius: 14px; margin-bottom: 14px; font-size: .9rem; }
        .tabs { display: flex; gap: 8px; margin-bottom: 20px; }
        .tabs button { margin: 0; flex: 1; background: #f1e6d6; color: #55483f; border-radius: 999px; }
        .tabs button.activo { background: var(--color-primario); color: #fff; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: #8a7c6c; padding: 10px 12px; border-bottom: 2px solid #eee1d0; }
        table td { padding: 12px; border-bottom: 1px solid #f1e6d6; font-size: .95rem; }
        .badge { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: .75rem; font-weight: 600; }
        .badge-activo { background: #e8f6ee; color: #1e7e42; }
        .badge-inactivo { background: #f4f0ea; color: #8a7c6c; }
        .scissors-divider { display: flex; align-items: center; gap: 10px; color: #c9b79c; margin: 20px 0; }
        .scissors-divider::before, .scissors-divider::after { content: ''; flex: 1; height: 1px; background: #e4d9c8; }
        footer.marca-footer {
            padding: 22px 16px 30px;
            display: flex;
            justify-content: center;
        }
        footer.marca-footer .redes {
            display: flex;
            gap: 12px;
        }
        footer.marca-footer .redes a {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--color-primario); color: #fff;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none; opacity: .85;
        }
        footer.marca-footer .redes a:hover { opacity: 1; }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="marca" style="display:none"></a>
    <header>
        <span class="marca">
            @if (!empty($barberia->logo_url))
                <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($barberia->logo_url) }}" alt="">
            @else
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 5a2 2 0 100 4 2 2 0 000-4zM6 15a2 2 0 100 4 2 2 0 000-4z" stroke="#fff" stroke-width="1.6"/>
                    <path d="M7.5 8.5L19 19M19 5L7.5 15.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            @endif
            {{ $barberia->nombre ?? config('app.name', 'MyBarberShop') }}
        </span>
        @yield('header-actions')
    </header>
    <div class="barber-stripe"></div>
    <main>
        @yield('content')
    </main>
    @if (isset($barberia) && $barberia && $barberia->redesSociales->isNotEmpty())
        <footer class="marca-footer">
            <div class="redes">
                @foreach ($barberia->redesSociales as $red)
                    <a href="{{ $red->url }}" target="_blank" rel="noopener" title="{{ \App\Models\BarberiaRedSocial::PLATAFORMAS[$red->plataforma] ?? $red->plataforma }}">
                        @include('partials.red-social-icon', ['plataforma' => $red->plataforma])
                    </a>
                @endforeach
            </div>
        </footer>
    @endif
</body>
</html>
