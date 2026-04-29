<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'program_id', 'student_id',
        'first_name', 'last_name', 'middle_name',
        'rfid_tag', 'year_level', 'is_irregular', 'is_active'
    ];
    
    protected $casts = [
        'is_irregular' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentScheduleEnrollment::class);
    }
    
    public function classSchedules(): BelongsToMany
    {
        return $this->belongsToMany(ClassSchedule::class, 'student_schedule_enrollments')
                    ->withPivot('enrollment_type', 'is_active')
                    ->withTimestamps();
    }
    
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
    
    // Helper
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
