<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Pet extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    protected $fillable = [
        'name', 
        'species', 
        'race', 
        'additionals', 
        'private_unit_id'
    ];

    protected $casts = [
        'additionals' => 'array', // JSON
    ];

    /**
     * La unidad/casa a la que pertenece la mascota.
     */
    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
}