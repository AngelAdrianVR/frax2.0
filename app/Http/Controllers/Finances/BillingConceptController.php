<?php

namespace App\Http\Controllers\Finances;

use App\Models\Finances\BillingConcept;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class BillingConceptController extends Controller
{
    public function index(Request $request)
    {
        $currentSubdivisionId = session('current_subdivision_id');
        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }

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

    // Aquí agregarías métodos store, update, destroy para crear nuevos conceptos
}