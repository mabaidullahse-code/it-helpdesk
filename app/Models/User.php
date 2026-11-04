<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'designation',
        'employee_id',
        'department_id',
        'role',
        'is_active',
        'password',
        'plain_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function headedDepartment(): HasOne
    {
        return $this->hasOne(Department::class, 'hod_user_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'complainant_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_technician_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'user_id');
    }

    public function assignmentsGiven(): HasMany
    {
        return $this->hasMany(TicketAssignment::class, 'assigned_by_user_id');
    }

    public function assignmentsReceived(): HasMany
    {
        return $this->hasMany(TicketAssignment::class, 'assigned_to_user_id');
    }

    // Role helper checks
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isDepartmentHod(): bool
    {
        return $this->role === 'department_hod';
    }

    public function isItHod(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->role === 'department_hod') {
            $dept = $this->department ?? $this->headedDepartment;
            return $dept && $dept->isItDepartment();
        }

        return false;
    }

    public function isItTechnician(): bool
    {
        return $this->role === 'it_technician';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isItStaff(): bool
    {
        return $this->isSuperAdmin() || $this->isItHod() || $this->isItTechnician();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }
}
