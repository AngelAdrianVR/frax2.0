<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = [
        'type', // 'like', 'love', 'care', 'haha', 'wow', 'sad', 'angry'
        'reactable_id', 
        'reactable_type', 
        'resident_id'
    ];
    
    public function reactable()
    {
        return $this->morphTo();
    }
}
