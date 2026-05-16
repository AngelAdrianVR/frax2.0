<?php

namespace App\Models\Community;

use App\Models\Community\User;
use App\Models\Community\PollOption;
use App\Models\Community\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'type',
        'is_pinned'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    protected $appends = [
        'created_at_human',
        'likes_count',
        'has_liked'
    ];

    // --- Relaciones ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // --- Accessors (Para mandar datos limpios a Vue) ---

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getLikesCountAttribute()
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function getHasLikedAttribute()
    {
        if (!auth()->check()) return false;
        return $this->reactions()->where('user_id', auth()->id())->where('type', 'like')->exists();
    }

    // --- Lógica de Negocio (Fat Model) ---

    /**
     * Alterna la reacción de un usuario (Si ya le dio like, se lo quita. Si no, se lo pone).
     */
    public function toggleReaction($userId, $type = 'like')
    {
        $reaction = $this->reactions()->where('user_id', $userId)->where('type', $type)->first();

        if ($reaction) {
            $reaction->delete();
            return false; // Reacción eliminada
        } else {
            $this->reactions()->create([
                'user_id' => $userId,
                'type' => $type
            ]);
            return true; // Reacción agregada
        }
    }

    public function pollOptions()
    {
        return $this->hasMany(PollOption::class);
    }

}