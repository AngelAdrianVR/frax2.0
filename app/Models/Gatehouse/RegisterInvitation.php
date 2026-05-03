<?php

namespace App\Models\Gatehouse;

use Illuminate\Database\Eloquent\Model;

class RegisterInvitation extends Model
{
    protected $fillable = [
        'email', 
        'token', 
        'role_type', // 'Dueño', 'Familiar'
        'status', // 'Pendiente', 'Aceptado', 'Expirado'
        'expires_at', 
        'invited_by_user_id', 
        'private_unit_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function inviter() 
    { 
        return $this->belongsTo(User::class, 'invited_by_user_id'); 
    }
    
    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
}
