<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\AccessLog;
use App\Models\AccessControl\Patrol;
use App\Models\AccessControl\ParcelService;
use App\Models\AccessControl\Visit;
use App\Models\AccessControl\Incident;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Dashboard Unificado del Guardia.
 * 
 * Proporciona una vista integral de todo lo que el guardia necesita:
 * - Visitas pendientes de hoy
 * - Paquetes en caseta
 * - Rondines activos
 * - Incidencias abiertas
 * - Últimos accesos registrados
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('AccessControl/Dashboard', [
            'stats' => $this->getStats(),
            'visitasPendientes' => $this->getVisitasPendientes(),
            'paquetesEnCaseta'  => $this->getPaquetesEnCaseta(),
            'rondinesActivos'   => $this->getRondinesActivos(),
            'incidenciasAbiertas' => $this->getIncidenciasAbiertas(),
            'ultimosAccesos'    => $this->getUltimosAccesos(),
        ]);
    }

    // ─── Datos para el Dashboard ────────────────────────────────────

    private function getStats(): array
    {
        return [
            'visitasPendientes' => Visit::pendientes()->count(),
            'paquetesEnCaseta'  => ParcelService::enCaseta()->count(),
            'rondinesActivos'   => Patrol::activos()->count(),
            'incidenciasAbiertas' => Incident::abiertos()->count(),
            'accesosHoy'        => AccessLog::hoy()->count(),
            'entradasHoy'       => AccessLog::hoy()->entradas()->count(),
            'salidasHoy'        => AccessLog::hoy()->salidas()->count(),
        ];
    }

    private function getVisitasPendientes()
    {
        return Visit::with('privateUnit')
            ->pendientes()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($v) => [
                'id'              => $v->id,
                'nombre'          => $v->name,
                'razon'           => $v->reason,
                'tipoAcceso'      => $v->access_type,
                'unidad'          => $v->privateUnit?->lot_number ?? 'N/A',
                'expira'          => $v->expiration_date?->diffForHumans(),
                'statusColor'     => $v->statusColor(),
            ]);
    }

    private function getPaquetesEnCaseta()
    {
        return ParcelService::with('privateUnit')
            ->enCaseta()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($p) => $p->resumenDashboard());
    }

    private function getRondinesActivos()
    {
        return Patrol::with('user')
            ->activos()
            ->latest('start_time')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'guardia'     => $p->user?->name ?? 'N/A',
                'inicio'      => $p->start_time?->diffForHumans(),
                'puntos'      => $p->scanned_points,
                'duracionMin' => $p->duracionMinutos() ?? 'En curso',
            ]);
    }

    private function getIncidenciasAbiertas()
    {
        return Incident::with('reportedBy')
            ->abiertos()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($i) => [
                'id'         => $i->id,
                'titulo'     => $i->title,
                'severidad'  => $i->severity,
                'tipo'       => $i->incident_type,
                'guardia'    => $i->reportedBy?->name ?? 'N/A',
                'hace'       => $i->created_at?->diffForHumans(),
                'color'      => $i->severidadColor(),
            ]);
    }

    private function getUltimosAccesos()
    {
        return AccessLog::with(['privateUnit', 'user'])
            ->latest('date_time')
            ->take(20)
            ->get()
            ->map(fn($log) => [
                'id'         => $log->id,
                'identificador' => $log->identifier ?? ($log->user?->name ?? 'Desconocido'),
                'movimiento' => $log->movement_type,
                'metodo'     => $log->verification_method,
                'unidad'     => $log->privateUnit?->lot_number ?? 'N/A',
                'fechaHora'  => $log->date_time?->format('d/m/Y H:i'),
            ]);
    }
}
