<?php

namespace App\Http\Controllers;

use App\Models\BankReconciliation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $currentSubdivisionId = session('current_subdivision_id');
        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }

        // Filtramos conciliaciones cuyos pagos pertenezcan a conceptos del fraccionamiento
        $reconciliations = BankReconciliation::query()
            ->with('payment.billingConcept')
            ->whereHas('payment.billingConcept', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            })
            ->latest()
            ->paginate(15)
            ->through(fn ($rec) => [
                'id' => $rec->id,
                'bank_reference' => $rec->bank_reference,
                'amount' => (float) $rec->amount,
                'transaction_date' => $rec->transaction_date->format('d/m/Y'),
                'status' => $rec->status,
                'error_message' => $rec->error_message,
                'matched_payment_folio' => $rec->payment ? $rec->payment->transaction_folio : null,
            ]);

        return Inertia::render('BankReconciliation/Index', [
            'reconciliations' => $reconciliations,
        ]);
    }
}