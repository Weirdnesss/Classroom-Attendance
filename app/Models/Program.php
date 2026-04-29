<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Program extends Model
{
    protected $fillable = ['department_id', 'code', 'name', 'years', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
    
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
    
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }
}
