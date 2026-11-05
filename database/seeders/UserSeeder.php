<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the user database seeds.
     */
    public function run(): void
    {
        // 1. Ensure all core departments exist
        $departments = [
            'IT' => Department::firstOrCreate(
                ['code' => 'IT'],
                [
                    'name' => 'IT Hardware & Networking',
                    'description' => 'Responsible for IT infrastructure, networking, computer hardware, servers, and technical support.',
                    'phone' => '+92-42-99231304',
                    'email' => 'it.support@helpdesk.local',
                    'location' => 'Main Administration Block, 1st Floor',
                    'is_active' => true,
                ]
            ),
            'HR' => Department::firstOrCreate(
                ['code' => 'HR'],
                [
                    'name' => 'Human Resources',
                    'description' => 'Employee relations, recruitment, attendance, and administrative affairs.',
                    'phone' => '+92-42-99231305',
                    'email' => 'hr@helpdesk.local',
                    'location' => 'Administration Block, Ground Floor',
                    'is_active' => true,
                ]
            ),
            'FIN' => Department::firstOrCreate(
                ['code' => 'FIN'],
                [
                    'name' => 'Finance & Accounts',
                    'description' => 'Payroll, disbursements, budgeting, and financial records.',
                    'phone' => '+92-42-99231306',
                    'email' => 'finance@helpdesk.local',
                    'location' => 'Administration Block, 2nd Floor',
                    'is_active' => true,
                ]
            ),
            'ACAD' => Department::firstOrCreate(
                ['code' => 'ACAD'],
                [
                    'name' => 'Academic & Examinations',
                    'description' => 'Curriculum, student evaluation, examination monitoring, and degrees.',
                    'phone' => '+92-42-99231307',
                    'email' => 'examinations@helpdesk.local',
                    'location' => 'Examinations Building, Ground Floor',
                    'is_active' => true,
                ]
            ),
            'PROC' => Department::firstOrCreate(
                ['code' => 'PROC'],
                [
                    'name' => 'Procurement & Stores',
                    'description' => 'Purchase of hardware, software, office supplies, and inventory management.',
                    'phone' => '+92-42-99231308',
                    'email' => 'procurement@helpdesk.local',
                    'location' => 'Stores Building, Ground Floor',
                    'is_active' => true,
                ]
            ),
            'PND' => Department::firstOrCreate(
                ['code' => 'PND'],
                [
                    'name' => 'Planning & Development',
                    'description' => 'University project planning, physical infrastructure, and expansion schemes.',
                    'phone' => '+92-42-99231309',
                    'email' => 'pnd@helpdesk.local',
                    'location' => 'Administration Block, 3rd Floor',
                    'is_active' => true,
                ]
            ),
            'ADM' => Department::firstOrCreate(
                ['code' => 'ADM'],
                [
                    'name' => 'Admissions & Student Affairs',
                    'description' => 'Student enrollment, merit lists, quota admissions, and student verification.',
                    'phone' => '+92-42-99231310',
                    'email' => 'admissions@helpdesk.local',
                    'location' => 'Student Service Centre',
                    'is_active' => true,
                ]
            ),
            'QEC' => Department::firstOrCreate(
                ['code' => 'QEC'],
                [
                    'name' => 'Quality Enhancement Cell',
                    'description' => 'Institutional quality assurance, academic audits, and accreditation.',
                    'phone' => '+92-42-99231311',
                    'email' => 'qec@helpdesk.local',
                    'location' => 'Academic Block, 2nd Floor',
                    'is_active' => true,
                ]
            ),
        ];

        // 2. Comprehensive Users Matrix
        $users = [
            // Super Administrators
            [
                'username' => 'superadmin',
                'name' => 'Super Administrator',
                'email' => 'admin@helpdesk.local',
                'phone' => '0300-1112233',
                'designation' => 'Director IT & Enterprise Systems',
                'employee_id' => 'EMP-0001',
                'department_code' => 'IT',
                'role' => 'super_admin',
                'password' => 'password',
            ],

            // IT Department HOD & Technicians
            [
                'username' => 'ithod',
                'name' => 'Muhammad Tariq',
                'email' => 'ithod@helpdesk.local',
                'phone' => '0300-2223344',
                'designation' => 'Head of IT Hardware & Networking',
                'employee_id' => 'EMP-0100',
                'department_code' => 'IT',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'IT',
            ],
            [
                'username' => 'tech_ahmed',
                'name' => 'Ahmed Ali',
                'email' => 'ahmed.tech@helpdesk.local',
                'phone' => '0301-3334455',
                'designation' => 'Senior Network & Hardware Technician',
                'employee_id' => 'EMP-0101',
                'department_code' => 'IT',
                'role' => 'it_technician',
                'password' => 'password',
            ],
            [
                'username' => 'tech_usman',
                'name' => 'Usman Farooq',
                'email' => 'usman.tech@helpdesk.local',
                'phone' => '0302-4445566',
                'designation' => 'Desktop Support Specialist',
                'employee_id' => 'EMP-0102',
                'department_code' => 'IT',
                'role' => 'it_technician',
                'password' => 'password',
            ],
            [
                'username' => 'tech_bilal',
                'name' => 'Bilal Arshad',
                'email' => 'bilal.tech@helpdesk.local',
                'phone' => '0303-1122334',
                'designation' => 'Senior Systems & Server Administrator',
                'employee_id' => 'EMP-0103',
                'department_code' => 'IT',
                'role' => 'it_technician',
                'password' => 'password',
            ],
            [
                'username' => 'tech_hamza',
                'name' => 'Hamza Saeed',
                'email' => 'hamza.tech@helpdesk.local',
                'phone' => '0304-2233445',
                'designation' => 'IT Helpdesk Support Specialist',
                'employee_id' => 'EMP-0104',
                'department_code' => 'IT',
                'role' => 'it_technician',
                'password' => 'password',
            ],
            [
                'username' => 'tech_zain',
                'name' => 'Zain ul Abideen',
                'email' => 'zain.tech@helpdesk.local',
                'phone' => '0305-3344556',
                'designation' => 'Fiber & Network Cabling Engineer',
                'employee_id' => 'EMP-0105',
                'department_code' => 'IT',
                'role' => 'it_technician',
                'password' => 'password',
            ],

            // Human Resources
            [
                'username' => 'hr_hod',
                'name' => 'Sara Khan',
                'email' => 'hrhod@helpdesk.local',
                'phone' => '0303-5556677',
                'designation' => 'Director Human Resources',
                'employee_id' => 'EMP-0200',
                'department_code' => 'HR',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'HR',
            ],
            [
                'username' => 'hr_staff',
                'name' => 'Zainab Bibi',
                'email' => 'hrstaff@helpdesk.local',
                'phone' => '0304-6667788',
                'designation' => 'HR Operations Officer',
                'employee_id' => 'EMP-0201',
                'department_code' => 'HR',
                'role' => 'employee',
                'password' => 'password',
            ],
            [
                'username' => 'hr_officer',
                'name' => 'Rashid Mehmood',
                'email' => 'rashid.hr@helpdesk.local',
                'phone' => '0306-7788990',
                'designation' => 'Senior Recruitment Specialist',
                'employee_id' => 'EMP-0202',
                'department_code' => 'HR',
                'role' => 'employee',
                'password' => 'password',
            ],

            // Finance & Accounts
            [
                'username' => 'fin_hod',
                'name' => 'Kamran Malik',
                'email' => 'finhod@helpdesk.local',
                'phone' => '0305-7778899',
                'designation' => 'Chief Financial Officer',
                'employee_id' => 'EMP-0300',
                'department_code' => 'FIN',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'FIN',
            ],
            [
                'username' => 'fin_staff',
                'name' => 'Bilal Hassan',
                'email' => 'finstaff@helpdesk.local',
                'phone' => '0306-8889900',
                'designation' => 'Accounts Officer',
                'employee_id' => 'EMP-0301',
                'department_code' => 'FIN',
                'role' => 'employee',
                'password' => 'password',
            ],
            [
                'username' => 'fin_cashier',
                'name' => 'Asad Siddiqui',
                'email' => 'asad.fin@helpdesk.local',
                'phone' => '0307-1122339',
                'designation' => 'Senior Cashier & Accounts Assistant',
                'employee_id' => 'EMP-0302',
                'department_code' => 'FIN',
                'role' => 'employee',
                'password' => 'password',
            ],

            // Academic & Examinations
            [
                'username' => 'acad_hod',
                'name' => 'Dr. Asim Qureshi',
                'email' => 'acadhod@helpdesk.local',
                'phone' => '0307-9990011',
                'designation' => 'Dean & Head of Academics',
                'employee_id' => 'EMP-0400',
                'department_code' => 'ACAD',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'ACAD',
            ],
            [
                'username' => 'acad_staff',
                'name' => 'Fatima Noor',
                'email' => 'acadstaff@helpdesk.local',
                'phone' => '0308-0001122',
                'designation' => 'Assistant Registrar',
                'employee_id' => 'EMP-0401',
                'department_code' => 'ACAD',
                'role' => 'employee',
                'password' => 'password',
            ],
            [
                'username' => 'acad_coordinator',
                'name' => 'Shahid Imran',
                'email' => 'shahid.acad@helpdesk.local',
                'phone' => '0309-2233440',
                'designation' => 'Examination Hall Superintendent',
                'employee_id' => 'EMP-0402',
                'department_code' => 'ACAD',
                'role' => 'employee',
                'password' => 'password',
            ],

            // Procurement & Stores
            [
                'username' => 'proc_hod',
                'name' => 'Engr. Faisal Naveed',
                'email' => 'prochod@helpdesk.local',
                'phone' => '0310-3344551',
                'designation' => 'Director Procurement & Stores',
                'employee_id' => 'EMP-0500',
                'department_code' => 'PROC',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'PROC',
            ],
            [
                'username' => 'proc_staff',
                'name' => 'Mian Waqas',
                'email' => 'procstaff@helpdesk.local',
                'phone' => '0311-4455662',
                'designation' => 'Store & Inventory Officer',
                'employee_id' => 'EMP-0501',
                'department_code' => 'PROC',
                'role' => 'employee',
                'password' => 'password',
            ],

            // Planning & Development
            [
                'username' => 'pnd_hod',
                'name' => 'Dr. Rizwan Bashir',
                'email' => 'pndhod@helpdesk.local',
                'phone' => '0312-5566773',
                'designation' => 'Director Planning & Development',
                'employee_id' => 'EMP-0600',
                'department_code' => 'PND',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'PND',
            ],
            [
                'username' => 'pnd_staff',
                'name' => 'Hina Altaf',
                'email' => 'pndstaff@helpdesk.local',
                'phone' => '0313-6677884',
                'designation' => 'Planning & Monitoring Officer',
                'employee_id' => 'EMP-0601',
                'department_code' => 'PND',
                'role' => 'employee',
                'password' => 'password',
            ],

            // Admissions & Student Affairs
            [
                'username' => 'adm_hod',
                'name' => 'Prof. Dr. Tahir Mahmood',
                'email' => 'admhod@helpdesk.local',
                'phone' => '0314-7788995',
                'designation' => 'Chairman Admissions Committee',
                'employee_id' => 'EMP-0700',
                'department_code' => 'ADM',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'ADM',
            ],
            [
                'username' => 'adm_staff',
                'name' => 'Khadija Rehman',
                'email' => 'admstaff@helpdesk.local',
                'phone' => '0315-8899006',
                'designation' => 'Admissions & Merit Verification Officer',
                'employee_id' => 'EMP-0701',
                'department_code' => 'ADM',
                'role' => 'employee',
                'password' => 'password',
            ],

            // Quality Enhancement Cell
            [
                'username' => 'qec_hod',
                'name' => 'Dr. Naila Jamil',
                'email' => 'qechod@helpdesk.local',
                'phone' => '0316-9900117',
                'designation' => 'Director Quality Enhancement Cell',
                'employee_id' => 'EMP-0800',
                'department_code' => 'QEC',
                'role' => 'department_hod',
                'password' => 'password',
                'is_hod_of' => 'QEC',
            ],
            [
                'username' => 'qec_staff',
                'name' => 'Omer Farooq',
                'email' => 'qecstaff@helpdesk.local',
                'phone' => '0317-0011228',
                'designation' => 'Quality Assurance & Data Analyst',
                'employee_id' => 'EMP-0801',
                'department_code' => 'QEC',
                'role' => 'employee',
                'password' => 'password',
            ],
        ];

        foreach ($users as $data) {
            $dept = $departments[$data['department_code']] ?? null;
            $hodOf = $data['is_hod_of'] ?? null;
            unset($data['department_code'], $data['is_hod_of']);

            $user = User::updateOrCreate(
                ['username' => $data['username']],
                array_merge($data, [
                    'department_id' => $dept?->id,
                    'is_active' => true,
                    'plain_password' => $data['password'],
                    // Password will be hashed with native $2y$ Bcrypt via User's 'hashed' cast
                    'password' => $data['password'],
                ])
            );

            // Assign HOD to department if marked
            if ($hodOf && isset($departments[$hodOf])) {
                $departments[$hodOf]->update(['hod_user_id' => $user->id]);
            }
        }
    }
}

