<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'changed_by_user_id',
        'from_status',
        'to_status',
        'action_label',
        'comment',
        'is_visible_to_complainant',
    ];

    protected $casts = [
        'is_visible_to_complainant' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
