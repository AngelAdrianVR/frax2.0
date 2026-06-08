<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\AccessLog;
use App\Models\Community\PrivateUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccessLogController extends Controller
{
    /**
     * Bitácora universal de accesos (vista de guardia/admin).
     */
    public function index(Request $request)
    {
        $query = AccessLog::with(['privateUnit', 'user', 'visit'])
            ->latest('date_time');

        // Filtros
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->input('movement_type'));
        }
        if ($request->filled('verification_method')) {
            $query->where('verification_method', $request->input('verification_method'));
        }
        if ($request->filled('access_category')) {
            $query->where('access_category', $request->input('access_category'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('visitor_name', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%")
                  ->orWhere('vehicle_plate', 'like', "%{$search}%")
                  ->orWhere('visitor_identification', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('date_time', $request->input('date'));
        }
        // Filtro: solo los que siguen dentro
        if ($request->boolean('solo_dentro')) {
            $query->pendientesSalida();
        }

        $logs = $query->paginate(25)
            ->through(fn($log) => [
                'id'                      => $log->id,
                'identificador'           => $log->displayName(),
                'visitor_name'            => $log->visitor_name,
                'visitor_company'         => $log->visitor_company,
                'visitor_identification'  => $log->visitor_identification,
                'vehicle_plate'           => $log->vehicle_plate,
                'vehicle_brand'           => $log->vehicle_brand,
                'vehicle_color'           => $log->vehicle_color,
                'movimiento'              => $log->movement_type,
                'movementColor'           => $log->movementColor(),
                'metodo'                  => $log->verification_method,
                'access_category'         => $log->access_category,
                'categoryLabel'           => $log->categoryLabel(),
                'unidad'                  => $log->privateUnit?->lot_number ?? 'N/A',
                'private_unit_id'         => $log->private_unit_id,
                'fechaHora'               => $log->date_time?->format('d/m/Y H:i'),
                'exit_time'               => $log->exit_time?->format('d/m/Y H:i'),
                'sigueDentro'             => $log->sigueDentro(),
                'estancia'                => $log->estanciaHumana(),
                'notas'                   => $log->notes,
                'esVehicular'             => $log->esVehicular(),
            ]);

        return Inertia::render('AccessControl/AccessLogs/Index', [
            'logs'         => $logs,
            'filters'      => $request->only('movement_type', 'verification_method', 'access_category', 'search', 'date', 'solo_dentro'),
            'privateUnits' => PrivateUnit::select('id', 'lot_number', 'unit_street')->orderBy('lot_number')->get(),
            'categories'   => AccessLog::CATEGORIES,
        ]);
    }

    /**
     * Guardar un nuevo acceso (entrada manual del guardia).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_name'           => 'required|string|max:150',
            'visitor_company'        => 'nullable|string|max:150',
            'visitor_identification' => 'nullable|string|max:100',
            'vehicle_plate'          => 'nullable|string|max:20',
            'vehicle_brand'          => 'nullable|string|max:80',
            'vehicle_color'          => 'nullable|string|max:50',
            'access_category'        => 'required|in:Visita,Proveedor,Contratista,Conductor,Delivery,Otro',
            'verification_method'    => 'required|in:QR,RFID,Manual,Biometrico',
            'notes'                  => 'nullable|string|max:500',
            'private_unit_id'        => 'required|exists:private_units,id',
        ]);

        $validated['movement_type'] = AccessLog::MOVEMENT_ENTRADA;
        $validated['date_time'] = now();
        $validated['identifier'] = $validated['visitor_name'];

        // Si tiene placa, también la guardamos como identifier secundario
        if (!empty($validated['vehicle_plate'])) {
            $validated['identifier'] = $validated['visitor_name'] . ' | ' . $validated['vehicle_plate'];
        }

        AccessLog::create($validated);

        return redirect()->route('access-logs.index')
            ->with('success', 'Entrada registrada exitosamente.');
    }

    /**
     * Ver detalle de un acceso.
     */
    public function show(AccessLog $accessLog)
    {
        $accessLog->load(['privateUnit', 'user', 'visit']);

        return Inertia::render('AccessControl/AccessLogs/Show', [
            'accessLog' => [
                'id'                      => $accessLog->id,
                'identificador'           => $accessLog->displayName(),
                'visitor_name'            => $accessLog->visitor_name,
                'visitor_company'         => $accessLog->visitor_company,
                'visitor_identification'  => $accessLog->visitor_identification,
                'vehicle_plate'           => $accessLog->vehicle_plate,
                'vehicle_brand'           => $accessLog->vehicle_brand,
                'vehicle_color'           => $accessLog->vehicle_color,
                'movimiento'              => $accessLog->movement_type,
                'movementColor'           => $accessLog->movementColor(),
                'metodo'                  => $accessLog->verification_method,
                'access_category'         => $accessLog->access_category,
                'categoryLabel'           => $accessLog->categoryLabel(),
                'unidad'                  => $accessLog->privateUnit?->lot_number ?? 'N/A',
                'unidadCalle'             => $accessLog->privateUnit?->unit_street ?? '',
                'private_unit_id'         => $accessLog->private_unit_id,
                'fechaHora'               => $accessLog->date_time?->format('d/m/Y H:i'),
                'exit_time'               => $accessLog->exit_time?->format('d/m/Y H:i'),
                'sigueDentro'             => $accessLog->sigueDentro(),
                'estancia'                => $accessLog->estanciaHumana(),
                'notas'                   => $accessLog->notes,
                'foto_url'               => $accessLog->foto_url,
                'esVehicular'             => $accessLog->esVehicular(),
            ],
            'privateUnits' => PrivateUnit::select('id', 'lot_number', 'unit_street')->orderBy('lot_number')->get(),
            'categories'   => AccessLog::CATEGORIES,
        ]);
    }

    /**
     * Actualizar un registro de acceso.
     */
    public function update(Request $request, AccessLog $accessLog)
    {
        $validated = $request->validate([
            'visitor_name'           => 'required|string|max:150',
            'visitor_company'        => 'nullable|string|max:150',
            'visitor_identification' => 'nullable|string|max:100',
            'vehicle_plate'          => 'nullable|string|max:20',
            'vehicle_brand'          => 'nullable|string|max:80',
            'vehicle_color'          => 'nullable|string|max:50',
            'access_category'        => 'required|in:Visita,Proveedor,Contratista,Conductor,Delivery,Otro',
            'verification_method'    => 'required|in:QR,RFID,Manual,Biometrico',
            'notes'                  => 'nullable|string|max:500',
            'private_unit_id'        => 'required|exists:private_units,id',
        ]);

        // Reconstruir identifier
        $validated['identifier'] = $validated['visitor_name'];
        if (!empty($validated['vehicle_plate'])) {
            $validated['identifier'] .= ' | ' . $validated['vehicle_plate'];
        }

        $accessLog->update($validated);

        return redirect()->route('access-logs.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Eliminar un registro de acceso.
     */
    public function destroy(AccessLog $accessLog)
    {
        $accessLog->delete();

        return redirect()->route('access-logs.index')
            ->with('success', 'Registro eliminado de la bitácora.');
    }

    /**
     * Registrar la salida (checkout) de una entrada existente.
     */
    public function checkout(AccessLog $accessLog)
    {
        if ($accessLog->movement_type !== AccessLog::MOVEMENT_ENTRADA) {
            return back()->with('error', 'Solo se puede registrar salida de una Entrada.');
        }

        if ($accessLog->exit_time) {
            return back()->with('error', 'Este acceso ya tiene registrada la salida.');
        }

        $accessLog->realizarCheckout();

        return back()->with('success', "Salida registrada: {$accessLog->displayName()}");
    }
}
