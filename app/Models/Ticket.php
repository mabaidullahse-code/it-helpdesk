<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'complainant_id',
        'department_id',
        'category_id',
        'subcategory_id',
        'asset_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_technician_id',
        'location',
        'room_number',
        'asset_tag_or_serial',
        'contact_number',
        'affected_users_count',
        'impact_level',
        'preferred_visit_time',
        'additional_info',
        'resolution_summary',
        'reopen_reason',
        'on_hold_reason',
        'cancellation_reason',
        'submitted_at',
        'reviewed_at',
        'assigned_at',
        'work_started_at',
        'completed_at',
        'confirmed_resolved_at',
        'reopened_at',
        'closed_at',
        'sla_response_deadline',
        'sla_resolution_deadline',
        'sla_responded_at',
        'is_sla_response_breached',
        'is_sla_resolution_breached',
        'sla_paused_at',
        'sla_total_paused_minutes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'work_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'confirmed_resolved_at' => 'datetime',
        'reopened_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_response_deadline' => 'datetime',
        'sla_resolution_deadline' => 'datetime',
        'sla_responded_at' => 'datetime',
        'is_sla_response_breached' => 'boolean',
        'is_sla_resolution_breached' => 'boolean',
        'sla_paused_at' => 'datetime',
        'sla_total_paused_minutes' => 'integer',
        'affected_users_count' => 'integer',
    ];

    // Relationships
    public function complainant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'complainant_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ComplaintSubcategory::class, 'subcategory_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class, 'ticket_id')->latest('assigned_at');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class, 'ticket_id')->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id')->latest();
    }

    public function publicComments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id')->where('is_internal', false)->latest();
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id')->where('is_internal', true)->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id')->latest();
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(TicketFeedback::class, 'ticket_id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailNotificationLog::class, 'ticket_id')->latest();
    }

    // Role-based scoping
    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin() || $user->isItHod()) {
            return $query; // Super Admin and IT HOD have global visibility over tickets
        }

        if ($user->isItTechnician()) {
            return $query->where('assigned_technician_id', $user->id);
        }

        if ($user->isDepartmentHod()) {
            return $query->where('department_id', $user->department_id);
        }

        // Regular employee can only see their own tickets
        return $query->where('complainant_id', $user->id);
    }

    // Convenience Scopes
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['closed', 'cancelled']);
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_technician_id')->whereIn('status', ['submitted', 'under_review']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['closed', 'cancelled'])
            ->where(function ($q) {
                $q->where('is_sla_resolution_breached', true)
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('sla_resolution_deadline')
                          ->where('sla_resolution_deadline', '<', now());
                  });
            });
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'cancelled'], true);
    }

    public function isOpen(): bool
    {
        return !$this->isClosed();
    }

    public function isAwaitingConfirmation(): bool
    {
        return $this->status === 'awaiting_confirmation';
    }

    public function isReopened(): bool
    {
        return $this->status === 'reopened';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'info',
            'under_review' => 'warning',
            'assigned' => 'primary',
            'in_progress' => 'info',
            'awaiting_information' => 'warning',
            'on_hold' => 'gray',
            'awaiting_confirmation' => 'purple',
            'reopened' => 'danger',
            'escalated' => 'danger',
            'closed' => 'success',
            'cancelled' => 'gray',
            default => 'secondary',
        };
    }
}
