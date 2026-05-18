<?php

namespace App\Http\Controllers\Finances;

use App\Models\Finances\BankReconciliation;
use App\Models\Finances\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        // NOTA: Para un sistema multi-coto real, tu tabla 'bank_reconciliations' 
        // debería tener un 'subdivision_id' o 'bank_account_id' vinculado al coto,
        // de lo contrario, los movimientos "Pendientes" (sin pago) se mezclarán entre cotos.
        // Asumiremos que estás filtrando por coto si tienes la columna.
        
        $currentSubdivisionId = session('current_subdivision_id') ?? DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');

        // Consulta base (idealmente filtrada por subdivision_id de la cuenta bancaria)
        $query = BankReconciliation::with('payment.billingConcept')->latest();

        // Calcular KPIs rápidos
        $kpis = [
            'total_reconciled' => (clone $query)->whereIn('status', ['Conciliado', 'Manual'])->sum('amount'),
            'total_pending' => (clone $query)->where('status', 'Pendiente')->sum('amount'),
            'count_errors' => (clone $query)->where('status', 'Error')->count(),
        ];

        $reconciliations = $query->paginate(15)
            ->through(fn ($rec) => [
                'id' => $rec->id,
                'bank_reference' => $rec->bank_reference,
                'amount' => (float) $rec->amount,
                'transaction_date' => $rec->transaction_date->format('d/m/Y'),
                'status' => $rec->status,
                'error_message' => $rec->error_message,
                'matched_payment_folio' => $rec->payment ? $rec->payment->transaction_folio : null,
                'payment_id' => $rec->payment_id,
            ]);

        // Traemos pagos recientes "Pendientes" o "No conciliados" del coto para el cruce manual
        $unreconciledPayments = Payment::whereHas('billingConcept', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            })
            // ->where('is_reconciled', false) // Si tienes esta bandera en tu modelo Payment
            ->latest()
            ->take(50)
            ->get(['id', 'transaction_folio', 'amount', 'payment_date']);

        return Inertia::render('Finances/BankReconciliation/Index', [
            'reconciliations' => $reconciliations,
            'kpis' => $kpis,
            'unreconciledPayments' => $unreconciledPayments,
        ]);
    }

    /**
     * Procesa la conciliación manual desde la vista.
     */
    public function update(Request $request, BankReconciliation $bankReconciliation)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        // Vinculamos el pago y cambiamos el estatus
        $bankReconciliation->update([
            'payment_id' => $request->payment_id,
            'status' => 'Manual', // O 'Conciliado', según tu regla de negocio
            'error_message' => null,
        ]);

        // Opcional: Actualizar el estatus del Pago a "Conciliado"
        // $bankReconciliation->payment()->update(['status' => 'Conciliado']);

        return redirect()->back()->with('success', 'Movimiento conciliado correctamente.');
    }
}