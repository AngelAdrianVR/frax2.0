<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vehicle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    protected $fillable = [
        'plate', 
        'brand', 
        'model', 
        'color', 
        'tag_access', 
        'user_id', 
        'private_unit_id'
    ];

    /**
     * El usuario dueño de este vehículo.
     */
    public function user() 
    { 
        return $this->belongsTo(User::class);
    }
    
    /**
     * La unidad/casa a la que pertenece el vehículo.
     */
    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
}