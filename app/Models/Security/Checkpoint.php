<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checkpoint extends Model
{
    protected $fillable = ['name', 'coordinates', 'tag_nfc_id'];
    
    // Relación inversa con logs de patrullaje
    public function logs(): HasMany
    {
        return $this->hasMany(CheckLogPatrol::class, 'checkpoint_id');
    }
}
