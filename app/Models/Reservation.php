<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['start_date_time', 
        'end_date_time', 
        'total_cost', 
        'status', // 'Pendiente', 'Aprobada', 'Rechazada', 'Cancelada', 'Completada'
        'amenity_id', 
        'resident_id'
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'total_cost' => 'decimal:2',
    ];
    
    public function amenity() 
    { 
        return $this->belongsTo(Amenity::class, 'amenity_id'); 
    }

    public function resident() 
    { 
        return $this->belongsTo(Resident::class, 'resident_id'); 
    }

}
