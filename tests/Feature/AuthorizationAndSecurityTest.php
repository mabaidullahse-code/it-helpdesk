<?php

namespace Tests\Feature;

use App\Filament\Pages\EmailSettings;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\TicketPolicy;
use App\Policies\UserPolicy;
use App\Services\EmailSettingsService;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class AuthorizationAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;
    protected Department $hrDept;
    protected Department $finDept;
    protected User $superAdmin;
    protected User $hrHod;
    protected User $finHod;
    protected User $hrEmployee;
    protected User $finEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itDept = Department::create(['name' => 'IT Department', 'code' => 'IT']);
        $this->hrDept = Department::create(['name' => 'Human Resources', 'code' => 'HR']);
        $this->finDept = Department::create(['name' => 'Finance & Accounts', 'code' => 'FIN']);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@test.local',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'department_id' => $this->itDept->id,
        ]);

        $this->hrHod = User::create([
            'name' => 'HR HOD',
            'username' => 'hr_hod',
            'password' => bcrypt('password'),
            'role' => 'department_hod',
            'department_id' => $this->hrDept->id,
        ]);
        $this->hrDept->update(['hod_user_id' => $this->hrHod->id]);

        $this->finHod = User::create([
            'name' => 'Finance HOD',
            'username' => 'fin_hod',
            'password' => bcrypt('password'),
            'role' => 'department_hod',
            'department_id' => $this->finDept->id,
        ]);
        $this->finDept->update(['hod_user_id' => $this->finHod->id]);

        $this->hrEmployee = User::create([
            'name' => 'HR Staff',
            'username' => 'hr_staff',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'department_id' => $this->hrDept->id,
        ]);

        $this->finEmployee = User::create([
            'name' => 'Finance Staff',
            'username' => 'fin_staff',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'department_id' => $this->finDept->id,
        ]);
    }

    public function test_user_policy_restricts_departmental_hod_to_own_department(): void
    {
        $policy = new UserPolicy();

        // HR HOD can view employees in their own department
        $this->assertTrue($policy->view($this->hrHod, $this->hrEmployee));

        // HR HOD CANNOT view employees in Finance department
        $this->assertFalse($policy->view($this->hrHod, $this->finEmployee));

        // Super Admin can view all employees
        $this->assertTrue($policy->view($this->superAdmin, $this->hrEmployee));
        $this->assertTrue($policy->view($this->superAdmin, $this->finEmployee));
    }

    public function test_employee_cannot_view_another_employees_private_complaint(): void
    {
        $category = ComplaintCategory::create([
            'name' => 'General Issue',
            'slug' => 'general-issue',
            'is_active' => true,
        ]);

        $ticket = app(TicketWorkflowService::class)->createTicket([
            'title' => 'Confidential HR Issue',
            'description' => 'Workstation lock screen error',
            'category_id' => $category->id,
            'priority' => 'low',
        ], $this->hrEmployee);

        $ticketPolicy = new TicketPolicy();

        // The complainant can view their own complaint
        $this->assertTrue($ticketPolicy->view($this->hrEmployee, $ticket));

        // An employee from Finance cannot view this private complaint
        $this->assertFalse($ticketPolicy->view($this->finEmployee, $ticket));

        // Department HOD of HR can view it
        $this->assertTrue($ticketPolicy->view($this->hrHod, $ticket));

        // Department HOD of Finance CANNOT view HR's ticket
        $this->assertFalse($ticketPolicy->view($this->finHod, $ticket));

        // Super Admin can view all tickets
        $this->assertTrue($ticketPolicy->view($this->superAdmin, $ticket));
    }

    public function test_smtp_credentials_are_encrypted_at_rest(): void
    {
        $service = app(EmailSettingsService::class);
        $service->saveSettings([
            'is_enabled' => true,
            'host' => 'smtp.mailtrap.io',
            'port' => 2525,
            'encryption' => 'tls',
            'username' => 'smtp_user',
            'password' => 'super_secret_smtp_pass_123',
            'from_address' => 'helpdesk@org.com',
            'from_name' => 'Helpdesk Portal',
        ]);

        // Check raw database record
        $rawSetting = SystemSetting::where('key', 'smtp_password')->first();
        $this->assertNotNull($rawSetting);
        $this->assertTrue($rawSetting->is_encrypted);
        // Ensure raw stored value is encrypted and not plaintext
        $this->assertNotEquals('super_secret_smtp_pass_123', $rawSetting->value);
        // Ensure decrypted value matches original
        $this->assertEquals('super_secret_smtp_pass_123', Crypt::decryptString($rawSetting->value));
    }

    public function test_only_super_admin_can_access_email_settings(): void
    {
        $this->actingAs($this->superAdmin);
        $this->assertTrue(EmailSettings::canAccess());

        $this->actingAs($this->hrHod);
        $this->assertFalse(EmailSettings::canAccess());

        $this->actingAs($this->hrEmployee);
        $this->assertFalse(EmailSettings::canAccess());
    }

    public function test_empty_optional_fields_do_not_prevent_user_creation(): void
    {
        $user = User::create([
            'name' => 'Staff With Optional Fields Blank',
            'username' => 'staff_blank_optional',
            'email' => null,
            'phone' => null,
            'employee_id' => null,
            'department_id' => $this->hrDept->id,
            'role' => 'employee',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'staff_blank_optional',
            'email' => null,
            'phone' => null,
        ]);
    }

    public function test_super_admin_can_access_asset_edit_page(): void
    {
        $asset = \App\Models\Asset::create([
            'asset_tag' => 'AST-TEST-001',
            'asset_type' => 'Computer',
            'name' => 'Dell Optiplex 7090',
            'serial_number' => 'SN123456789',
            'department_id' => $this->itDept->id,
            'status' => 'in_service',
        ]);

        \Livewire\Livewire::actingAs($this->superAdmin)
            ->test(\App\Filament\Resources\Assets\Pages\EditAsset::class, [
                'record' => $asset->id,
            ])
            ->assertSuccessful();
    }

    public function test_it_hod_cannot_see_edit_action_on_complaint_categories(): void
    {
        $itHod = User::create([
            'name' => 'IT HOD User',
            'username' => 'it_hod_user',
            'password' => bcrypt('password'),
            'role' => 'department_hod',
            'department_id' => $this->itDept->id,
        ]);
        $this->itDept->update(['hod_user_id' => $itHod->id]);

        $category = ComplaintCategory::create([
            'name' => 'Network Issue',
            'slug' => 'network-issue',
            'default_priority' => 'high',
        ]);

        // IT HOD can view list, but MUST NOT see the Edit action
        \Livewire\Livewire::actingAs($itHod)
            ->test(\App\Filament\Resources\ComplaintCategories\Pages\ListComplaintCategories::class)
            ->assertTableActionHidden('edit', $category);

        // Super Admin MUST see the Edit action
        \Livewire\Livewire::actingAs($this->superAdmin)
            ->test(\App\Filament\Resources\ComplaintCategories\Pages\ListComplaintCategories::class)
            ->assertTableActionVisible('edit', $category);
    }

    public function test_it_hod_cannot_see_edit_action_on_sla_policies(): void
    {
        $itHod = User::create([
            'name' => 'IT HOD SLA',
            'username' => 'it_hod_sla',
            'password' => bcrypt('password'),
            'role' => 'department_hod',
            'department_id' => $this->itDept->id,
        ]);
        $this->itDept->update(['hod_user_id' => $itHod->id]);

        $policy = \App\Models\SlaPolicy::create([
            'name' => 'Critical Priority SLA',
            'priority' => 'critical',
            'response_time_hours' => 1,
            'resolution_time_hours' => 4,
            'is_active' => true,
        ]);

        // IT HOD can view list, but MUST NOT see the Edit action
        \Livewire\Livewire::actingAs($itHod)
            ->test(\App\Filament\Resources\SlaPolicies\Pages\ListSlaPolicies::class)
            ->assertTableActionHidden('edit', $policy);

        // Super Admin MUST see the Edit action
        \Livewire\Livewire::actingAs($this->superAdmin)
            ->test(\App\Filament\Resources\SlaPolicies\Pages\ListSlaPolicies::class)
            ->assertTableActionVisible('edit', $policy);
    }

    public function test_hod_can_only_view_their_own_user_and_departmental_staff(): void
    {
        // Add a super admin whose department happens to be HR
        $hrSuperAdmin = User::create([
            'name' => 'HR Attached Super Admin',
            'username' => 'hr_superadmin',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'department_id' => $this->hrDept->id,
        ]);

        $this->actingAs($this->hrHod);
        $visibleUsers = \App\Filament\Resources\Users\UserResource::getEloquentQuery()->get();

        // HR HOD must see themselves
        $this->assertTrue($visibleUsers->contains('id', $this->hrHod->id));

        // HR HOD must see their own departmental staff
        $this->assertTrue($visibleUsers->contains('id', $this->hrEmployee->id));

        // HR HOD must NOT see staff from another department
        $this->assertFalse($visibleUsers->contains('id', $this->finEmployee->id));
        $this->assertFalse($visibleUsers->contains('id', $this->finHod->id));

        // HR HOD must NOT see Super Admin even if in same department
        $this->assertFalse($visibleUsers->contains('id', $hrSuperAdmin->id));
        $this->assertFalse($visibleUsers->contains('id', $this->superAdmin->id));
    }

    public function test_user_creation_can_only_be_performed_by_admin_and_hod(): void
    {
        $this->actingAs($this->superAdmin);
        $this->assertTrue(\App\Filament\Resources\Users\UserResource::canCreate());

        $this->actingAs($this->hrHod);
        $this->assertTrue(\App\Filament\Resources\Users\UserResource::canCreate());

        $this->actingAs($this->hrEmployee);
        $this->assertFalse(\App\Filament\Resources\Users\UserResource::canCreate());

        $this->actingAs($this->finEmployee);
        $this->assertFalse(\App\Filament\Resources\Users\UserResource::canCreate());
    }

    public function test_department_hod_cannot_create_or_escalate_roles_beyond_staff(): void
    {
        \Livewire\Livewire::actingAs($this->hrHod)
            ->test(\App\Filament\Resources\Users\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'New HR Staff Member',
                'username' => 'new_hr_staff',
                'designation' => 'HR Assistant',
                'password' => 'secret123',
                'role' => 'employee',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $createdUser = User::where('username', 'new_hr_staff')->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals('employee', $createdUser->role);
        $this->assertEquals($this->hrDept->id, $createdUser->department_id);
    }

    public function test_department_hod_cannot_change_own_role(): void
    {
        \Livewire\Livewire::actingAs($this->hrHod)
            ->test(\App\Filament\Resources\Users\Pages\EditUser::class, ['record' => $this->hrHod->getRouteKey()])
            ->fillForm([
                'name' => 'HR HOD Updated Name',
                'role' => 'employee',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // The HOD's role must remain unchanged as department_hod
        $this->assertEquals('department_hod', $this->hrHod->fresh()->role);
        $this->assertEquals('HR HOD Updated Name', $this->hrHod->fresh()->name);
    }

    public function test_department_hod_cannot_create_hod_or_super_admin(): void
    {
        \Livewire\Livewire::actingAs($this->hrHod)
            ->test(\App\Filament\Resources\Users\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Attempted HR Co-HOD',
                'username' => 'attempted_hr_hod',
                'designation' => 'Assistant',
                'password' => 'secret123',
                'role' => 'department_hod',
            ])
            ->call('create')
            ->assertHasFormErrors(['role']);

        $this->assertNull(User::where('username', 'attempted_hr_hod')->first());
    }

    public function test_department_hod_cannot_escalate_to_super_admin(): void
    {
        \Livewire\Livewire::actingAs($this->hrHod)
            ->test(\App\Filament\Resources\Users\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Attempted Super Admin',
                'username' => 'fake_superadmin',
                'designation' => 'Hacker',
                'password' => 'secret123',
                'role' => 'super_admin',
            ])
            ->call('create');

        $createdUser = User::where('username', 'fake_superadmin')->first();
        if ($createdUser) {
            $this->assertNotEquals('super_admin', $createdUser->role);
        }
    }

    public function test_department_hod_cannot_deactivate_own_or_staff_account(): void
    {
        // 1. Cannot deactivate own account
        \Livewire\Livewire::actingAs($this->hrHod)
            ->test(\App\Filament\Resources\Users\Pages\EditUser::class, ['record' => $this->hrHod->getRouteKey()])
            ->fillForm([
                'is_active' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($this->hrHod->fresh()->is_active);

        // 2. Cannot deactivate staff account
        \Livewire\Livewire::actingAs($this->hrHod)
            ->test(\App\Filament\Resources\Users\Pages\EditUser::class, ['record' => $this->hrEmployee->getRouteKey()])
            ->fillForm([
                'is_active' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($this->hrEmployee->fresh()->is_active);
    }
}



