<?php

namespace Tests\Feature;

use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('UHS IT Helpdesk');
    }

    public function test_public_tracking_api_returns_ticket_details(): void
    {
        $dept = Department::create(['name' => 'Human Resources', 'code' => 'HR']);
        $cat = ComplaintCategory::create(['name' => 'Internet', 'slug' => 'internet']);
        $user = User::create([
            'name' => 'Sara Staff',
            'username' => 'sarastaff',
            'department_id' => $dept->id,
            'role' => 'employee',
            'password' => bcrypt('password'),
        ]);

        $ticket = app(TicketWorkflowService::class)->createTicket([
            'title' => 'Wi-Fi dropped',
            'description' => 'Cannot reach eduroam',
            'category_id' => $cat->id,
            'priority' => 'medium',
        ], $user);

        $response = $this->postJson('/api/track-complaint', [
            'tracking_id' => $ticket->tracking_id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'found' => true,
            'ticket' => [
                'tracking_id' => $ticket->tracking_id,
                'title' => 'Wi-Fi dropped',
            ],
        ]);
    }
}
