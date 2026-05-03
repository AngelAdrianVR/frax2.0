<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name', 'date_time', 'participants', 'description', 'location', 
        'cost', 'capacity_event', 'capacity_per_resident', 'rules', 'subdivision_id'
    ];
    protected $casts = [
        'date_time' => 'datetime',
        'cost' => 'decimal:2',
        'rules' => 'array',
    ];
    
    // Polimorfismo
    public function comments() 
    { 
        return $this->morphMany(Comment::class, 'commentable'); 
    }
    
    public function reactions() {
         return $this->morphMany(Reaction::class, 'reactable'); 
    }
}
