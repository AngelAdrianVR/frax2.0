<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\ParcelService;
use App\Models\Community\PrivateUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParcelServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ParcelService::with('privateUnit')->latest();

        // Residentes ven solo paquetes de su unidad
        if ($request->user()->getCurrentPropertyId()) {
            $query->deUnidad($request->user()->getCurrentPropertyId());
        }

        // Filtro por estatus
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $parcels = $query->paginate(15)
            ->through(fn($parcel) => [
                'id'              => $parcel->id,
                'name'            => $parcel->name,
                'tracking_number' => $parcel->tracking_number ?? 'S/N',
                'status'           => $parcel->status,
                'unidad'           => $parcel->privateUnit?->lot_number ?? 'N/A',
                'receipt_date'     => $parcel->receipt_date?->format('d/m/Y H:i'),
                'diasEnCaseta'     => $parcel->diasEnCaseta(),
            ]);

        return Inertia::render('AccessControl/ParcelServices/Index', [
            'parcels' => $parcels,
            'filters' => $request->only('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/ParcelServices/Create', [
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'tracking_number'  => 'nullable|string|max:255',
            'private_unit_id' => 'required|exists:private_units,id',
            'status'           => 'required|in:Recibido,Entregado,Regresado',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['receipt_date'] = now();

        ParcelService::create($validated);

        return redirect()->route('parcel-services.index')
            ->with('success', 'Paquete registrado exitosamente.');
    }

    public function show(ParcelService $parcelService)
    {
        $parcelService->load(['privateUnit', 'receivedBy']);

        return Inertia::render('AccessControl/ParcelServices/Show', [
            'parcel' => [
                'id'              => $parcelService->id,
                'name'            => $parcelService->name,
                'tracking_number'  => $parcelService->tracking_number,
                'status'           => $parcelService->status,
                'enCaseta'         => $parcelService->estaEnCaseta(),
                'diasEnCaseta'     => $parcelService->diasEnCaseta(),
                'receipt_date'     => $parcelService->receipt_date?->format('d/m/Y H:i'),
                'delivery_date'    => $parcelService->delivery_date?->format('d/m/Y H:i'),
                'unidad'           => $parcelService->privateUnit?->lot_number ?? 'N/A',
                'recibidoPor'      => $parcelService->receivedBy?->name ?? 'N/A',
            ],
        ]);
    }

    public function edit(ParcelService $parcelService)
    {
        return Inertia::render('AccessControl/ParcelServices/Edit', [
            'parcel' => $parcelService->only([
                'id', 'name', 'tracking_number', 'status',
                'private_unit_id',
            ]),
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function update(Request $request, ParcelService $parcelService)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'tracking_number'  => 'nullable|string|max:255',
            'private_unit_id' => 'required|exists:private_units,id',
            'status'           => 'required|in:Recibido,Entregado,Regresado',
        ]);

        // Si cambia a Entregado, registrar fecha
        if ($validated['status'] === ParcelService::STATUS_ENTREGADO
            && $parcelService->status !== ParcelService::STATUS_ENTREGADO) {
            $parcelService->marcarEntregado();
        }

        $parcelService->update($validated);

        return redirect()->route('parcel-services.index')
            ->with('success', 'Paquete actualizado correctamente.');
    }

    public function destroy(ParcelService $parcelService)
    {
        $parcelService->delete();

        return redirect()->route('parcel-services.index')
            ->with('success', 'Registro de paquetería eliminado.');
    }

    // ─── Acciones Rápidas ───────────────────────────────────────────

    /**
     * Marcar paquete como entregado (acción rápida desde el dashboard).
     */
    public function marcarEntregado(ParcelService $parcelService)
    {
        $parcelService->marcarEntregado();

        return back()->with('success', 'Paquete marcado como entregado.');
    }
}
