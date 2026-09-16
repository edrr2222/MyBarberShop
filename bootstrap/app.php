<?php

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
        ]);

        // Con 3 guards independientes (client/empleado/admin), el redirect
        // por defecto a la ruta "login" (que no existe) daba 500 en vez de
        // mandar al login correcto cuando la sesión expira.
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('staff/*')) {
                return route('empleado.login');
            }

            if (session('barberia_slug') && session('sede_slug')) {
                return route('tenant.landing', [
                    'barberiaSlug' => session('barberia_slug'),
                    'sedeSlug' => session('sede_slug'),
                ]);
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
