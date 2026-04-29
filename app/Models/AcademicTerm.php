<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicTerm extends Model
{
    protected $fillable = [
        'academic_year_id', 'label', 'start_date', 'end_date', 'is_active'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
    
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
    
    public function periods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }
    
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }
    
    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }
}
