<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Pages\RegistrationSettings;
use App\Models\Department;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\OtpService;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomRegister extends SimplePage
{
    protected string $view = 'filament.pages.auth.custom-register';

    public int $step = 1; // 1: Registration Form, 2: 4-digit OTP Verification

    // Form inputs
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public ?int $department_id = null;
    public string $designation = '';
    public string $employee_id = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Step 2 OTP input
    public string $otp = '';

    public function mount(): void
    {
        if (auth()->check()) {
            redirect()->intended(filament()->getUrl());
        }

        if (!RegistrationSettings::isRegistrationEnabled()) {
            Notification::make()
                ->title('Registration Closed')
                ->body('Staff self-registration is currently disabled. Please contact the Directorate of IT.')
                ->warning()
                ->send();
        }
    }

    public function getHeading(): string|Htmlable
    {
        return $this->step === 1 ? 'Staff Account Registration' : 'Verify Email with 4-Digit Code';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if ($this->step === 1) {
            return 'Enter your institutional credentials to create your employee helpdesk account.';
        }

        return 'A 4-digit verification code has been sent to your institutional email (valid for 30 minutes).';
    }

    public function sendRegistrationOtp(): void
    {
        if (!RegistrationSettings::isRegistrationEnabled()) {
            Notification::make()
                ->title('Registration Disabled')
                ->body('Staff registration is currently closed. Please contact IT Administration.')
                ->danger()
                ->send();
            return;
        }

        $this->email = strtolower(trim($this->email));
        $this->username = strtolower(trim($this->username));

        // 1. Validate form fields
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'designation' => ['required', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.unique' => 'This username is already taken. Please choose another.',
            'email.unique' => 'An account with this email address already exists. Please log in or use forgot password.',
            'employee_id.unique' => 'An account with this Staff / Employee ID already exists.',
            'department_id.required' => 'Please select your department.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        // 2. Validate email domain against allowed domains (default uhs.edu.pk)
        if (!RegistrationSettings::isEmailDomainAllowed($this->email)) {
            $allowedList = implode(', ', RegistrationSettings::getAllowedDomains());
            throw ValidationException::withMessages([
                'email' => "Registration is restricted to authorized institutional email domains ({$allowedList}). If you need access, please contact the IT Directorate.",
            ]);
        }

        // 3. Prepare payload for OTP storage
        $payload = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'department_id' => $this->department_id,
            'designation' => $this->designation,
            'employee_id' => filled($this->employee_id) ? $this->employee_id : null,
            'phone' => filled($this->phone) ? $this->phone : null,
            'password_hash' => Hash::make($this->password),
            'plain_password' => $this->password,
        ];

        // 4. Generate 4-digit OTP expiring in 30 minutes
        app(OtpService::class)->generate($this->email, 'registration', $payload);

        $this->step = 2;
        $this->otp = '';

        Notification::make()
            ->title('Verification Code Sent')
            ->body("A 4-digit verification code has been dispatched to {$this->email}. Code expires in 30 minutes.")
            ->success()
            ->send();
    }

    public function verifyAndRegister(): void
    {
        $this->validate([
            'otp' => ['required', 'digits:4'],
        ], [
            'otp.digits' => 'Verification code must be exactly 4 digits.',
            'otp.required' => 'Please enter the 4-digit verification code.',
        ]);

        $otpRecord = app(OtpService::class)->verify($this->email, 'registration', $this->otp);

        if (!$otpRecord) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid or expired verification code (valid for 30 minutes). Please verify the code or click Resend.',
            ]);
        }

        $payload = $otpRecord->payload;

        if (!$payload || empty($payload['username']) || empty($payload['email'])) {
            Notification::make()
                ->title('Registration Error')
                ->body('Registration session expired. Please fill out the registration form again.')
                ->danger()
                ->send();
            $this->step = 1;
            return;
        }

        // Create the user in database
        $user = User::create([
            'name' => $payload['name'],
            'username' => $payload['username'],
            'email' => $payload['email'],
            'department_id' => $payload['department_id'],
            'designation' => $payload['designation'],
            'employee_id' => $payload['employee_id'] ?? null,
            'phone' => $payload['phone'] ?? null,
            'password' => $payload['password_hash'],
            'plain_password' => $payload['plain_password'],
            'role' => 'employee',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        app(AuditLogService::class)->log(
            action: 'staff_self_registered',
            description: "Staff self-registered account: {$user->name} ({$user->username}) with email {$user->email}",
            userId: $user->id
        );

        auth()->login($user);
        session()->regenerate();

        Notification::make()
            ->title('Registration Successful!')
            ->body("Welcome to UHS IT Portal, {$user->name}. Your account is verified and ready.")
            ->success()
            ->send();

        redirect()->intended(filament()->getUrl());
    }

    public function resendOtp(): void
    {
        if (empty($this->email)) {
            $this->step = 1;
            return;
        }

        $payload = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'department_id' => $this->department_id,
            'designation' => $this->designation,
            'employee_id' => filled($this->employee_id) ? $this->employee_id : null,
            'phone' => filled($this->phone) ? $this->phone : null,
            'password_hash' => Hash::make($this->password),
            'plain_password' => $this->password,
        ];

        app(OtpService::class)->generate($this->email, 'registration', $payload);

        Notification::make()
            ->title('New Code Sent')
            ->body("A new 4-digit verification code has been dispatched to {$this->email}. Valid for 30 minutes.")
            ->info()
            ->send();
    }

    public function backToStepOne(): void
    {
        $this->step = 1;
        $this->otp = '';
    }
}

