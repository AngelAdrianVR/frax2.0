<?php

namespace App\Http\Controllers\Gatehouse;

use App\Models\Gatehouse\RegisterInvitation;
use App\Http\Controllers\Controller;
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

        return Inertia::render('Gatehouse/RegisterInvitations/Index', [
            'invitations' => $invitations
        ]);
    }
}