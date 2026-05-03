<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentStatusController extends Controller
{
    /**
     * Calcula el estatus de morosidad de la unidad seleccionada actualmente.
     * Retorna: 'current', 'late' o 'defaulter'.
     */
    public function show(Request $request)
    {
        // 1. Obtener la unidad seleccionada usando el método del modelo User
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        // Si es administrador (string 'admin_X') o no hay propiedad, retornamos estatus neutral
        if (!$currentPropertyId || is_string($currentPropertyId)) {
            return response()->json(['status' => 'current']);
        }

        // 2. Consulta Eficiente: Contar cuotas expiradas y no pagadas
        // Usamos Query Builder (DB::table) para evitar hidratar modelos pesados
        $expiredCount = DB::table('generated_fees')
            ->where('private_unit_id', $currentPropertyId)
            ->whereDate('expiration_date', '<', now()) // Ya venció
            ->whereNotIn('status', ['Pagado', 'Cancelado']) // No está pagada ni cancelada
            ->count();

        // 3. Determinar estatus según regla de negocio
        // < 2 vencidos (0) -> Al corriente (Current)
        // 1 o 2 vencidos -> Retrasado (Late)
        // >= 3 vencidos -> Moroso (Defaulter)
        
        $status = 'current';

        if ($expiredCount >= 3) {
            $status = 'defaulter';
        } elseif ($expiredCount > 0) {
            $status = 'late';
        }

        return response()->json([
            'status' => $status,
            'expired_count' => $expiredCount
        ]);
    }
}