<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Carga los stored procedures (funciones plpgsql) usados por el
 * flujo de fidelidad: generar token QR, escanear, redimir.
 * El SQL fuente vive en database/procedures/loyalty_procedures.sql
 * para que sea fácil de editar sin tocar la migración.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS pgcrypto'); // requerido por gen_random_uuid()

        $sql = file_get_contents(database_path('procedures/loyalty_procedures.sql'));
        DB::unprepared($sql);
    }

    public function down(): void
    {
        DB::unprepared('
            DROP FUNCTION IF EXISTS loyalty.fn_generar_token(BIGINT, BIGINT, INT);
            DROP FUNCTION IF EXISTS loyalty.fn_escanear(UUID, BIGINT, BIGINT, BIGINT);
            DROP FUNCTION IF EXISTS loyalty.fn_redimir(BIGINT);
        ');
    }
};
