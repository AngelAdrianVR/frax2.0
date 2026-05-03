<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patrol extends Model
{
    protected $fillable = ['start_time', 
        'end_time', 
        'status',  // 'Activo', 'Terminado', 'Incidente'
        'scanned_points', 
        'user_id'
];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'scanned_points' => 'integer',
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }

    public function logs(): HasMany
    {
        return $this->hasMany(CheckLogPatrol::class, 'patrol_id');
    }
}
