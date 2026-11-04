<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        // Synchronize plain_password with password
        if (!empty($data['password'])) {
            if (empty($data['plain_password'])) {
                $data['plain_password'] = $data['password'];
            }
        } elseif (!empty($data['plain_password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['plain_password']);
        }

        // Enforce departmental isolation on the server side
        if ($user && !$user->isSuperAdmin()) {
            $data['department_id'] = $user->department_id;
            if ($user->isItHod()) {
                if (!in_array($data['role'] ?? '', ['it_technician', 'employee'], true)) {
                    $data['role'] = 'employee';
                }
            } else {
                $data['role'] = 'employee';
            }
            $data['is_active'] = true;
        }

        return $data;
    }
}
