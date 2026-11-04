<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => static::getResource()::canDelete($this->getRecord())),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        // Synchronize plain_password with password
        if (!empty($data['password'])) {
            $data['plain_password'] = $data['password'];
        } elseif (!empty($data['plain_password']) && $data['plain_password'] !== $this->getRecord()->plain_password) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['plain_password']);
        }

        if ($user && !$user->isSuperAdmin()) {
            $data['department_id'] = $user->department_id;

            // HOD can never change their own role or any HOD's role
            if ($this->getRecord()->id === $user->id || $this->getRecord()->role === 'department_hod') {
                $data['role'] = $this->getRecord()->role;
            }

            // Prevent escalating to super_admin or department_hod for regular staff
            if (in_array($data['role'] ?? '', ['super_admin', 'department_hod'], true) && $this->getRecord()->role !== 'department_hod') {
                $data['role'] = $this->getRecord()->role;
            }

            // Non-superadmin cannot change account active status
            $data['is_active'] = $this->getRecord()->is_active;
        }

        // Users cannot deactivate their own accounts
        if ($user && $this->getRecord()->id === $user->id) {
            $data['is_active'] = true;
        }

        return $data;
    }
}
