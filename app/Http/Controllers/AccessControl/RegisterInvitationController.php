<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\RegisterInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterInvitationController extends Controller
{
    public function index(Request $request)
    {
        $query = RegisterInvitation::latest();

        if ($request->user()->getCurrentPropertyId()) {
            $query->deUnidad($request->user()->getCurrentPropertyId());
        }

        $invitations = $query->paginate(15)
            ->through(fn($invitation) => [
                'id'         => $invitation->id,
                'email'      => $invitation->email,
                'role_type'  => $invitation->role_type,
                'status'     => $invitation->status,
                'vigente'    => $invitation->estaVigente(),
                'expirada'   => $invitation->estaExpirada(),
                'expires_at' => $invitation->expires_at?->format('d/m/Y h:i A'),
            ]);

        return Inertia::render('AccessControl/RegisterInvitations/Index', [
            'invitations' => $invitations,
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/RegisterInvitations/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'     => 'required|email|max:255',
            'role_type' => 'required|in:Dueño,Familiar',
        ]);

        RegisterInvitation::generarInvitacion([
            'email'              => $validated['email'],
            'role_type'          => $validated['role_type'],
            'invited_by_user_id' => $request->user()->id,
            'private_unit_id'    => $request->user()->getCurrentPropertyId(),
        ]);

        // TODO: Enviar correo electrónico con el token

        return redirect()->route('register-invitations.index')
            ->with('success', 'Invitación enviada exitosamente.');
    }

    public function show(RegisterInvitation $registerInvitation)
    {
        return Inertia::render('AccessControl/RegisterInvitations/Show', [
            'invitation' => [
                'id'         => $registerInvitation->id,
                'email'      => $registerInvitation->email,
                'role_type'  => $registerInvitation->role_type,
                'status'     => $registerInvitation->status,
                'token'      => $registerInvitation->token,
                'vigente'    => $registerInvitation->estaVigente(),
                'expirada'   => $registerInvitation->estaExpirada(),
                'expires_at' => $registerInvitation->expires_at?->format('d/m/Y h:i A'),
                'created_at' => $registerInvitation->created_at?->format('d/m/Y h:i A'),
            ],
        ]);
    }

    public function edit(RegisterInvitation $registerInvitation)
    {
        return Inertia::render('AccessControl/RegisterInvitations/Edit', [
            'invitation' => $registerInvitation->only([
                'id', 'email', 'role_type', 'status',
            ]),
        ]);
    }

    public function update(Request $request, RegisterInvitation $registerInvitation)
    {
        $validated = $request->validate([
            'email'     => 'required|email|max:255',
            'role_type' => 'required|in:Dueño,Familiar',
            'status'    => 'required|in:Pendiente,Aceptado,Expirado',
        ]);

        $registerInvitation->update($validated);

        return redirect()->route('register-invitations.index')
            ->with('success', 'Invitación actualizada.');
    }

    public function destroy(RegisterInvitation $registerInvitation)
    {
        $registerInvitation->delete();

        return redirect()->route('register-invitations.index')
            ->with('success', 'Invitación eliminada.');
    }
}
