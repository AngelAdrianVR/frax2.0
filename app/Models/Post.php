<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['description', 
        'type', // 'General', 'Market', 'Noticia'
        'resident_id', 
        'subdivision_id'
];
    
    public function resident() { return $this->belongsTo(Resident::class, 'resident_id'); }
    
    // Polimorfismo
    public function comments() 
    { 
        return $this->morphMany(Comment::class, 'commentable'); 
    }
    
    public function reactions() 
    { 
        return $this->morphMany(Reaction::class, 'reactable'); 
    }
}
