<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['name', 'species', 'race', 'additionals', 'resident_id', 'private_unit_id'];

    protected $casts = [
        'additionals' => 'array', // JSON
    ];

    // Relationships
    public function resident() 
    { 
        return $this->belongsTo(Resident::class, 'resident_id'); 
    }

    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
}
