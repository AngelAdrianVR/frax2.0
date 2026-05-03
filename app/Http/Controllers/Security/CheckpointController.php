<?php

namespace App\Http\Controllers\Security;

use App\Models\Security\Checkpoint;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckpointController extends Controller
{
    public function index(Request $request)
    {
        $checkpoints = Checkpoint::latest()
            ->paginate(15)
            ->through(function ($checkpoint) {
                return [
                    'id' => $checkpoint->id,
                    'name' => $checkpoint->name,
                    'coordinates' => $checkpoint->coordinates ?? 'No definidas',
                    'tag_nfc_id' => $checkpoint->tag_nfc_id ?? 'Sin Tag Asignado',
                ];
            });

        return Inertia::render('Security/Checkpoints/Index', [
            'checkpoints' => $checkpoints
        ]);
    }
}