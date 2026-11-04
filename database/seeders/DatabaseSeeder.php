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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departments
        $itDept = Department::create([
            'name' => 'IT Hardware & Networking',
            'code' => 'IT',
            'description' => 'Responsible for IT infrastructure, networking, computer hardware, servers, and technical support.',
            'phone' => '+92-42-99231304',
            'email' => 'it.support@org.com',
            'location' => 'Main Administration Block, 1st Floor',
            'is_active' => true,
        ]);

        $hrDept = Department::create([
            'name' => 'Human Resources',
            'code' => 'HR',
            'description' => 'Employee relations, recruitment, attendance, and administrative affairs.',
            'phone' => '+92-42-99231305',
            'email' => 'hr@org.com',
            'location' => 'Administration Block, Ground Floor',
            'is_active' => true,
        ]);

        $finDept = Department::create([
            'name' => 'Finance & Accounts',
            'code' => 'FIN',
            'description' => 'Payroll, disbursements, budgeting, and financial records.',
            'phone' => '+92-42-99231306',
            'email' => 'finance@org.com',
            'location' => 'Administration Block, 2nd Floor',
            'is_active' => true,
        ]);

        $acadDept = Department::create([
            'name' => 'Academic & Examinations',
            'code' => 'ACAD',
            'description' => 'Curriculum, student evaluation, examination monitoring, and degrees.',
            'phone' => '+92-42-99231307',
            'email' => 'examinations@org.com',
            'location' => 'Examinations Building, Ground Floor',
            'is_active' => true,
        ]);

        // 2. Users
        $defaultPassword = Hash::make('password');

        // Super Administrator
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'admin@helpdesk.local',
            'phone' => '0300-1112233',
            'designation' => 'Director IT & Enterprise Systems',
            'employee_id' => 'EMP-0001',
            'department_id' => $itDept->id,
            'role' => 'super_admin',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);

        // IT HOD
        $itHod = User::create([
            'name' => 'Muhammad Tariq',
            'username' => 'ithod',
            'email' => 'ithod@helpdesk.local',
            'phone' => '0300-2223344',
            'designation' => 'Head of IT Hardware & Networking',
            'employee_id' => 'EMP-0100',
            'department_id' => $itDept->id,
            'role' => 'department_hod',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);
        $itDept->update(['hod_user_id' => $itHod->id]);

        // IT Technicians
        $tech1 = User::create([
            'name' => 'Ahmed Ali',
            'username' => 'tech_ahmed',
            'email' => 'ahmed.tech@helpdesk.local',
            'phone' => '0301-3334455',
            'designation' => 'Senior Network & Hardware Technician',
            'employee_id' => 'EMP-0101',
            'department_id' => $itDept->id,
            'role' => 'it_technician',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);

        $tech2 = User::create([
            'name' => 'Usman Farooq',
            'username' => 'tech_usman',
            'email' => 'usman.tech@helpdesk.local',
            'phone' => '0302-4445566',
            'designation' => 'Desktop Support Specialist',
            'employee_id' => 'EMP-0102',
            'department_id' => $itDept->id,
            'role' => 'it_technician',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);

        // HR Department Users
        $hrHod = User::create([
            'name' => 'Sara Khan',
            'username' => 'hr_hod',
            'email' => 'hrhod@helpdesk.local',
            'phone' => '0303-5556677',
            'designation' => 'Director Human Resources',
            'employee_id' => 'EMP-0200',
            'department_id' => $hrDept->id,
            'role' => 'department_hod',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);
        $hrDept->update(['hod_user_id' => $hrHod->id]);

        $hrStaff = User::create([
            'name' => 'Zainab Bibi',
            'username' => 'hr_staff',
            'email' => 'hrstaff@helpdesk.local',
            'phone' => '0304-6667788',
            'designation' => 'HR Operations Officer',
            'employee_id' => 'EMP-0201',
            'department_id' => $hrDept->id,
            'role' => 'employee',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);

        // Finance Department Users
        $finHod = User::create([
            'name' => 'Kamran Malik',
            'username' => 'fin_hod',
            'email' => 'finhod@helpdesk.local',
            'phone' => '0305-7778899',
            'designation' => 'Chief Financial Officer',
            'employee_id' => 'EMP-0300',
            'department_id' => $finDept->id,
            'role' => 'department_hod',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);
        $finDept->update(['hod_user_id' => $finHod->id]);

        $finStaff = User::create([
            'name' => 'Bilal Hassan',
            'username' => 'fin_staff',
            'email' => 'finstaff@helpdesk.local',
            'phone' => '0306-8889900',
            'designation' => 'Accounts Officer',
            'employee_id' => 'EMP-0301',
            'department_id' => $finDept->id,
            'role' => 'employee',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);

        // Academic Department Users
        $acadHod = User::create([
            'name' => 'Dr. Asim Qureshi',
            'username' => 'acad_hod',
            'email' => 'acadhod@helpdesk.local',
            'phone' => '0307-9990011',
            'designation' => 'Dean & Head of Academics',
            'employee_id' => 'EMP-0400',
            'department_id' => $acadDept->id,
            'role' => 'department_hod',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);
        $acadDept->update(['hod_user_id' => $acadHod->id]);

        $acadStaff = User::create([
            'name' => 'Fatima Noor',
            'username' => 'acad_staff',
            'email' => 'acadstaff@helpdesk.local',
            'phone' => '0308-0001122',
            'designation' => 'Assistant Registrar',
            'employee_id' => 'EMP-0401',
            'department_id' => $acadDept->id,
            'role' => 'employee',
            'is_active' => true,
            'password' => $defaultPassword,
        ]);

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
            $cat = ComplaintCategory::create([
                'name' => $catData['name'],
                'slug' => Str::slug($catData['name']),
                'description' => $catData['description'],
                'default_priority' => $catData['priority'],
                'sort_order' => $order++,
                'is_active' => true,
            ]);

            foreach ($catData['subcategories'] as $sub) {
                ComplaintSubcategory::create([
                    'category_id' => $cat->id,
                    'name' => $sub,
                    'is_active' => true,
                ]);
            }
        }

        // 4. SLA Policies
        SlaPolicy::create([
            'name' => 'Critical Priority Policy',
            'priority' => 'critical',
            'response_time_hours' => 1,
            'resolution_time_hours' => 4,
            'escalation_threshold_hours' => 3,
            'reminder_interval_hours' => 1,
            'description' => 'Organization-wide outages, core network switches down, or major infrastructure breakdown.',
            'is_active' => true,
        ]);

        SlaPolicy::create([
            'name' => 'High Priority Policy',
            'priority' => 'high',
            'response_time_hours' => 4,
            'resolution_time_hours' => 12,
            'escalation_threshold_hours' => 10,
            'reminder_interval_hours' => 2,
            'description' => 'Substantial departmental disruption, departmental internet down, or primary printer offline.',
            'is_active' => true,
        ]);

        SlaPolicy::create([
            'name' => 'Medium Priority Policy',
            'priority' => 'medium',
            'response_time_hours' => 8,
            'resolution_time_hours' => 24,
            'escalation_threshold_hours' => 20,
            'reminder_interval_hours' => 4,
            'description' => 'Issue affecting an individual user or minor departmental convenience.',
            'is_active' => true,
        ]);

        SlaPolicy::create([
            'name' => 'Low Priority Policy',
            'priority' => 'low',
            'response_time_hours' => 24,
            'resolution_time_hours' => 48,
            'escalation_threshold_hours' => 40,
            'reminder_interval_hours' => 8,
            'description' => 'Routine assistance, non-urgent software installations, or general queries.',
            'is_active' => true,
        ]);

        // 5. Assets Register
        Asset::create([
            'asset_tag' => 'AST-2026-001',
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
        ]);

        Asset::create([
            'asset_tag' => 'AST-2026-002',
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
        ]);

        Asset::create([
            'asset_tag' => 'AST-2026-003',
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
        ]);

        Asset::create([
            'asset_tag' => 'AST-2026-004',
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
        ]);

        // 6. Knowledge Articles
        $netCat = ComplaintCategory::where('slug', 'internet-connectivity')->first();
        $printCat = ComplaintCategory::where('slug', 'printer-and-scanner-issues')->first();

        KnowledgeArticle::create([
            'category_id' => $netCat?->id,
            'author_id' => $itHod->id,
            'title' => 'How to Reconnect to Office Wi-Fi and Clear IP Conflict',
            'slug' => 'how-to-reconnect-office-wifi',
            'summary' => 'Quick step-by-step instructions to troubleshoot Wi-Fi connection errors and renew IP leases.',
            'content' => "### Reconnecting to Office Wi-Fi\n\n1. Disconnect from the Wi-Fi network from your taskbar.\n2. Open Command Prompt and type `ipconfig /release` then press Enter.\n3. Type `ipconfig /renew` and wait 10 seconds.\n4. Reconnect to the network using your institutional credentials.\n\nIf you still receive no internet access, please lodge a ticket under **Wi-Fi Problems**.",
            'keywords' => 'wifi, internet, ipconfig, network, slow',
            'views_count' => 45,
            'is_published' => true,
        ]);

        KnowledgeArticle::create([
            'category_id' => $printCat?->id,
            'author_id' => $tech1->id,
            'title' => 'Troubleshooting Network Printer Paper Jams & Offline Status',
            'slug' => 'troubleshooting-network-printers',
            'summary' => 'Standard procedure to clear print spooler queues and resolve printer offline messages.',
            'content' => "### Printer Offline Solutions\n\n1. Verify the printer power switch is on and the network cable LED is blinking green.\n2. Open `Services.msc` on Windows, find **Print Spooler**, right-click and select **Restart**.\n3. Make sure paper tray 2 has paper properly aligned.\n\nIf the issue persists, submit a complaint and our technician will inspect the hardware.",
            'keywords' => 'printer, offline, paper jam, spooler, hp',
            'views_count' => 78,
            'is_published' => true,
        ]);

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
