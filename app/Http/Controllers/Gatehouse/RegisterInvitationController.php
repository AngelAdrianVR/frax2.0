<?php

namespace App\Http\Controllers\Gatehouse;

use App\Models\Gatehouse\RegisterInvitation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class RegisterInvitationController extends Controller
{
    /**
     * Muestra la lista de invitaciones.
     */
    public function index(Request $request)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        $invitations = RegisterInvitation::query()
            ->where('private_unit_id', $currentPropertyId)
            ->latest()
            ->paginate(15)
            ->through(function ($invitation) {
                return [
                    'id' => $invitation->id,
                    'email' => $invitation->email,
                    'role_type' => $invitation->role_type,
                    'status' => $invitation->status,
                    'expires_at' => $invitation->expires_at ? $invitation->expires_at->format('d/m/Y h:i A') : 'N/A',
                ];
            });

        return Inertia::render('Gatehouse/RegisterInvitations/Index', [
            'invitations' => $invitations
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva invitación.
     */
    public function create()
    {
        return Inertia::render('Gatehouse/RegisterInvitations/Create');
    }

    /**
     * Guarda la nueva invitación en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role_type' => 'required|in:Dueño,Familiar',
        ]);

        $currentPropertyId = $request->user()->getCurrentPropertyId();

        // Creamos la invitación
        RegisterInvitation::create([
            'email' => $validated['email'],
            'role_type' => $validated['role_type'],
            'token' => Str::random(40), // Generador de token seguro único
            'status' => 'Pendiente',
            'expires_at' => now()->addHours(48), // Expira en 48 horas
            'invited_by_user_id' => $request->user()->id,
            'private_unit_id' => $currentPropertyId,
        ]);

        // NOTA: Aquí iría la lógica para enviar el correo electrónico (Ej. Mail::to(...)->send(...))

        return redirect()->route('register-invitations.index');
    }

    /**
     * Muestra los detalles de una invitación específica.
     */
    public function show($id)
    {
        $invitation = RegisterInvitation::findOrFail($id);

        return Inertia::render('Gatehouse/RegisterInvitations/Show', [
            'invitation' => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'role_type' => $invitation->role_type,
                'status' => $invitation->status,
                'token' => $invitation->token,
                'expires_at' => $invitation->expires_at ? $invitation->expires_at->format('d/m/Y h:i A') : 'N/A',
                'created_at' => $invitation->created_at->format('d/m/Y h:i A'),
            ]
        ]);
    }

    /**
     * Muestra el formulario para editar una invitación.
     */
    public function edit($id)
    {
        $invitation = RegisterInvitation::findOrFail($id);

        return Inertia::render('Gatehouse/RegisterInvitations/Edit', [
            'invitation' => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'role_type' => $invitation->role_type,
                'status' => $invitation->status,
            ]
        ]);
    }

    /**
     * Actualiza la invitación en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $invitation = RegisterInvitation::findOrFail($id);

        // Validación de seguridad backend por si intentan editarla forzando la petición
        if ($invitation->status !== 'Pendiente') {
            return redirect()->back()->withErrors(['email' => 'Solo se pueden editar invitaciones pendientes.']);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role_type' => 'required|in:Dueño,Familiar',
        ]);

        $invitation->update([
            'email' => $validated['email'],
            'role_type' => $validated['role_type'],
        ]);

        return redirect()->route('register-invitations.index');
    }

    /**
     * Revoca (Elimina) la invitación de la base de datos.
     */
    public function destroy($id)
    {
        $invitation = RegisterInvitation::findOrFail($id);
        $invitation->delete();

        return redirect()->route('register-invitations.index');
    }
}