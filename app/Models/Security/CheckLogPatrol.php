<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;

class CheckLogPatrol extends Model
{
    // Como es una tabla registro, indicamos la tabla si no sigue la convención plural estándar
    protected $table = 'check_log_patrols'; 

    protected $fillable = ['scan_date_time', 'patrol_id', 'checkpoint_id'];

    protected $casts = [
        'scan_date_time' => 'datetime',
    ];

    public function patrol() 
    { 
        return $this->belongsTo(Patrol::class, 'patrol_id'); 
    }
    
    public function checkpoint() 
    { 
        return $this->belongsTo(Checkpoint::class, 'checkpoint_id'); 
    }
}
