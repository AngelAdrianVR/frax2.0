<?php

namespace App\Http\Controllers\Finances;

use App\Models\Finances\Payment;
use App\Http\Controllers\Controller;
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
            ->with(['user', 'billingConcept']) // Cambiado de resident a user
            ->whereHas('billingConcept', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            });

        // 3. Buscador
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_folio', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qUser) use ($search) { // Cambiado de resident a user
                      $qUser->where('name', 'like', "%{$search}%"); // Cambiado de full_name a name
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
                    // Mantenemos la llave resident_name para la vista, pero llamamos a user->name
                    'resident_name' => $payment->user->name ?? 'Anónimo', 
                ];
            });

        return Inertia::render('Finances/Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        // Aquí iría la lógica para registrar un pago manual si es necesario
    }
}