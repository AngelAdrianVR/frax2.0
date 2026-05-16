<?php

namespace App\Models\Community;

use App\Models\Community\PollVote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'text'];

    // Relación con los votos
    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    // Relación con el post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}