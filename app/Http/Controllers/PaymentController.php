<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener Fraccionamiento Actual (Lógica Admin)
        $currentSubdivisionId = session('current_subdivision_id');
        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado.');
        }

        $search = $request->input('search');

        // 2. Consulta filtrada por el concepto que pertenece al fraccionamiento
        $query = Payment::query()
            ->with(['resident', 'billingConcept'])
            ->whereHas('billingConcept', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            });

        // 3. Buscador
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_folio', 'like', "%{$search}%")
                  ->orWhereHas('resident', function ($qRes) use ($search) {
                      $qRes->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(function ($payment) {
                return [
                    'id' => $payment->id,
                    'folio' => $payment->transaction_folio,
                    'amount' => (float) $payment->amount,
                    'payment_date' => $payment->payment_date->format('d/m/Y H:i'),
                    'method' => $payment->payment_method,
                    'concept' => $payment->billingConcept->name ?? 'N/A',
                    'resident_name' => $payment->resident->full_name ?? 'Anónimo',
                ];
            });

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        // Aquí iría la lógica para registrar un pago manual si es necesario
    }
}