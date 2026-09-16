<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\EmpleadoAuthController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\TenantController;
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
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');
});

// ---- App barbero ----
Route::prefix('staff')->group(function () {
    Route::get('/login', fn () => view('empleado.login'))->name('empleado.login');
    Route::post('/login', [EmpleadoAuthController::class, 'login']);

    Route::middleware('auth:empleado')->group(function () {
        Route::get('/scanner', fn () => view('empleado.scanner'))->name('empleado.scanner');
        Route::get('/perfil', fn () => view('empleado.perfil'))->name('empleado.perfil');
        Route::post('/logout', [EmpleadoAuthController::class, 'logout'])->name('empleado.logout');
    });
});

// ---- Panel admin ----
Route::prefix('admin')->group(function () {
    Route::get('/login', fn () => view('admin.login'))->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

// ---- API interna del flujo de sellos (consumida por Vue) ----
Route::prefix('api/loyalty')->group(function () {
    Route::middleware('auth:client')->get('/qr-token', [QrController::class, 'generarToken']);
    Route::middleware('auth:empleado')->post('/escanear', [QrController::class, 'escanear']);
    Route::middleware('auth:empleado')->post('/redimir', [QrController::class, 'redimir']);
});
