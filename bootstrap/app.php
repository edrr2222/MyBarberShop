<?php

use App\Support\TenantToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\ResolveTenant::class,
            'resolve.barberia' => \App\Http\Middleware\ResolveBarberiaToken::class,
        ]);

        // Render (y la mayoría de plataformas cloud) terminan el HTTPS en su
        // proxy y reenvían el tráfico en plano al contenedor — sin esto,
        // Laravel genera URLs de assets/redirects en http:// aunque el
        // visitante esté en https://. La IP del proxy no es fija/conocida,
        // así que se confía en cualquier origen (estándar en este tipo de
        // entornos, ya que el propio Render es el único que puede llegar
        // al contenedor).
        $middleware->trustProxies(at: '*');

        // Con 3 guards independientes (client/empleado/admin), el redirect
        // por defecto a la ruta "login" (que no existe) daba 500 en vez de
        // mandar al login correcto cuando la sesión expira.
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('*/staff/*')) {
                return route('empleado.login', ['barberiaToken' => $request->segment(1)]);
            }

            if (session('barberia_id') && session('sede_id')) {
                return route('tenant.landing', [
                    'barberiaToken' => TenantToken::barberia(session('barberia_id')),
                    'sedeToken' => TenantToken::sede(session('sede_id')),
                ]);
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
