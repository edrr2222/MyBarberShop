<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Núcleo del sistema de fidelidad: generación de QR dinámico (cliente)
 * y escaneo/aplicación de sello (barbero). Toda la lógica de negocio
 * vive en los stored procedures (database/procedures/loyalty_procedures.sql);
 * este controller solo valida sesión/permisos y traduce a JSON.
 */
class QrController extends Controller
{
    /**
     * GET /api/loyalty/qr-token
     * Llamado por la app del cliente (polling) para mostrar/refrescar su QR.
     */
    public function generarToken(Request $request)
    {
        $client = auth('client')->user();

        if (! $client) {
            return response()->json(['ok' => false, 'mensaje' => 'No autenticado'], 401);
        }

        $segundos = LoyaltyConfig::where('barberia_id', $client->barberia_id)
            ->value('qr_token_segundos') ?? 60;

        $row = DB::selectOne(
            'select * from loyalty.fn_generar_token(?, ?, ?)',
            [$client->id, $client->barberia_id, $segundos]
        );

        return response()->json([
            'ok' => true,
            'token' => $row->token,
            'expires_at' => $row->expires_at,
        ]);
    }

    /**
     * POST /api/loyalty/escanear
     * Llamado por la app del barbero al leer el QR del cliente con la cámara.
     * body: { token, servicio_id? }
     */
    public function escanear(Request $request)
    {
        $empleado = auth('empleado')->user();

        if (! $empleado) {
            return response()->json(['ok' => false, 'mensaje' => 'No autenticado'], 401);
        }

        $request->validate([
            'token' => 'required|uuid',
            'servicio_id' => 'nullable|integer',
        ]);

        $row = DB::selectOne(
            'select * from loyalty.fn_escanear(?, ?, ?, ?)',
            [$request->token, $empleado->id, $empleado->sede_id, $request->servicio_id]
        );

        return response()->json([
            'ok' => $row->ok,
            'mensaje' => $row->mensaje,
            'sellos_actuales' => $row->sellos_actuales,
            'sellos_requeridos' => $row->sellos_requeridos,
            'corte_gratis' => $row->corte_gratis,
            'card_id' => $row->card_id,
        ]);
    }

    /**
     * POST /api/loyalty/redimir
     * Llamado por el barbero cuando efectivamente aplica el corte gratis.
     * body: { card_id }
     */
    public function redimir(Request $request)
    {
        $empleado = auth('empleado')->user();

        if (! $empleado) {
            return response()->json(['ok' => false, 'mensaje' => 'No autenticado'], 401);
        }

        $request->validate(['card_id' => 'required|integer']);

        $row = DB::selectOne('select * from loyalty.fn_redimir(?)', [$request->card_id]);

        return response()->json([
            'ok' => $row->ok,
            'mensaje' => $row->mensaje,
            'nueva_card_id' => $row->nueva_card_id,
        ]);
    }
}
