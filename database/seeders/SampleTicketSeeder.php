<?php

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Database\Seeder;

class SampleTicketSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'hr_staff')->first();
        $cat = ComplaintCategory::where('slug', 'lan-network-connection')->first() ?? ComplaintCategory::first();

        if ($user && $cat) {
            app(TicketWorkflowService::class)->createTicket([
                'title' => 'Network dropouts in HR office workstations',
                'description' => 'Workstations in HR Ground floor experience intermittent network dropouts when opening shared institutional databases.',
                'category_id' => $cat->id,
                'priority' => 'high',
                'location' => 'Admin Block, Ground Floor',
                'room_number' => 'Room 104',
                'contact_number' => $user->phone,
            ], $user);
        }
    }
}
