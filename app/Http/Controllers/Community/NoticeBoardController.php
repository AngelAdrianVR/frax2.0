<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Community\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeBoardController extends Controller
{
    public function index()
    {
        // Obtenemos las publicaciones ordenadas (primero las fijadas, luego las más recientes)
        $posts = Post::with(['user:id,name']) // Solo traemos los datos necesarios del usuario
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20);

        return Inertia::render('Community/NoticeBoard/Index', [
            'posts' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'type' => 'nullable|in:general,announcement,alert',
            'is_pinned' => 'nullable|boolean'
        ]);

        // Aseguramos que solo los admins puedan fijar publicaciones o hacer avisos oficiales
        if (!auth()->user()->hasRole('admin')) {
            $validated['type'] = 'general';
            $validated['is_pinned'] = false;
        }

        $request->user()->posts()->create($validated);

        return back()->with('success', 'Publicación creada exitosamente.');
    }

    public function destroy(Post $post)
    {
        // Solo el autor o un administrador puede eliminarla
        if (auth()->id() !== $post->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para eliminar esta publicación.');
        }

        $post->delete();

        return back()->with('success', 'Publicación eliminada.');
    }

    public function toggleReact(Post $post)
    {
        // El modelo se encarga de la lógica (Principio SOLID)
        $post->toggleReaction(auth()->id(), 'like');

        return back(); // Inertia actualizará la vista mágicamente sin recargar
    }
}