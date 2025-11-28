<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vehicle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    protected $fillable = [
        'plate', 
        'brand', 
        'model', 
        'color', 
        'tag_access', 
        'resident_id', 
        'private_unit_id'
    ];

    public function resident() 
    { 
        return $this->belongsTo(Resident::class, 'resident_id'); 
    }
    
    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
}
