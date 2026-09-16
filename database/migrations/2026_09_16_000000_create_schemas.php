<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Crea los schemas de Postgres que organizan las tablas del proyecto:
 * - tenant:   barbería, sedes, clientes y admins
 * - barberia: empleados, servicios, redes sociales
 * - loyalty:  tarjeta de fidelidad, sellos y tokens QR
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS tenant');
        DB::statement('CREATE SCHEMA IF NOT EXISTS barberia');
        DB::statement('CREATE SCHEMA IF NOT EXISTS loyalty');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS loyalty CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS barberia CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS tenant CASCADE');
    }
};
