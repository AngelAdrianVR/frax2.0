<?php

namespace App\Http\Controllers\Community;

use App\Models\Community\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TagController extends Controller
{
    /**
     * Guarda un nuevo Tag asociado a una propiedad.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag_code' => 'required|string|max:50|unique:tags,tag_code',
            'private_unit_id' => 'required|exists:private_units,id'
        ], [
            'tag_code.unique' => 'Este código de Tag ya está registrado en otra propiedad.'
        ]);

        Tag::create([
            'tag_code' => $validated['tag_code'],
            'status' => 'Activo', // Por defecto se crea activo
            'private_unit_id' => $validated['private_unit_id'],
        ]);

        // Retornamos hacia atrás para no romper el modal de Vue
        return Redirect::back()->with('success', 'Tag de acceso registrado correctamente.');
    }

    /**
     * Elimina un Tag.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return Redirect::back()->with('success', 'Tag eliminado correctamente.');
    }
}