<?php

namespace Tests\Feature;

use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ReportExportService;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected Department $dept;
    protected User $admin;
    protected User $employee;
    protected ComplaintCategory $category;
    protected Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dept = Department::create(['name' => 'IT Department', 'code' => 'IT']);
        $this->admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'department_id' => $this->dept->id,
        ]);
        $this->employee = User::create([
            'name' => 'Employee',
            'username' => 'emp1',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'department_id' => $this->dept->id,
        ]);
        $this->category = ComplaintCategory::create(['name' => 'Hardware', 'slug' => 'hardware']);

        $this->ticket = app(TicketWorkflowService::class)->createTicket([
            'title' => 'Monitor flickers',
            'description' => 'Display cable loose',
            'category_id' => $this->category->id,
            'priority' => 'low',
        ], $this->employee);
    }

    public function test_csv_export_returns_streamed_response(): void
    {
        $response = app(ReportExportService::class)->exportCsv([], $this->admin);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_excel_export_returns_streamed_response(): void
    {
        $response = app(ReportExportService::class)->exportExcel([], $this->admin);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('content-type'));
    }

    public function test_individual_ticket_pdf_generates_successfully(): void
    {
        $pdf = app(ReportExportService::class)->generateTicketPdf($this->ticket);
        $this->assertNotNull($pdf);
        $this->assertEquals(200, $pdf->getStatusCode());
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));
    }

    public function test_reports_pdf_generates_successfully(): void
    {
        $pdf = app(ReportExportService::class)->exportPdf([], $this->admin);
        $this->assertNotNull($pdf);
        $this->assertEquals(200, $pdf->getStatusCode());
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));
    }
}
