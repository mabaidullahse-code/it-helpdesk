<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'hod_user_id',
        'phone',
        'email',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'department_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'department_id');
    }

    public function isItDepartment(): bool
    {
        return strtoupper((string) $this->code) === 'IT';
    }
}
