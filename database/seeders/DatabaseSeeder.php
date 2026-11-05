<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\ComplaintCategory;
use App\Models\ComplaintSubcategory;
use App\Models\Department;
use App\Models\KnowledgeArticle;
use App\Models\SlaPolicy;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1 & 2. Seed Departments and Users
        $this->call(UserSeeder::class);

        // Fetch references for relational seeders
        $itDept = Department::where('code', 'IT')->first();
        $hrDept = Department::where('code', 'HR')->first();
        $finDept = Department::where('code', 'FIN')->first();
        $itHod = User::where('username', 'ithod')->first();
        $tech1 = User::where('username', 'tech_ahmed')->first();
        $hrStaff = User::where('username', 'hr_staff')->first();
        $finStaff = User::where('username', 'fin_staff')->first();

        // 3. Complaint Categories & Subcategories
        $categories = [
            [
                'name' => 'Internet Connectivity',
                'priority' => 'high',
                'description' => 'Organization-wide, departmental, or workstation internet access issues.',
                'subcategories' => ['Slow Internet Speed', 'Total Connection Loss', 'DNS Resolution Failure', 'Gateway Unreachable'],
            ],
            [
                'name' => 'LAN / Network Connection',
                'priority' => 'high',
                'description' => 'Local area network, Ethernet cabling, and wall port connectivity.',
                'subcategories' => ['No Ethernet Link', 'Damaged RJ-45 Connector', 'Port Disconnected', 'Packet Loss'],
            ],
            [
                'name' => 'Wi-Fi Problems',
                'priority' => 'medium',
                'description' => 'Wireless access point coverage, authentication, or connectivity failures.',
                'subcategories' => ['Cannot Connect to SSID', 'Frequent Disconnections', 'Weak Signal / Dead Zone', 'SSID Not Visible'],
            ],
            [
                'name' => 'Computer or Laptop Hardware',
                'priority' => 'medium',
                'description' => 'Workstation desktop and laptop power, display, RAM, and hardware faults.',
                'subcategories' => ['No Power / Won\'t Turn On', 'Blue Screen of Death (BSOD)', 'Monitor / Display Failure', 'Overheating / Fan Noise', 'Hard Drive Failure'],
            ],
            [
                'name' => 'Printer and Scanner Issues',
                'priority' => 'medium',
                'description' => 'Network printers, multi-function copiers, scanners, and print spooler errors.',
                'subcategories' => ['Printer Offline', 'Paper Jam', 'Toner Replacement Needed', 'Scanner Not Responding', 'Driver Error'],
            ],
            [
                'name' => 'Server and Infrastructure Issues',
                'priority' => 'critical',
                'description' => 'Core server infrastructure, virtualization, shared file servers, and domain controllers.',
                'subcategories' => ['File Server Inaccessible', 'Domain Authentication Down', 'Backup Failure', 'Storage Array Warning'],
            ],
            [
                'name' => 'Email and Account Access',
                'priority' => 'medium',
                'description' => 'Institutional email, Outlook configuration, password resets, and account unlock.',
                'subcategories' => ['Cannot Login to Outlook', 'Password Reset Request', 'Mailbox Quota Full', 'Spam / Phishing Alert'],
            ],
            [
                'name' => 'Software Installation or Errors',
                'priority' => 'low',
                'description' => 'Standard office software installation, antivirus updates, and licensed applications.',
                'subcategories' => ['Office Suite Activation', 'Antivirus Signature Update', 'Specialized ERP/App Setup', 'Browser Crash'],
            ],
            [
                'name' => 'Network Switches, Routers, and Cabling',
                'priority' => 'critical',
                'description' => 'Departmental network switch failure, rack maintenance, and trunk lines.',
                'subcategories' => ['Switch Reboot / Crash', 'Fiber Uplink Down', 'Patch Panel Re-punching'],
            ],
            [
                'name' => 'New Equipment or Installation',
                'priority' => 'low',
                'description' => 'Deploying newly procured PC, laptop, or peripheral equipment.',
                'subcategories' => ['New PC Deployment', 'Workstation Relocation', 'Peripheral Setup'],
            ],
            [
                'name' => 'Other IT Issues',
                'priority' => 'medium',
                'description' => 'General technical assistance and inquiries.',
                'subcategories' => ['General Technical Inquiry', 'Multimedia / Projector Setup'],
            ],
        ];

        $order = 1;
        foreach ($categories as $catData) {
            $cat = ComplaintCategory::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'default_priority' => $catData['priority'],
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );

            foreach ($catData['subcategories'] as $sub) {
                ComplaintSubcategory::firstOrCreate(
                    [
                        'category_id' => $cat->id,
                        'name' => $sub,
                    ],
                    [
                        'is_active' => true,
                    ]
                );
            }
        }

        // 4. SLA Policies
        SlaPolicy::firstOrCreate(
            ['priority' => 'critical'],
            [
                'name' => 'Critical Priority Policy',
                'response_time_hours' => 1,
                'resolution_time_hours' => 4,
                'escalation_threshold_hours' => 3,
                'reminder_interval_hours' => 1,
                'description' => 'Organization-wide outages, core network switches down, or major infrastructure breakdown.',
                'is_active' => true,
            ]
        );

        SlaPolicy::firstOrCreate(
            ['priority' => 'high'],
            [
                'name' => 'High Priority Policy',
                'response_time_hours' => 4,
                'resolution_time_hours' => 12,
                'escalation_threshold_hours' => 10,
                'reminder_interval_hours' => 2,
                'description' => 'Substantial departmental disruption, departmental internet down, or primary printer offline.',
                'is_active' => true,
            ]
        );

        SlaPolicy::firstOrCreate(
            ['priority' => 'medium'],
            [
                'name' => 'Medium Priority Policy',
                'response_time_hours' => 8,
                'resolution_time_hours' => 24,
                'escalation_threshold_hours' => 20,
                'reminder_interval_hours' => 4,
                'description' => 'Issue affecting an individual user or minor departmental convenience.',
                'is_active' => true,
            ]
        );

        SlaPolicy::firstOrCreate(
            ['priority' => 'low'],
            [
                'name' => 'Low Priority Policy',
                'response_time_hours' => 24,
                'resolution_time_hours' => 48,
                'escalation_threshold_hours' => 40,
                'reminder_interval_hours' => 8,
                'description' => 'Routine assistance, non-urgent software installations, or general queries.',
                'is_active' => true,
            ]
        );

        // 5. Assets Register
        if ($hrDept && $hrStaff) {
            Asset::firstOrCreate(
                ['asset_tag' => 'AST-2026-001'],
                [
                    'asset_type' => 'Computer',
                    'name' => 'HR Operations Desktop',
                    'manufacturer' => 'Dell',
                    'model' => 'OptiPlex 7090 MT',
                    'serial_number' => 'DL7090-99214',
                    'department_id' => $hrDept->id,
                    'assigned_user_id' => $hrStaff->id,
                    'location' => 'Admin Block, Ground Floor, Room 104',
                    'ip_address' => '192.168.10.45',
                    'mac_address' => '00:1A:2B:3C:4D:5E',
                    'status' => 'in_service',
                    'purchase_date' => '2024-03-15',
                    'warranty_expiry' => '2027-03-15',
                    'notes' => '16GB RAM, 512GB NVMe SSD, Core i7-11700',
                ]
            );

            Asset::firstOrCreate(
                ['asset_tag' => 'AST-2026-002'],
                [
                    'asset_type' => 'Printer',
                    'name' => 'HR Shared Network Printer',
                    'manufacturer' => 'HP',
                    'model' => 'LaserJet Pro M404dn',
                    'serial_number' => 'HPLJ-404-5829',
                    'department_id' => $hrDept->id,
                    'assigned_user_id' => null,
                    'location' => 'Admin Block, Room 102 Printing Area',
                    'ip_address' => '192.168.10.200',
                    'mac_address' => 'B4:2E:99:A1:C2:E4',
                    'status' => 'in_service',
                    'purchase_date' => '2023-08-10',
                    'warranty_expiry' => '2026-08-10',
                    'notes' => 'Network duplex monochrome laser printer',
                ]
            );
        }

        if ($finDept && $finStaff) {
            Asset::firstOrCreate(
                ['asset_tag' => 'AST-2026-003'],
                [
                    'asset_type' => 'Laptop',
                    'name' => 'Finance Accounts Laptop',
                    'manufacturer' => 'Lenovo',
                    'model' => 'ThinkPad T14 Gen 3',
                    'serial_number' => 'PF-289410',
                    'department_id' => $finDept->id,
                    'assigned_user_id' => $finStaff->id,
                    'location' => 'Admin Block, 2nd Floor, Room 210',
                    'ip_address' => '192.168.20.72',
                    'mac_address' => '48:2C:6A:11:F5:8B',
                    'status' => 'in_service',
                    'purchase_date' => '2024-01-20',
                    'warranty_expiry' => '2027-01-20',
                    'notes' => 'Core i5-1240P, 16GB RAM, 512GB SSD',
                ]
            );
        }

        if ($itDept) {
            Asset::firstOrCreate(
                ['asset_tag' => 'AST-2026-004'],
                [
                    'asset_type' => 'Network Switch',
                    'name' => 'Academic Block Core Distribution Switch',
                    'manufacturer' => 'Cisco',
                    'model' => 'Catalyst 2960-X 48-Port PoE',
                    'serial_number' => 'FOC2134S88A',
                    'department_id' => $itDept->id,
                    'assigned_user_id' => null,
                    'location' => 'Academic Block Server Room, Rack 2',
                    'ip_address' => '192.168.1.15',
                    'mac_address' => '00:27:0D:33:91:AA',
                    'status' => 'in_service',
                    'purchase_date' => '2022-06-01',
                    'warranty_expiry' => '2027-06-01',
                    'notes' => 'Gigabit PoE switch feeding academic workstations',
                ]
            );
        }

        // 6. Knowledge Articles
        $netCat = ComplaintCategory::where('slug', 'internet-connectivity')->first();
        $printCat = ComplaintCategory::where('slug', 'printer-and-scanner-issues')->first();

        if ($itHod) {
            KnowledgeArticle::firstOrCreate(
                ['slug' => 'how-to-reconnect-office-wifi'],
                [
                    'category_id' => $netCat?->id,
                    'author_id' => $itHod->id,
                    'title' => 'How to Reconnect to Office Wi-Fi and Clear IP Conflict',
                    'summary' => 'Quick step-by-step instructions to troubleshoot Wi-Fi connection errors and renew IP leases.',
                    'content' => "### Reconnecting to Office Wi-Fi\n\n1. Disconnect from the Wi-Fi network from your taskbar.\n2. Open Command Prompt and type `ipconfig /release` then press Enter.\n3. Type `ipconfig /renew` and wait 10 seconds.\n4. Reconnect to the network using your institutional credentials.\n\nIf you still receive no internet access, please lodge a ticket under **Wi-Fi Problems**.",
                    'keywords' => 'wifi, internet, ipconfig, network, slow',
                    'views_count' => 45,
                    'is_published' => true,
                ]
            );
        }

        if ($tech1) {
            KnowledgeArticle::firstOrCreate(
                ['slug' => 'troubleshooting-network-printers'],
                [
                    'category_id' => $printCat?->id,
                    'author_id' => $tech1->id,
                    'title' => 'Troubleshooting Network Printer Paper Jams & Offline Status',
                    'summary' => 'Standard procedure to clear print spooler queues and resolve printer offline messages.',
                    'content' => "### Printer Offline Solutions\n\n1. Verify the printer power switch is on and the network cable LED is blinking green.\n2. Open `Services.msc` on Windows, find **Print Spooler**, right-click and select **Restart**.\n3. Make sure paper tray 2 has paper properly aligned.\n\nIf the issue persists, submit a complaint and our technician will inspect the hardware.",
                    'keywords' => 'printer, offline, paper jam, spooler, hp',
                    'views_count' => 78,
                    'is_published' => true,
                ]
            );
        }

        // 7. System Settings
        SystemSetting::setValue('app_name', 'IT Helpdesk & Service Desk Portal', false, 'organization', 'Portal Name');
        SystemSetting::setValue('organization_name', 'University & Enterprise IT Services', false, 'organization', 'Organization Name');
        SystemSetting::setValue('ticket_prefix', 'ITHN', false, 'tickets', 'Tracking ID prefix');
        SystemSetting::setValue('smtp_enabled', false, false, 'email', 'Outbound SMTP enabled');
        SystemSetting::setValue('smtp_host', '127.0.0.1', false, 'email', 'SMTP Host');
        SystemSetting::setValue('smtp_port', 587, false, 'email', 'SMTP Port');
        SystemSetting::setValue('smtp_encryption', 'tls', false, 'email', 'SMTP Encryption');
        SystemSetting::setValue('smtp_from_address', 'it.helpdesk@org.local', false, 'email', 'From Email Address');
        SystemSetting::setValue('smtp_from_name', 'IT Helpdesk & Service Desk Portal', false, 'email', 'From Display Name');
    }
}
