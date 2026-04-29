<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
        protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

        protected $casts = [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
        
        public function teacher(): HasOne
        {
            return $this->hasOne(Teacher::class);
        }
        
        public function student(): HasOne
        {
            return $this->hasOne(Student::class);
        }
}
