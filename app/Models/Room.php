<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    protected $fillable = [
        'code', 'name', 'building', 'floor', 'capacity', 'is_active'
    ];
    
    protected $casts = ['is_active' => 'boolean'];
    
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
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
