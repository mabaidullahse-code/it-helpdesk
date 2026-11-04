<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isSuperAdmin() || $user->isItHod()) {
            return true;
        }

        if ($user->isItTechnician()) {
            return $ticket->assigned_technician_id === $user->id;
        }

        if ($user->isDepartmentHod()) {
            return $ticket->department_id === $user->department_id;
        }

        // Complainant only sees their own ticket
        return $ticket->complainant_id === $user->id;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_active;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function startWork(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin() || $user->isItHod() || ($user->isItTechnician() && $ticket->assigned_technician_id === $user->id);
    }

    public function completeWork(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin() || $user->isItHod() || ($user->isItTechnician() && $ticket->assigned_technician_id === $user->id);
    }

    public function confirmResolution(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->complainant_id || $user->isSuperAdmin();
    }

    public function reopen(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->complainant_id || $user->isSuperAdmin() || $user->isItHod();
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isSuperAdmin();
    }
}
