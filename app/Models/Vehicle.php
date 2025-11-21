<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['plate', 'brand', 'model', 'color', 'tag_access', 'resident_id', 'private_unit_id'];

    public function resident() 
    { 
        return $this->belongsTo(Resident::class, 'resident_id'); 
    }
    
    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
}
