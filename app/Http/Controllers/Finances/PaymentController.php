<?php

namespace App\Http\Controllers\Finances;

use App\Models\Finances\Payment;
use App\Models\Finances\BillingConcept;
use App\Models\Community\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Obtiene el ID del fraccionamiento activo de forma centralizada.
     */
    private function getCurrentSubdivisionId(Request $request)
    {
        $id = session('current_subdivision_id');
        if (!$id) {
            $id = DB::table('subdivision_user')->where('user_id', $request->user()->id)->value('subdivision_id');
        }
        return $id;
    }

    public function index(Request $request)
    {
        $subdivisionId = $this->getCurrentSubdivisionId($request);
        if (!$subdivisionId) return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado.');

        $payments = Payment::query()
            ->with(['user', 'billingConcept'])
            ->forSubdivision($subdivisionId)
            ->search($request->input('search'))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn($payment) => [
                'id' => $payment->id,
                'folio' => $payment->transaction_folio,
                'amount' => (float) $payment->amount,
                'payment_date' => $payment->payment_date->format('d/m/Y H:i'),
                'method' => $payment->payment_method,
                'concept' => $payment->billingConcept->name ?? 'N/A',
                'resident_name' => $payment->user->name ?? 'Anónimo',
            ]);

        return Inertia::render('Finances/Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request)
    {
        $subdivisionId = $this->getCurrentSubdivisionId($request);
        
        // Cargar usuarios y conceptos del coto para los selects del formulario
        $users = User::whereHas('subdivisions', fn($q) => $q->where('subdivision_id', $subdivisionId))
            ->select('id', 'name')->orderBy('name')->get();
            
        $concepts = BillingConcept::where('subdivision_id', $subdivisionId)
            ->select('id', 'name', 'base_amount')->orderBy('name')->get();

        return Inertia::render('Finances/Payments/Create', [
            'users' => $users,
            'concepts' => $concepts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Transferencia,Efectivo,Tarjeta,Cheque',
            'payment_date' => 'required|date',
            'transaction_folio' => 'nullable|string|max:100',
            'user_id' => 'required|exists:users,id',
            'billing_concept_id' => 'nullable|exists:billing_concepts,id'
        ]);

        $subdivisionId = $this->getCurrentSubdivisionId($request);
        
        // Pasamos la orden al Modelo
        Payment::registerPayment($validated, $subdivisionId);

        return redirect()->route('payments.index')->with('success', 'Pago registrado exitosamente.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'billingConcept']);
        
        return Inertia::render('Finances/Payments/Show', [
            'payment' => [
                'id' => $payment->id,
                'transaction_folio' => $payment->transaction_folio,
                'amount' => (float) $payment->amount,
                'payment_date' => $payment->payment_date->format('d \d\e M Y, h:i A'),
                'payment_method' => $payment->payment_method,
                'resident' => $payment->user ? ['id' => $payment->user->id, 'name' => $payment->user->name, 'email' => $payment->user->email] : null,
                'concept' => $payment->billingConcept ? ['id' => $payment->billingConcept->id, 'name' => $payment->billingConcept->name] : null,
                'created_at' => $payment->created_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    public function edit(Request $request, Payment $payment)
    {
        $subdivisionId = $this->getCurrentSubdivisionId($request);
        
        $users = User::whereHas('subdivisions', fn($q) => $q->where('subdivision_id', $subdivisionId))
            ->select('id', 'name')->orderBy('name')->get();
            
        $concepts = BillingConcept::where('subdivision_id', $subdivisionId)
            ->select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Finances/Payments/Edit', [
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_date' => $payment->payment_date->format('Y-m-d\TH:i'), // Formato datetime-local
                'transaction_folio' => $payment->transaction_folio,
                'user_id' => $payment->user_id,
                'billing_concept_id' => $payment->billing_concept_id,
            ],
            'users' => $users,
            'concepts' => $concepts
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Transferencia,Efectivo,Tarjeta,Cheque',
            'payment_date' => 'required|date',
            'transaction_folio' => 'nullable|string|max:100',
            'user_id' => 'required|exists:users,id',
            'billing_concept_id' => 'nullable|exists:billing_concepts,id'
        ]);

        $payment->updatePayment($validated);

        return redirect()->route('payments.show', $payment->id)->with('success', 'Pago actualizado exitosamente.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Pago eliminado correctamente.');
    }
}