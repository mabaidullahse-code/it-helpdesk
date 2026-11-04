<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'response_time_hours',
        'resolution_time_hours',
        'escalation_threshold_hours',
        'reminder_interval_hours',
        'description',
        'is_active',
    ];

    protected $casts = [
        'response_time_hours' => 'integer',
        'resolution_time_hours' => 'integer',
        'escalation_threshold_hours' => 'integer',
        'reminder_interval_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function getPolicyForPriority(string $priority): ?self
    {
        return static::where('priority', $priority)->where('is_active', true)->first();
    }
}
