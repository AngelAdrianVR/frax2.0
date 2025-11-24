<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrequentVisitor extends Model
{
    protected $fillable = [
        'alias', 
        'name', 
        'default_reason', 
        'default_access_type', // Peatonal o Vehicular
        'default_plate', 
        'private_unit_id'
    ];

    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
}
