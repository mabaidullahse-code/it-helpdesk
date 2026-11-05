<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class CustomLogin extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return 'Sign in to Portal';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Username or Email Address')
            ->placeholder('e.g. superadmin or name@uhs.edu.pk')
            ->required()
            ->autocomplete('username')
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        $resetUrl = filament()->getRequestPasswordResetUrl() ?? url('/admin/password-reset/request');

        return TextInput::make('password')
            ->label('Password')
            ->hint(new HtmlString('<a href="'.e($resetUrl).'" style="font-size: 0.75rem; color: #059669; font-weight: 600; text-decoration: none;">Forgot password?</a>'))
            ->password()
            ->revealable()
            ->required()
            ->autocomplete('current-password');
    }

    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        $login = $data['login'] ?? $data['email'] ?? '';
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $type => $login,
            'password' => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $login = trim($data['login'] ?? '');
        $password = $data['password'] ?? '';

        // Check if user exists by username or email
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if (! $user) {
            Notification::make()
                ->title('Account Not Found')
                ->body('No account found matching this username or email address.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'data.login' => 'No account found matching this username or email address.',
            ]);
        }

        if (! $user->is_active) {
            Notification::make()
                ->title('Account Deactivated')
                ->body('Your account has been deactivated. Please contact the IT Directorate.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'data.login' => 'This account has been deactivated. Please contact IT Administration.',
            ]);
        }

        $normalizedHash = preg_replace('/^\$2[abx]\$/', '$2y$', (string) $user->password);
        $passwordMatches = false;

        try {
            $passwordMatches = Hash::check($password, $normalizedHash) || password_verify($password, $user->password);
        } catch (\Throwable) {
            $passwordMatches = password_verify($password, $user->password);
        }

        if (! $passwordMatches) {
            Notification::make()
                ->title('Incorrect Password')
                ->body('The password you entered is incorrect. Please verify your password or use "Forgot password?".')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'data.password' => 'The password you entered is incorrect. Please try again.',
            ]);
        }

        // Seamlessly migrate legacy $2a/$2b or rehash if needed
        if (str_starts_with($user->password, '$2a$') || str_starts_with($user->password, '$2b$') || Hash::needsRehash($user->password)) {
            $user->forceFill([
                'password' => $password,
                'plain_password' => $password,
            ])->saveQuietly();
        }

        // Credentials valid, sign in
        Filament::auth()->login($user, (bool) ($data['remember'] ?? false));
        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.password' => 'The password you entered is incorrect. Please try again.',
        ]);
    }
}
