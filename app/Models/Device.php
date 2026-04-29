<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Device extends Model
{
    protected $fillable = [
        'room_id', 'device_uid', 'name', 'is_active', 'last_seen_at'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];
    
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
    
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
