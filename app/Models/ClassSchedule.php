<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassSchedule extends Model
{
    protected $fillable = [
        'academic_term_id', 'subject_id', 'teacher_id', 'room_id',
        'program_id', 'year_level', 'days', 'start_time', 'end_time', 'is_active'
    ];
    
    protected $casts = [
        'days' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }
    
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
    
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
    
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
    
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentScheduleEnrollment::class);
    }
    
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_schedule_enrollments')
                    ->withPivot('enrollment_type', 'is_active')
                    ->withTimestamps();
    }
    
    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }
}
