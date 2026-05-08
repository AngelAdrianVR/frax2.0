<?php

namespace App\Http\Controllers\Finances;

use App\Models\Finances\BillingConcept;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class BillingConceptController extends Controller
{
    /**
     * Helper para obtener el ID del fraccionamiento actual
     */
    private function getCurrentSubdivisionId(Request $request)
    {
        $currentSubdivisionId = session('current_subdivision_id');
        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }
        return $currentSubdivisionId;
    }

    public function index(Request $request)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);

        $concepts = BillingConcept::query()
            ->where('subdivision_id', $currentSubdivisionId)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'base_amount' => (float) $c->base_amount,
                'recurrence_type' => $c->recurrence_type,
                'slow_payers_apply' => $c->slow_payers_apply
            ]);

        return Inertia::render('Finances/BillingConcepts/Index', [
            'concepts' => $concepts
        ]);
    }

    public function create()
    {
        return Inertia::render('Finances/BillingConcepts/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'recurrence_type' => 'required|string|in:Semanal,Quincenal,Mensual,Bimestral,Anual,Pago unico',
            'slow_payers_apply' => 'boolean',
        ]);

        $validated['subdivision_id'] = $this->getCurrentSubdivisionId($request);
        $validated['slow_payers_apply'] = $request->input('slow_payers_apply', false);

        BillingConcept::create($validated);

        return redirect()->route('billing-concepts.index')->with('success', 'Concepto creado exitosamente.');
    }

    public function edit(BillingConcept $billingConcept)
    {
        return Inertia::render('Finances/BillingConcepts/Edit', [
            'concept' => [
                'id' => $billingConcept->id,
                'name' => $billingConcept->name,
                'base_amount' => (float) $billingConcept->base_amount,
                'recurrence_type' => $billingConcept->recurrence_type,
                'slow_payers_apply' => $billingConcept->slow_payers_apply,
            ]
        ]);
    }

    public function update(Request $request, BillingConcept $billingConcept)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'recurrence_type' => 'required|string|in:Semanal,Quincenal,Mensual,Bimestral,Anual,Pago unico',
            'slow_payers_apply' => 'boolean',
        ]);

        $validated['slow_payers_apply'] = $request->input('slow_payers_apply', false);

        $billingConcept->update($validated);

        return redirect()->route('billing-concepts.index')->with('success', 'Concepto actualizado correctamente.');
    }

    public function destroy(BillingConcept $billingConcept)
    {
        // Si ya hay cuotas generadas con este concepto, podrías querer restringir el borrado.
        // Por ahora lo eliminamos.
        $billingConcept->delete();

        return redirect()->route('billing-concepts.index')->with('success', 'Concepto eliminado correctamente.');
    }
}