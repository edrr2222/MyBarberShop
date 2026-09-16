<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Crea el schema de Postgres configurado en DB_SCHEMA (si no es "public")
 * ANTES de correr las migraciones. Es necesario porque `php artisan migrate`
 * crea su propia tabla de control "migrations" (usando el search_path
 * configurado) antes de leer ningún archivo de migración — si ese schema
 * todavía no existe, esa creación falla. Ver config/database.php.
 */
class PrepareDatabaseSchema extends Command
{
    protected $signature = 'db:prepare-schema';

    protected $description = 'Crea el schema de Postgres de la app (DB_SCHEMA) si no existe, antes de migrar';

    public function handle(): int
    {
        $schema = config('database.connections.pgsql.search_path');

        if (! $schema || $schema === 'public') {
            $this->info('DB_SCHEMA no configurado (o es "public"), nada que preparar.');

            return self::SUCCESS;
        }

        DB::statement('CREATE SCHEMA IF NOT EXISTS "'.$schema.'"');
        $this->info("Schema \"{$schema}\" listo.");

        return self::SUCCESS;
    }
}
