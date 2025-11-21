<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    protected $table = 'access_log';

    protected $fillable = [
        'identifier', 
        'movement_type', 
        'date_time', 
        'verification_method', 
        'notes', 
        'foto_url', 
        'private_unit_id', 
        'user_id', 
        'visit_event_id',
        'visit_id'     
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function privateUnit(): BelongsTo
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
    
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'user_id'); 
    }
    
    public function visitEvent(): BelongsTo
    { 
        return $this->belongsTo(VisitEvent::class, 'visit_event_id'); 
    }
    
    public function visit(): BelongsTo
    { 
        return $this->belongsTo(Visit::class, 'visit_id'); 
    }
}