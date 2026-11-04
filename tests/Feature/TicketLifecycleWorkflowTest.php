<?php

namespace Tests\Feature;

use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TicketLifecycleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;
    protected Department $hrDept;
    protected User $superAdmin;
    protected User $itHod;
    protected User $technician;
    protected User $hrEmployee;
    protected ComplaintCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        $this->itDept = Department::create([
            'name' => 'IT Hardware & Networking',
            'code' => 'IT',
            'is_active' => true,
        ]);

        $this->hrDept = Department::create([
            'name' => 'Human Resources',
            'code' => 'HR',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Admin User',
            'username' => 'superadmin',
            'email' => 'admin@test.local',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->itHod = User::create([
            'name' => 'IT HOD',
            'username' => 'ithod',
            'email' => 'ithod@test.local',
            'department_id' => $this->itDept->id,
            'password' => bcrypt('password'),
            'role' => 'department_hod',
            'is_active' => true,
        ]);
        $this->itDept->update(['hod_user_id' => $this->itHod->id]);

        $this->technician = User::create([
            'name' => 'Ahmed Ali',
            'username' => 'tech_ahmed',
            'designation' => 'Network Technician',
            'department_id' => $this->itDept->id,
            'password' => bcrypt('password'),
            'role' => 'it_technician',
            'is_active' => true,
        ]);

        $this->hrEmployee = User::create([
            'name' => 'Zainab Bibi',
            'username' => 'hr_zainab',
            'designation' => 'HR Assistant',
            'department_id' => $this->hrDept->id,
            'password' => bcrypt('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        $this->category = ComplaintCategory::create([
            'name' => 'LAN / Network Connection',
            'slug' => 'lan-network-connection',
            'default_priority' => 'high',
            'is_active' => true,
        ]);
    }

    public function test_complete_ticket_lifecycle_scenario(): void
    {
        $workflowService = app(TicketWorkflowService::class);

        // 1. Employee submits a network complaint
        $ticket = $workflowService->createTicket([
            'title' => 'No internet connectivity in Room 104',
            'description' => 'The Ethernet wall port LED is unlit and network is unreachable.',
            'category_id' => $this->category->id,
            'priority' => 'high',
            'location' => 'HR Department, Ground Floor',
            'room_number' => '104',
        ], $this->hrEmployee);

        // Assert submission state
        $this->assertNotNull($ticket->tracking_id);
        $this->assertStringStartsWith('ITHN-', $ticket->tracking_id);
        $this->assertEquals('submitted', $ticket->status);
        $this->assertNotNull($ticket->sla_response_deadline);
        $this->assertNotNull($ticket->sla_resolution_deadline);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'tracking_id' => $ticket->tracking_id,
            'complainant_id' => $this->hrEmployee->id,
            'status' => 'submitted',
        ]);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'to_status' => 'submitted',
            'action_label' => 'Complaint Submitted',
        ]);

        // 2. IT HOD reviews and assigns technician Ahmed Ali
        $workflowService->assignTechnician($ticket, $this->technician, $this->itHod, 'Please check wall port patching in Room 104.');

        $ticket->refresh();
        $this->assertEquals('assigned', $ticket->status);
        $this->assertEquals($this->technician->id, $ticket->assigned_technician_id);
        $this->assertNotNull($ticket->assigned_at);
        $this->assertDatabaseHas('ticket_assignments', [
            'ticket_id' => $ticket->id,
            'assigned_to_user_id' => $this->technician->id,
            'assigned_by_user_id' => $this->itHod->id,
        ]);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'to_status' => 'assigned',
            'action_label' => 'Technician Assigned',
        ]);

        // 3. Technician Ahmed Ali starts work
        $workflowService->startWork($ticket, $this->technician, 'Inspecting patch panel 2 and testing cable continuity.');

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
        $this->assertNotNull($ticket->work_started_at);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'to_status' => 'in_progress',
            'action_label' => 'Work Started',
        ]);

        // 4. Technician marks work completed with resolution summary
        $workflowService->markWorkCompleted($ticket, $this->technician, 'Re-crimped RJ-45 connector and reconnected to switch port 14.');

        $ticket->refresh();
        $this->assertEquals('awaiting_confirmation', $ticket->status);
        $this->assertNotNull($ticket->completed_at);
        $this->assertStringContainsString('Re-crimped RJ-45', $ticket->resolution_summary);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'to_status' => 'awaiting_confirmation',
        ]);

        // 5. Complainant reports issue persists and reopens the complaint
        $workflowService->reopenTicket($ticket, $this->hrEmployee, 'Connection drops every 2 minutes when downloading files.');

        $ticket->refresh();
        $this->assertEquals('reopened', $ticket->status);
        $this->assertNotNull($ticket->reopened_at);
        $this->assertEquals('Connection drops every 2 minutes when downloading files.', $ticket->reopen_reason);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'to_status' => 'reopened',
            'action_label' => 'Complaint Reopened',
        ]);

        // 6. Technician addresses the issue and submits updated resolution
        $workflowService->markWorkCompleted($ticket, $this->technician, 'Replaced faulty patch cable with Cat6 shielded cable and configured port speed.');

        $ticket->refresh();
        $this->assertEquals('awaiting_confirmation', $ticket->status);

        // 7. Complainant confirms issue is resolved with 5-star rating
        $workflowService->confirmResolved($ticket, $this->hrEmployee, 5, 'Network speed is now stable and fast. Thank you!');

        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
        $this->assertNotNull($ticket->confirmed_resolved_at);
        $this->assertNotNull($ticket->closed_at);
        $this->assertDatabaseHas('ticket_feedback', [
            'ticket_id' => $ticket->id,
            'rating' => 5,
        ]);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'to_status' => 'closed',
            'action_label' => 'Resolution Confirmed',
        ]);

        // Verify full timeline preserves 6 distinct transitions
        $this->assertGreaterThanOrEqual(6, $ticket->statusHistories()->count());
    }

    public function test_complainant_receives_email_notifications_on_every_ticket_action(): void
    {
        $workflowService = app(TicketWorkflowService::class);

        // 1. Submit -> email to generator
        $ticket = $workflowService->createTicket([
            'title' => 'Printer jammed in HR',
            'description' => 'Paper stuck inside roller.',
            'category_id' => $this->category->id,
            'priority' => 'medium',
        ], $this->hrEmployee);

        Notification::assertSentTo(
            $this->hrEmployee,
            \App\Notifications\TicketEventNotification::class,
            fn ($n) => $n->eventType === 'submitted'
        );

        // 2. Assign -> email to generator
        $workflowService->assignTechnician($ticket, $this->technician, $this->itHod, 'Check printer rollers');
        Notification::assertSentTo(
            $this->hrEmployee,
            \App\Notifications\TicketEventNotification::class,
            fn ($n) => $n->eventType === 'assigned'
        );

        // 3. Work Started -> email to generator
        $workflowService->startWork($ticket, $this->technician, 'Inspecting printer hardware');
        Notification::assertSentTo(
            $this->hrEmployee,
            \App\Notifications\TicketEventNotification::class,
            fn ($n) => $n->eventType === 'work_started'
        );

        // 4. Progress reply -> email to generator
        $workflowService->addComment($ticket, $this->technician, 'Roller cleaned, running test print page.');
        Notification::assertSentTo(
            $this->hrEmployee,
            \App\Notifications\TicketEventNotification::class,
            fn ($n) => $n->eventType === 'progress_update'
        );

        // 5. Work Completed / Resolved -> email to generator
        $workflowService->markWorkCompleted($ticket, $this->technician, 'Paper cleared and rollers cleaned.');
        Notification::assertSentTo(
            $this->hrEmployee,
            \App\Notifications\TicketEventNotification::class,
            fn ($n) => $n->eventType === 'work_completed'
        );

        // 6. Confirmed Resolved & Closed -> confirmation email to generator
        $workflowService->confirmResolved($ticket, $this->hrEmployee, 5, 'Printing properly now.');
        Notification::assertSentTo(
            $this->hrEmployee,
            \App\Notifications\TicketEventNotification::class,
            fn ($n) => $n->eventType === 'confirmed_resolved'
        );
    }

    public function test_create_ticket_page_renders_successfully_with_balanced_schema(): void
    {
        $this->actingAs($this->superAdmin);

        \Livewire\Livewire::test(\App\Filament\Resources\Tickets\Pages\CreateTicket::class)
            ->assertSuccessful()
            ->assertFormFieldExists('title')
            ->assertFormFieldExists('category_id')
            ->assertFormFieldExists('description')
            ->assertFormFieldExists('complainant_id')
            ->assertFormFieldExists('department_id')
            ->assertFormFieldExists('location');
    }
}

