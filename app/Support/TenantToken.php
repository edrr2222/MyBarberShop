<?php

namespace App\Support;

use Hashids\Hashids;

/**
 * Convierte los IDs de barbería/sede en tokens cortos y no adivinables para
 * las URLs públicas (cliente y staff), en vez de exponer el id numérico
 * secuencial o el nombre real del negocio. Reversible solo con APP_KEY: sin
 * el secreto de la app, no se puede decodificar ni enumerar barberías
 * probando ids consecutivos.
 *
 * No se guarda en base de datos — se recalcula al vuelo a partir del id,
 * así que no hace falta migración ni columna nueva.
 */
class TenantToken
{
    public static function barberia(int $id): string
    {
        return static::hashidsFor('barberia')->encode($id);
    }

    public static function decodeBarberia(?string $token): ?int
    {
        return static::decode('barberia', $token);
    }

    public static function sede(int $id): string
    {
        return static::hashidsFor('sede')->encode($id);
    }

    public static function decodeSede(?string $token): ?int
    {
        return static::decode('sede', $token);
    }

    private static function decode(string $context, ?string $token): ?int
    {
        if (! $token) {
            return null;
        }

        $decoded = static::hashidsFor($context)->decode($token);

        return $decoded[0] ?? null;
    }

    private static function hashidsFor(string $context): Hashids
    {
        // Salt distinto por contexto para que el mismo id numérico (ej. 1)
        // no produzca el mismo token para una barbería que para una sede.
        // Se hashea (en vez de solo concatenar) porque Hashids no distingue
        // bien salts largos que comparten un prefijo común como APP_KEY —
        // con solo concatenar, "barberia" y "sede" daban el mismo código.
        return new Hashids(hash('sha256', $context.'|tenant-token|'.config('app.key')), 8);
    }
}
