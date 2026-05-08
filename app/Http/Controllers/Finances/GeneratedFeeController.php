<?php

namespace App\Http\Controllers\Finances;

use App\Models\Finances\GeneratedFee;
use App\Models\Community\PrivateUnit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GeneratedFeeController extends Controller
{
    /**
     * Muestra las cuotas (Fees). 
     * Si es residente, muestra las suyas. Si es admin, muestra todas las del fraccionamiento.
     */
    public function index(Request $request)
    {
        // 1. Obtener el contexto actual (Multi-tenancy)
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        if (!$currentPropertyId) {
            return redirect()->route('dashboard')->with('error', 'Debes seleccionar un contexto o propiedad.');
        }

        $query = GeneratedFee::query()->with(['billingConcept:id,name', 'privateUnit:id,name,subdivision_id']);
        $statsQuery = GeneratedFee::query();

        // 2. Lógica de visibilidad (Admin vs Residente)
        if (is_string($currentPropertyId) && str_starts_with($currentPropertyId, 'admin_')) {
            // Es administrador: filtramos por las cuotas de este fraccionamiento
            $subdivisionId = (int) str_replace('admin_', '', $currentPropertyId);
            
            $query->whereHas('privateUnit', function ($q) use ($subdivisionId) {
                $q->where('subdivision_id', $subdivisionId);
            });
            $statsQuery->whereHas('privateUnit', function ($q) use ($subdivisionId) {
                $q->where('subdivision_id', $subdivisionId);
            });
        } else {
            // Es residente: filtramos solo por su propiedad
            $query->where('private_unit_id', $currentPropertyId);
            $statsQuery->where('private_unit_id', $currentPropertyId);
        }

        // 3. Consulta de Cuotas Paginadas
        $fees = $query->orderBy('expiration_date', 'desc')
            ->paginate(10)
            ->through(function ($fee) {
                return [
                    'id' => $fee->id,
                    'unit_name' => $fee->privateUnit->name ?? 'Propiedad', // Para que el admin identifique la casa
                    'concept_name' => $fee->billingConcept->name ?? 'Concepto General',
                    'payment_reference' => $fee->payment_reference,
                    'total_amount' => (float) $fee->total_amount,
                    'amount_paid' => (float) $fee->amount_paid,
                    'balance' => (float) ($fee->total_amount - $fee->amount_paid),
                    'status' => $fee->status, // Pendiente, Parcial, Pagado, Atrasada
                    'expiration_date' => $fee->expiration_date->format('Y-m-d'),
                    'period' => $fee->start_period->translatedFormat('F Y'),
                    'is_overdue' => $fee->expiration_date < now() && $fee->status !== 'Pagado',
                ];
            });

        // 4. Calcular Totales para las Tarjetas (KPIs)
        $stats = $statsQuery->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
            ->selectRaw('
                SUM(total_amount - amount_paid) as total_debt,
                COUNT(*) as pending_count,
                MIN(expiration_date) as next_due_date
            ')
            ->first();

        return Inertia::render('Finances/GeneratedFees/Index', [
            'fees' => $fees,
            'stats' => [
                'total_debt' => (float) ($stats->total_debt ?? 0),
                'pending_count' => (int) ($stats->pending_count ?? 0),
                'next_due_date' => $stats->next_due_date ? \Carbon\Carbon::parse($stats->next_due_date)->translatedFormat('d M Y') : 'Al día',
            ],
        ]);
    }

    /**
     * Muestra la pantalla de Checkout/Pago para una cuota específica.
     */
    public function pay(Request $request, GeneratedFee $fee)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();
        
        $fee->load(['billingConcept:id,name', 'privateUnit:id,name,subdivision_id']);

        // Validar permisos según el contexto
        if (is_string($currentPropertyId) && str_starts_with($currentPropertyId, 'admin_')) {
            $subdivisionId = (int) str_replace('admin_', '', $currentPropertyId);
            if ($fee->privateUnit->subdivision_id !== $subdivisionId) {
                abort(403, 'No tienes permiso para ver esta cuota.');
            }
        } else {
            if ($fee->private_unit_id !== $currentPropertyId) {
                abort(403, 'No tienes permiso para pagar esta cuota.');
            }
        }

        // Si ya está pagada, regresarlo
        if ($fee->status === 'Pagado') {
            return redirect()->route('fees.index')->with('info', 'Esta cuota ya ha sido pagada en su totalidad.');
        }

        return Inertia::render('Finances/GeneratedFees/Pay', [
            'fee' => [
                'id' => $fee->id,
                'unit_name' => $fee->privateUnit->name ?? 'Propiedad',
                'concept_name' => $fee->billingConcept->name ?? 'Concepto General',
                'payment_reference' => $fee->payment_reference,
                'total_amount' => (float) $fee->total_amount,
                'amount_paid' => (float) $fee->amount_paid,
                'balance' => (float) ($fee->total_amount - $fee->amount_paid),
                'expiration_date' => $fee->expiration_date->format('Y-m-d'),
                'period' => $fee->start_period->translatedFormat('F Y'),
                'is_overdue' => $fee->expiration_date < now() && $fee->status !== 'Pagado',
            ]
        ]);
    }

    /**
     * Procesa la subida del comprobante y registra el pago en estado "Por Validar".
     */
    public function processPayment(Request $request, GeneratedFee $fee)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($fee->total_amount - $fee->amount_paid),
            'reference' => 'nullable|string|max:255',
            'receipt_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        // 1. Guardar el archivo en storage/app/public/receipts
        $path = $request->file('receipt_file')->store('receipts', 'public');

        // 2. Aquí crearíamos el registro en la tabla de Payments (Pagos)
        /*
        \App\Models\Finances\Payment::create([
            'generated_fee_id' => $fee->id,
            'user_id' => $request->user()->id,
            'amount' => $validated['amount'],
            'reference' => $validated['reference'],
            'receipt_path' => $path,
            'status' => 'En Revisión' // El Admin debe aprobarlo para que se reste del saldo
        ]);
        */
        
        return redirect()->route('fees.index')->with('success', '¡Comprobante enviado! El administrador lo validará en breve.');
    }

    // =========================================================================
    // MÉTODOS DE ADMINISTRADOR (Controlador Ligero)
    // =========================================================================

    public function storeManual(Request $request, PrivateUnit $privateUnit)
    {
        $validated = $request->validate([
            'concept' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        PrivateUnit::createManualCharge($privateUnit, $validated);

        return redirect()->back()->with('success', 'Cargo o multa generado correctamente.');
    }

    public function addBalance(Request $request, PrivateUnit $privateUnit)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'required|string|max:255',
        ]);

        $privateUnit->addCreditBalance($validated['amount'], $validated['reference']);

        return redirect()->back()->with('success', 'Saldo a favor registrado exitosamente.');
    }
}