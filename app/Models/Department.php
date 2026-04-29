<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    protected $fillable = ['code', 'name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
    
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
    
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
    
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}
