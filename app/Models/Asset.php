<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'asset_type',
        'name',
        'manufacturer',
        'model',
        'serial_number',
        'department_id',
        'assigned_user_id',
        'location',
        'ip_address',
        'mac_address',
        'status',
        'purchase_date',
        'warranty_expiry',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'asset_id');
    }
}
