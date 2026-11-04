<?php

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleTicketsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ComplaintCategory::all();
        $departments = Department::all();
        $technicians = User::where('role', 'it_technician')->get();
        $employees = User::whereIn('role', ['employee', 'department_hod'])->get();

        if ($categories->isEmpty() || $departments->isEmpty() || $employees->isEmpty()) {
            return;
        }

        $sampleIssues = [
            ['title' => 'Slow internet speed in Dean Office workstation', 'cat' => 'Internet Connectivity', 'priority' => 'high', 'status' => 'closed', 'days_ago' => 24],
            ['title' => 'Ethernet port damaged on Wall Panel Port #4', 'cat' => 'LAN / Network Connection', 'priority' => 'medium', 'status' => 'closed', 'days_ago' => 21],
            ['title' => 'Office Wi-Fi disconnection every 10 minutes', 'cat' => 'Wi-Fi Problems', 'priority' => 'medium', 'status' => 'closed', 'days_ago' => 19],
            ['title' => 'Desktop PC won\'t power on after power outage', 'cat' => 'Computer or Laptop Hardware', 'priority' => 'high', 'status' => 'closed', 'days_ago' => 17],
            ['title' => 'HP LaserJet 404 printer paper jam error', 'cat' => 'Printer and Scanner Issues', 'priority' => 'low', 'status' => 'closed', 'days_ago' => 15],
            ['title' => 'Shared department fileserver inaccessible from Finance', 'cat' => 'Server and Infrastructure Issues', 'priority' => 'critical', 'status' => 'closed', 'days_ago' => 14],
            ['title' => 'Outlook credentials pop-up loop and authentication error', 'cat' => 'Email and Account Access', 'priority' => 'medium', 'status' => 'closed', 'days_ago' => 12],
            ['title' => 'Need SPSS Statistics and Office Suite installed', 'cat' => 'Software Installation or Errors', 'priority' => 'low', 'status' => 'closed', 'days_ago' => 10],
            ['title' => 'Core switch port blinking amber in Academic Block', 'cat' => 'Network Switches, Routers, and Cabling', 'priority' => 'critical', 'status' => 'closed', 'days_ago' => 9],
            ['title' => 'Dual monitor setup request for Exam Data Operator', 'cat' => 'New Equipment or Installation', 'priority' => 'low', 'status' => 'closed', 'days_ago' => 8],
            ['title' => 'Wi-Fi signal weak in 2nd floor conference hall', 'cat' => 'Wi-Fi Problems', 'priority' => 'medium', 'status' => 'in_progress', 'days_ago' => 6],
            ['title' => 'Network printer driver missing on new Windows 11 laptop', 'cat' => 'Printer and Scanner Issues', 'priority' => 'low', 'status' => 'in_progress', 'days_ago' => 5],
            ['title' => 'Cannot access internal staff portal from HR LAN', 'cat' => 'LAN / Network Connection', 'priority' => 'high', 'status' => 'in_progress', 'days_ago' => 4],
            ['title' => 'Continuous blue screen BSOD on Finance Accounts PC', 'cat' => 'Computer or Laptop Hardware', 'priority' => 'high', 'status' => 'in_progress', 'days_ago' => 3],
            ['title' => 'Urgent: Internet connection down in Pathology Lab', 'cat' => 'Internet Connectivity', 'priority' => 'critical', 'status' => 'submitted', 'days_ago' => 2],
            ['title' => 'Institutional email mailbox quota exceeded 5GB', 'cat' => 'Email and Account Access', 'priority' => 'medium', 'status' => 'under_review', 'days_ago' => 2],
            ['title' => 'Scanner driver error on HP Multi-function machine', 'cat' => 'Printer and Scanner Issues', 'priority' => 'low', 'status' => 'submitted', 'days_ago' => 1],
            ['title' => 'Workstation relocation request to Room 115', 'cat' => 'New Equipment or Installation', 'priority' => 'low', 'status' => 'submitted', 'days_ago' => 1],
            ['title' => 'Network connectivity failure in Physiology Dept', 'cat' => 'LAN / Network Connection', 'priority' => 'high', 'status' => 'reopened', 'days_ago' => 3],
            ['title' => 'Emergency: DNS resolution failure across Main Admin building', 'cat' => 'Internet Connectivity', 'priority' => 'critical', 'status' => 'in_progress', 'days_ago' => 0],
        ];

        foreach ($sampleIssues as $index => $issue) {
            $cat = $categories->firstWhere('name', $issue['cat']) ?? $categories->first();
            $dept = $departments->random();
            $complainant = $employees->random();
            $tech = $technicians->isNotEmpty() ? $technicians->random() : null;

            $submittedAt = Carbon::now()->subDays($issue['days_ago'])->subHours(rand(1, 8));
            $isOverdue = in_array($issue['priority'], ['critical', 'high']) && in_array($issue['status'], ['submitted', 'in_progress', 'reopened']);

            $ticket = Ticket::create([
                'tracking_id' => 'ITHN-' . strtoupper(Str::random(6)),
                'complainant_id' => $complainant->id,
                'department_id' => $dept->id,
                'category_id' => $cat->id,
                'title' => $issue['title'],
                'description' => "Detailed report: {$issue['title']}. Location: {$dept->name}. Please assign technician for immediate inspection.",
                'priority' => $issue['priority'],
                'status' => $issue['status'],
                'assigned_technician_id' => in_array($issue['status'], ['in_progress', 'closed', 'reopened']) ? $tech?->id : null,
                'location' => $dept->location ?? 'Main Campus',
                'contact_number' => $complainant->phone ?? '0300-1122334',
                'submitted_at' => $submittedAt,
                'assigned_at' => in_array($issue['status'], ['in_progress', 'closed', 'reopened']) ? $submittedAt->copy()->addMinutes(30) : null,
                'work_started_at' => in_array($issue['status'], ['in_progress', 'closed', 'reopened']) ? $submittedAt->copy()->addHour() : null,
                'completed_at' => $issue['status'] === 'closed' ? $submittedAt->copy()->addHours(6) : null,
                'closed_at' => $issue['status'] === 'closed' ? $submittedAt->copy()->addHours(7) : null,
                'reopened_at' => $issue['status'] === 'reopened' ? $submittedAt->copy()->addDays(1) : null,
                'sla_resolution_deadline' => $submittedAt->copy()->addHours(match ($issue['priority']) {
                    'critical' => 4,
                    'high' => 12,
                    'medium' => 24,
                    'low' => 48,
                }),
                'is_sla_resolution_breached' => $isOverdue,
                'resolution_summary' => $issue['status'] === 'closed' ? 'Issue verified, physical connectivity restored, network throughput confirmed.' : null,
                'created_at' => $submittedAt,
                'updated_at' => Carbon::now()->subDays(max(0, $issue['days_ago'] - 1)),
            ]);
        }
    }
}

