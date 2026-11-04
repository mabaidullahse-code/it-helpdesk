<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Services\AuditLogService;
use App\Services\OtpService;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomPasswordReset extends SimplePage
{
    protected string $view = 'filament.pages.auth.custom-password-reset';

    public int $step = 1; // 1: Enter email/username, 2: Enter 4-digit OTP & new password

    public string $login = '';
    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        if (auth()->check()) {
            redirect()->intended(filament()->getUrl());
        }
    }

    public function getHeading(): string|Htmlable
    {
        return $this->step === 1 ? 'Forgot Password' : 'Reset Password with 4-Digit Code';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if ($this->step === 1) {
            return 'Enter your registered email address or username to receive a 4-digit reset code.';
        }

        return 'Enter the 4-digit code sent to your email (valid for 30 minutes) and your new password.';
    }

    public function sendResetOtp(): void
    {
        $this->validate([
            'login' => ['required', 'string'],
        ], [
            'login.required' => 'Please enter your username or registered email address.',
        ]);

        $search = trim($this->login);
        $user = User::where('email', $search)->orWhere('username', $search)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => 'No account found matching this email or username.',
            ]);
        }

        if (empty($user->email)) {
            throw ValidationException::withMessages([
                'login' => 'This account does not have a registered email address. Please contact the IT Directorate.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'This account has been deactivated. Please contact IT Administration.',
            ]);
        }

        $this->email = $user->email;

        // Generate 4-digit OTP expiring in 30 minutes
        app(OtpService::class)->generate($this->email, 'password_reset', [
            'user_id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
        ]);

        $this->step = 2;
        $this->otp = '';
        $this->password = '';
        $this->password_confirmation = '';

        Notification::make()
            ->title('4-Digit Code Sent')
            ->body("A 4-digit reset code has been sent to {$this->email}. Code expires in 30 minutes.")
            ->success()
            ->send();
    }

    public function resetPasswordWithOtp(): void
    {
        $this->validate([
            'otp' => ['required', 'digits:4'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'otp.digits' => 'Reset code must be exactly 4 digits.',
            'otp.required' => 'Please enter the 4-digit code sent to your email.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $otpRecord = app(OtpService::class)->verify($this->email, 'password_reset', $this->otp);

        if (!$otpRecord) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid or expired 4-digit code (valid for 30 minutes). Please check your email or click Resend.',
            ]);
        }

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => 'Unable to locate user account.',
            ]);
        }

        // Update password and plain_password
        $user->password = Hash::make($this->password);
        $user->plain_password = $this->password;
        $user->save();

        app(AuditLogService::class)->log(
            action: 'password_reset_via_otp',
            description: "User {$user->username} reset password using 4-digit OTP",
            userId: $user->id
        );

        Notification::make()
            ->title('Password Reset Successfully')
            ->body('Your password has been updated. Please sign in with your new credentials.')
            ->success()
            ->send();

        redirect()->to(filament()->getLoginUrl());
    }

    public function resendOtp(): void
    {
        if (empty($this->email)) {
            $this->step = 1;
            return;
        }

        $user = User::where('email', $this->email)->first();

        app(OtpService::class)->generate($this->email, 'password_reset', [
            'user_id' => $user?->id,
            'name' => $user?->name,
            'username' => $user?->username,
        ]);

        Notification::make()
            ->title('New Code Sent')
            ->body("A new 4-digit reset code has been sent to {$this->email}. Valid for 30 minutes.")
            ->info()
            ->send();
    }

    public function backToStepOne(): void
    {
        $this->step = 1;
        $this->otp = '';
    }
}

