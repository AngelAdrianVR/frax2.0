<?php

namespace App\Models\Community;

use App\Models\Community\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'content'
    ];

    protected $appends = [
        'created_at_human'
    ];

    // --- Relaciones ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // --- Accessors ---

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}