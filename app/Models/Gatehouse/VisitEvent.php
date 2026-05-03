<?php

namespace App\Models\Gatehouse;

use Illuminate\Database\Eloquent\Model;

class VisitEvent extends Model
{
    protected $fillable = [
        'name', 
        'qr_code', 
        'date_time_start', 
        'date_time_end', 
        'guest_amount', 
        'max_qr_uses', 
        'current_use_count', 
        'status', // 'Activo', 'Inactivo', 'Cancelado'
        'description', 
        'private_unit_id'
    ];

    protected $casts = [
        'date_time_start' => 'datetime',
        'date_time_end' => 'datetime',
        'guest_amount' => 'integer',
        'max_qr_uses' => 'integer',
        'current_use_count' => 'integer',
    ];

    public function privateUnit() { return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); }
    
    // Polimorfismo para comentarios
    public function comments() 
    { 
        return $this->morphMany(Comment::class, 'commentable'); 
    }
    
    public function reactions() 
    { 
        return $this->morphMany(Reaction::class, 'reactable'); 
    }
}
