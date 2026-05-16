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
                $totalVotes = $post->pollOptions->sum(function($option) {
                    return $option->votes->count();
                });
                
                $post->poll_total_votes = $totalVotes;
                
                $post->pollOptions->transform(function ($option) use ($totalVotes) {
                    $optionCount = $option->votes->count();
                    $option->votes_count = $optionCount;
                    $option->percentage = $totalVotes > 0 ? round(($optionCount / $totalVotes) * 100) : 0;
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
            'poll_options' => 'nullable|array|max:5', // Validamos la encuesta
            'poll_options.*' => 'nullable|string|max:100'
        ]);

        $post = $request->user()->posts()->create([
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'general',
            'is_pinned' => $validated['is_pinned'] ?? false,
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

    public function toggleReact(Post $post)
    {
        // 4. SOLUCIÓN ERROR 1366: Pasamos el ID del usuario correctamente
        $post->toggleReaction(auth()->id(), 'like');
        return back();
    }

    public function storeComment(Request $request, Post $post)
    {
        // 5. Agregamos el guardado de comentarios que faltaba
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
        // 6. Agregamos la lógica para votar en la encuesta
        $user_id = auth()->id();
        $post = $pollOption->post;

        $hasVotedInThisPost = $post->pollOptions()->whereHas('votes', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->exists();

        if ($hasVotedInThisPost) {
            return back()->with('error', 'Ya has votado en esta encuesta.');
        }

        $pollOption->votes()->create([
            'user_id' => $user_id
        ]);

        return back();
    }
}