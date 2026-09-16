<?php
/**
 * Este archivo NO se usa directamente: es un snippet para copiar
 * dentro de tu config/auth.php existente (arrays 'guards' y 'providers').
 *
 * Recuerda crear los modelos correspondientes:
 *   App\Models\Client   -> tabla tenant.client
 *   App\Models\Empleado -> tabla barberia.empleado
 *   App\Models\Admin    -> tabla tenant.admin
 * cada uno implementando Illuminate\Contracts\Auth\Authenticatable
 * (o extendiendo Illuminate\Foundation\Auth\User).
 */

return [
    'defaults' => [
        'guard' => 'client',
        'passwords' => 'clients',
    ],

    'guards' => [
        'client' => [
            'driver' => 'session',
            'provider' => 'clients',
        ],
        'empleado' => [
            'driver' => 'session',
            'provider' => 'empleados',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'clients' => [
            'driver' => 'eloquent',
            'model' => App\Models\Client::class,
        ],
        'empleados' => [
            'driver' => 'eloquent',
            'model' => App\Models\Empleado::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],
];
