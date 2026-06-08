<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\VisitEvent;
use App\Models\Community\PrivateUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitEventController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitEvent::with('privateUnit')->latest();

        if ($request->user()->getCurrentPropertyId()) {
            $query->deUnidad($request->user()->getCurrentPropertyId());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $events = $query->paginate(15)
            ->through(fn($event) => $event->resumenDashboard());

        return Inertia::render('AccessControl/VisitEvents/Index', [
            'events' => $events,
            'filters' => $request->only('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/VisitEvents/Create', [
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'description'     => 'nullable|string|max:500',
            'date_time_start' => 'required|date',
            'date_time_end'   => 'required|date|after:date_time_start',
            'guest_amount'    => 'required|integer|min:1',
            'max_qr_uses'     => 'nullable|integer|min:1',
            'private_unit_id' => 'required|exists:private_units,id',
        ]);

        VisitEvent::crearConQR($validated);

        return redirect()->route('visit-events.index')
            ->with('success', 'Evento creado exitosamente.');
    }

    public function show(VisitEvent $visitEvent)
    {
        $visitEvent->load(['privateUnit', 'visits', 'accessLogs']);

        return Inertia::render('AccessControl/VisitEvents/Show', [
            'event' => $visitEvent->resumenDashboard() + [
                'description'  => $visitEvent->description,
                'qr_code'      => $visitEvent->qr_code,
                'inicio'       => $visitEvent->date_time_start?->format('d/m/Y H:i'),
                'fin'          => $visitEvent->date_time_end?->format('d/m/Y H:i'),
                'maxQrUses'    => $visitEvent->max_qr_uses,
                'unidad'       => $visitEvent->privateUnit?->lot_number,
            ],
        ]);
    }

    public function edit(VisitEvent $visitEvent)
    {
        return Inertia::render('AccessControl/VisitEvents/Edit', [
            'event' => $visitEvent->only([
                'id', 'name', 'description', 'date_time_start',
                'date_time_end', 'guest_amount', 'max_qr_uses',
                'status', 'private_unit_id',
            ]),
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function update(Request $request, VisitEvent $visitEvent)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'description'     => 'nullable|string|max:500',
            'date_time_start' => 'required|date',
            'date_time_end'   => 'required|date|after:date_time_start',
            'guest_amount'    => 'required|integer|min:1',
            'max_qr_uses'     => 'nullable|integer|min:1',
            'status'          => 'required|in:Activo,Inactivo,Cancelado',
            'private_unit_id' => 'required|exists:private_units,id',
        ]);

        $visitEvent->update($validated);

        return redirect()->route('visit-events.index')
            ->with('success', 'Evento actualizado.');
    }

    public function destroy(VisitEvent $visitEvent)
    {
        $visitEvent->cancelar();

        return redirect()->route('visit-events.index')
            ->with('success', 'Evento cancelado.');
    }
}
