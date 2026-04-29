<?php

namespace App\Http\Controllers;

use App\Models\RegisterInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterInvitationController extends Controller
{
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
                    'expires_at' => $invitation->expires_at ? $invitation->expires_at->format('d/m/Y') : 'N/A',
                ];
            });

        return Inertia::render('RegisterInvitations/Index', [
            'invitations' => $invitations
        ]);
    }
}