<?php

namespace App\Http\Controllers;

use App\Models\GeneratedFee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class GeneratedFeeController extends Controller
{
    /**
     * Muestra las cuotas (Fees) del residente para la propiedad actual.
     */
    public function index(Request $request)
    {
        // 1. Obtener el contexto actual (Multi-tenancy)
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        if (!$currentPropertyId || is_string($currentPropertyId)) {
            // Si es admin global o no tiene propiedad seleccionada, no mostrar nada o redirigir
            return redirect()->route('dashboard')->with('error', 'Debes seleccionar una propiedad para ver tus cuotas.');
        }

        // 2. Consulta de Cuotas
        $fees = GeneratedFee::query()
            ->with(['billingConcept:id,name']) // Eager loading para el nombre del concepto
            ->where('private_unit_id', $currentPropertyId)
            ->orderBy('expiration_date', 'desc') // Las más recientes primero
            ->paginate(10)
            ->through(function ($fee) {
                return [
                    'id' => $fee->id,
                    'concept_name' => $fee->billingConcept->name ?? 'Concepto General',
                    'payment_reference' => $fee->payment_reference,
                    'total_amount' => (float) $fee->total_amount,
                    'amount_paid' => (float) $fee->amount_paid,
                    'balance' => (float) ($fee->total_amount - $fee->amount_paid),
                    'status' => $fee->status, // Pendiente, Parcial, Pagado, Atrasada
                    'expiration_date' => $fee->expiration_date->format('Y-m-d'),
                    'period' => $fee->start_period->translatedFormat('F Y'), // Ej: "Enero 2024"
                    'is_overdue' => $fee->expiration_date < now() && $fee->status !== 'Pagado',
                ];
            });

        // 3. Calcular Totales para las Tarjetas (KPIs)
        $stats = GeneratedFee::where('private_unit_id', $currentPropertyId)
            ->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
            ->selectRaw('
                SUM(total_amount - amount_paid) as total_debt,
                COUNT(*) as pending_count,
                MIN(expiration_date) as next_due_date
            ')
            ->first();

        return Inertia::render('GeneratedFeeds/Index', [
            'fees' => $fees,
            'stats' => [
                'total_debt' => (float) ($stats->total_debt ?? 0),
                'pending_count' => (int) ($stats->pending_count ?? 0),
                'next_due_date' => $stats->next_due_date ? \Carbon\Carbon::parse($stats->next_due_date)->translatedFormat('d M Y') : 'Al día',
            ],
        ]);
    }
}