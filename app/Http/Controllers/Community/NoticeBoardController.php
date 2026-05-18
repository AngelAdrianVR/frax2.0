<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Community\Post;
use App\Models\Community\PollOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeBoardController extends Controller
{
    public function index()
    {
        // 1. Traemos las publicaciones con usuarios, comentarios y opciones de encuesta
        $posts = Post::with([
                'user:id,name', 
                'comments.user:id,name', 
                'pollOptions.votes' 
            ])
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20);

        // 2. Procesamos la data para que Vue la entienda perfectamente
        $posts->getCollection()->transform(function ($post) {
            if ($post->pollOptions && $post->pollOptions->count() > 0) {
                // Obtenemos cuántas personas únicas han votado
                $voterIds = collect();
                foreach($post->pollOptions as $option) {
                    $voterIds = $voterIds->merge($option->votes->pluck('user_id'));
                }
                $uniqueVotersCount = $voterIds->unique()->count();
                
                $post->poll_total_votes = $uniqueVotersCount;
                
                $post->pollOptions->transform(function ($option) use ($uniqueVotersCount) {
                    $optionCount = $option->votes->count();
                    $option->votes_count = $optionCount;
                    // El porcentaje ahora es basado en personas
                    $option->percentage = $uniqueVotersCount > 0 ? round(($optionCount / $uniqueVotersCount) * 100) : 0;
                    $option->has_voted = $option->votes->where('user_id', auth()->id())->isNotEmpty();
                    return $option;
                });

                $post->user_has_voted_any = $post->pollOptions->contains('has_voted', true);
                // Forzamos el nombre snake_case para que Vue lo lea correctamente
                $post->poll_options = $post->pollOptions; 
            }
            return $post;
        });

        return Inertia::render('Community/NoticeBoard/Index', [
            'posts' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'type' => 'nullable|in:general,announcement,alert',
            'is_pinned' => 'nullable|boolean',
            'is_multiple_choice' => 'nullable|boolean',
            'poll_options' => 'nullable|array|max:5',
            'poll_options.*' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:5120', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('notice-board', 'public');
        }

        $post = $request->user()->posts()->create([
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'general',
            'is_pinned' => $validated['is_pinned'] ?? false,
            'is_multiple_choice' => $validated['is_multiple_choice'] ?? false,
            'image_path' => $imagePath,
        ]);

        // 3. Si vienen opciones, las guardamos en la tabla de encuestas
        if (!empty($validated['poll_options'])) {
            foreach ($validated['poll_options'] as $optionText) {
                if (!empty(trim($optionText))) {
                    $post->pollOptions()->create(['text' => $optionText]);
                }
            }
        }

        return back()->with('success', 'Publicación creada exitosamente.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Publicación eliminada.');
    }

    // Aquí está la función update completa y corregida para manejar el check de "Fijar"
    public function update(Request $request, Post $post)
    {
        // Validamos que sea el dueño de la publicación o un admin
        $isAdmin = auth()->user()->hasRole('admin') || in_array(auth()->user()->role, ['Admin', 'Administrador', 'Empleado']);
        
        if (auth()->id() !== $post->user_id && !$isAdmin) {
            abort(403, 'No tienes permiso para editar esta publicación.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_pinned' => 'nullable|boolean', // Permitimos validar la casilla
            'is_poll_closed' => 'nullable|boolean',
        ]);

        // Actualizamos. Si viene el valor is_pinned lo usamos, si no, conservamos el actual.
        $post->update([
            'content' => $validated['content'],
            'is_pinned' => $request->has('is_pinned') ? $validated['is_pinned'] : $post->is_pinned,
            'is_poll_closed' => $request->has('is_poll_closed') ? $validated['is_poll_closed'] : $post->is_poll_closed,
        ]);

        return back()->with('success', 'Publicación actualizada.');
    }

    public function toggleReact(Post $post)
    {
        // SOLUCIÓN ERROR 1366: Pasamos el ID del usuario correctamente
        $post->toggleReaction(auth()->id(), 'like');
        return back();
    }

    public function storeComment(Request $request, Post $post)
    {
        // Agregamos el guardado de comentarios que faltaba
        $validated = $request->validate([
            'content' => 'required|string|max:500'
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content']
        ]);

        return back()->with('success', 'Comentario publicado.');
    }

    public function vote(Request $request, PollOption $pollOption)
    {
        $user_id = auth()->id();
        $post = $pollOption->post;

        if ($post->is_multiple_choice) {
            // Si es múltiple, alternamos el voto (poner/quitar)
            $existingVote = $pollOption->votes()->where('user_id', $user_id)->first();
            if ($existingVote) {
                $existingVote->delete();
            } else {
                $pollOption->votes()->create(['user_id' => $user_id]);
            }
        } else {
            // Si es única, eliminamos votos anteriores en esta encuesta y guardamos el nuevo (Cambio de voto)
            $postOptionIds = $post->pollOptions->pluck('id');
            \App\Models\Community\PollVote::whereIn('poll_option_id', $postOptionIds)
                ->where('user_id', $user_id)
                ->delete();

            $pollOption->votes()->create(['user_id' => $user_id]);
        }

        return back();
    }
}