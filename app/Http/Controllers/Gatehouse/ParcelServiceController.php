<?php

namespace App\Http\Controllers\Gatehouse;

use App\Models\Gatehouse\ParcelService;
use App\Models\Community\PrivateUnit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParcelServiceController extends Controller
{
    /**
     * Muestra el catálogo de paquetes.
     */
    public function index(Request $request)
    {
        $query = ParcelService::query();

        // Validamos si el usuario tiene el método y una propiedad asignada (Residentes)
        // Si no lo tiene (es Guardia o Admin), verá todos los paquetes.
        if (method_exists($request->user(), 'getCurrentPropertyId') && $request->user()->getCurrentPropertyId()) {
            $query->where('private_unit_id', $request->user()->getCurrentPropertyId());
        }

        $parcels = $query->latest()
            ->paginate(15)
            ->through(function ($parcel) {
                return [
                    'id' => $parcel->id,
                    'name' => $parcel->name,
                    'courier' => $parcel->name ?? 'Desconocido', // Mapeado para que empate con la vista Vue
                    'tracking_number' => $parcel->tracking_number ?? 'S/N',
                    'status' => $parcel->status ?? 'En Caseta',
                    'received_at' => $parcel->created_at ? $parcel->created_at->format('d/m/Y H:i') : 'N/A',
                ];
            });

        return Inertia::render('Gatehouse/ParcelServices/Index', [
            'parcels' => $parcels
        ]);
    }

    /**
     * Muestra el formulario para registrar un nuevo paquete.
     */
    public function create()
    {
        // Obtenemos todas las unidades. 
        // Usamos get() directamente para traer todas las columnas y evitar el error si tu base de datos no tiene una columna llamada 'name'.
        $privateUnits = PrivateUnit::orderBy('id')->get();

        return Inertia::render('Gatehouse/ParcelServices/Create', [
            'privateUnits' => $privateUnits
        ]);
    }

    /**
     * Guarda el nuevo paquete en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'private_unit_id' => 'required|exists:private_units,id',
            'status' => 'required|string'
        ]);

        // Autocompletamos quién recibió el paquete y cuándo
        $validated['user_id'] = $request->user()->id;
        $validated['receipt_date'] = now();

        ParcelService::create($validated);

        return redirect()->route('parcel-services.index')
                         ->with('success', 'Paquete registrado exitosamente en caseta.');
    }

    /**
     * Muestra el detalle del paquete (El "Pase / Ticket").
     */
    public function show($id)
    {
        // Cargamos la casa destino y quién lo recibió para mostrarlos en la vista
        $parcel = ParcelService::with(['privateUnit', 'receivedBy'])->findOrFail($id);

        return Inertia::render('Gatehouse/ParcelServices/Show', [
            'parcel' => $parcel
        ]);
    }

    /**
     * Muestra el formulario para editar un paquete existente.
     */
    public function edit($id)
    {
        $parcel = ParcelService::findOrFail($id);
        
        // Traemos todas las columnas para evitar el error de 'name'
        $privateUnits = PrivateUnit::orderBy('id')->get();

        return Inertia::render('Gatehouse/ParcelServices/Edit', [
            'parcel' => $parcel,
            'privateUnits' => $privateUnits
        ]);
    }

    /**
     * Actualiza los datos o el estatus del paquete en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $parcel = ParcelService::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'private_unit_id' => 'sometimes|required|exists:private_units,id',
            'status' => 'sometimes|required|string'
        ]);

        // Si el estatus cambia a "Entregado" y antes no lo estaba, registramos la fecha exacta de entrega
        if (isset($validated['status']) && $validated['status'] === 'Entregado' && $parcel->status !== 'Entregado') {
            $validated['delivery_date'] = now();
        }

        $parcel->update($validated);

        // Retornamos a la página anterior (útil si se marcó como entregado desde la vista Show)
        return back()->with('success', 'Paquete actualizado exitosamente.');
    }

    /**
     * Elimina el paquete del registro.
     */
    public function destroy($id)
    {
        $parcel = ParcelService::findOrFail($id);
        $parcel->delete();

        return redirect()->route('parcel-services.index')
                         ->with('success', 'El registro del paquete ha sido eliminado.');
    }
}