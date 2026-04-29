<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttendanceLog extends Model
{
    protected $fillable = [
        'class_session_id', 'student_id', 'device_id', 'room_id',
        'status', 'time_in', 'time_out', 'is_manual_override', 'remarks'
    ];
    
    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'is_manual_override' => 'boolean',
    ];
    
    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }
    
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
    
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
