<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SessionPeriod extends Model
{
    protected $fillable = [
        'class_session_id', 'label',
        'time_in_start', 'time_in_end', 'late_start',
        'time_out_start', 'time_out_end',
        'grace_minutes', 'late_enabled', 'timeout_enabled'
    ];
    
    protected $casts = [
        'late_enabled' => 'boolean',
        'timeout_enabled' => 'boolean',
    ];
    
    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }
}
