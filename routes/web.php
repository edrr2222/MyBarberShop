<?php

use App\Http\Controllers\Admin\BarberiaRedSocialController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\EmpleadoRedSocialController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\EmpleadoAuthController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\TenantController;
use App\Models\Servicio;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ---- Entrada del cliente vía QR fijo de sede ----
Route::middleware('tenant')->prefix('b/{barberiaSlug}/{sedeSlug}')->group(function () {
    Route::get('/', [TenantController::class, 'show'])->name('tenant.landing');
    Route::post('/registro', [ClientAuthController::class, 'register'])->name('client.register');
    Route::post('/login', [ClientAuthController::class, 'login'])->name('client.login');
});

// ---- App cliente (autenticado con guard 'client') ----
Route::middleware('auth:client')->group(function () {
    Route::get('/mi-qr', fn () => view('client.qr'))->name('client.qr');
    Route::get('/servicios', function () {
        $client = auth('client')->user();
        $servicios = Servicio::where('barberia_id', $client->barberia_id)
            ->where('estado', true)
            ->orderBy('categoria')
            ->orderBy('orden')
            ->get()
            ->groupBy('categoria');

        return view('client.servicios', ['servicios' => $servicios, 'barberia' => $client->barberia]);
    })->name('client.servicios');
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');
});

// ---- App barbero ----
Route::prefix('staff')->group(function () {
    Route::get('/login', fn () => view('empleado.login'))->name('empleado.login');
    Route::post('/login', [EmpleadoAuthController::class, 'login']);

    Route::middleware('auth:empleado')->group(function () {
        Route::get('/scanner', function (\Illuminate\Http\Request $request) {
            $empleado = auth('empleado')->user();
            $servicios = Servicio::where('barberia_id', $empleado->sede->barberia_id)
                ->where('estado', true)
                ->orderBy('orden')
                ->get();
            $servicioId = $request->query('servicio_id');

            return view('empleado.scanner', ['servicios' => $servicios, 'servicioId' => $servicioId, 'barberia' => $empleado->sede->barberia]);
        })->name('empleado.scanner');
        Route::get('/perfil', function () {
            $empleado = auth('empleado')->user();
            $empleado->load('redesSociales');

            return view('empleado.perfil', ['empleado' => $empleado, 'barberia' => $empleado->sede->barberia]);
        })->name('empleado.perfil');
        Route::post('/logout', [EmpleadoAuthController::class, 'logout'])->name('empleado.logout');
    });
});

// ---- Panel admin ----
Route::prefix('admin')->group(function () {
    Route::get('/login', fn () => view('admin.login'))->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard', ['admin' => auth('admin')->user()]))->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/marca', [MarcaController::class, 'edit'])->name('marca.edit');
        Route::put('/marca', [MarcaController::class, 'update'])->name('marca.update');
        Route::post('/marca/redes-sociales', [BarberiaRedSocialController::class, 'store'])->name('marca.redes-sociales.store');
        Route::delete('/marca/redes-sociales/{redSocial}', [BarberiaRedSocialController::class, 'destroy'])->name('marca.redes-sociales.destroy');

        Route::get('/sedes', [SedeController::class, 'index'])->name('sedes.index');
        Route::get('/sedes/crear', [SedeController::class, 'create'])->name('sedes.create');
        Route::post('/sedes', [SedeController::class, 'store'])->name('sedes.store');
        Route::get('/sedes/{sede}/editar', [SedeController::class, 'edit'])->name('sedes.edit');
        Route::put('/sedes/{sede}', [SedeController::class, 'update'])->name('sedes.update');
        Route::post('/sedes/{sede}/toggle', [SedeController::class, 'toggle'])->name('sedes.toggle');

        Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
        Route::get('/empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create');
        Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
        Route::get('/empleados/{empleado}/editar', [EmpleadoController::class, 'edit'])->name('empleados.edit');
        Route::put('/empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
        Route::post('/empleados/{empleado}/toggle', [EmpleadoController::class, 'toggle'])->name('empleados.toggle');
        Route::post('/empleados/{empleado}/redes-sociales', [EmpleadoRedSocialController::class, 'store'])->name('empleados.redes-sociales.store');
        Route::delete('/empleados/{empleado}/redes-sociales/{redSocial}', [EmpleadoRedSocialController::class, 'destroy'])->name('empleados.redes-sociales.destroy');

        Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
        Route::get('/servicios/crear', [ServicioController::class, 'create'])->name('servicios.create');
        Route::post('/servicios', [ServicioController::class, 'store'])->name('servicios.store');
        Route::get('/servicios/{servicio}/editar', [ServicioController::class, 'edit'])->name('servicios.edit');
        Route::put('/servicios/{servicio}', [ServicioController::class, 'update'])->name('servicios.update');
        Route::post('/servicios/{servicio}/toggle', [ServicioController::class, 'toggle'])->name('servicios.toggle');

        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::post('/clientes/{client}/toggle', [ClienteController::class, 'toggle'])->name('clientes.toggle');
    });
});

// ---- API interna del flujo de sellos (consumida por Vue) ----
Route::prefix('api/loyalty')->group(function () {
    Route::middleware('auth:client')->get('/qr-token', [QrController::class, 'generarToken']);
    Route::middleware('auth:empleado')->post('/escanear', [QrController::class, 'escanear']);
    Route::middleware('auth:empleado')->post('/redimir', [QrController::class, 'redimir']);
});
