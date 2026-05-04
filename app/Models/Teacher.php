<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 'department_id', 'employee_id',
        'first_name', 'last_name', 'middle_name', 'is_active'
    ];
    
    protected $casts = ['is_active' => 'boolean'];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }
    
    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }
    
    // Helper
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
