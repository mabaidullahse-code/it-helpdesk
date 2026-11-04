<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->isSuperAdmin()) {
            $data['complainant_id'] = $user->id;
            $data['department_id'] = $user->department_id;
        }

        $complainant = User::findOrFail($data['complainant_id']);

        return app(TicketWorkflowService::class)->createTicket($data, $complainant);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
