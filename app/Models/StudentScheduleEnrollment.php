<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentScheduleEnrollment extends Model
{
    protected $fillable = [
        'student_id', 'class_schedule_id', 'enrollment_type', 'is_active'
    ];
    
    protected $casts = ['is_active' => 'boolean'];
    
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }
}
