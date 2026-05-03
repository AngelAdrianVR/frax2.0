<?php

namespace App\Http\Controllers\Gatehouse;

use App\Models\Gatehouse\ParcelService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParcelServiceController extends Controller
{
    public function index(Request $request)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        $parcels = ParcelService::query()
            ->where('private_unit_id', $currentPropertyId)
            ->latest()
            ->paginate(15)
            ->through(function ($parcel) {
                return [
                    'id' => $parcel->id,
                    'courier' => $parcel->courier ?? 'Desconocido', // Ej: DHL, Amazon
                    'tracking_number' => $parcel->tracking_number ?? 'S/N',
                    'status' => $parcel->status ?? 'En Caseta', // Ej: En Caseta, Entregado
                    'received_at' => $parcel->created_at ? $parcel->created_at->format('d/m/Y H:i') : 'N/A',
                ];
            });

        return Inertia::render('Gatehouse/ParcelServices/Index', [
            'parcels' => $parcels
        ]);
    }
}