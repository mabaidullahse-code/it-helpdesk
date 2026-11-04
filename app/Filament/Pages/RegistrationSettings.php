<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use App\Services\AuditLogService;
use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class RegistrationSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static UnitEnum|string|null $navigationGroup = 'Administration & Organization';

    protected static ?string $navigationLabel = 'Registration & Domain Settings';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.registration-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public function mount(): void
    {
        $allowed = static::getAllowedDomains();

        $this->form->fill([
            'allow_registration' => (bool) SystemSetting::getValue('allow_registration', true),
            'allowed_domains' => $allowed,
            'default_role' => SystemSetting::getValue('registration_default_role', 'employee'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Staff Self-Registration Access')
                    ->description('Control whether University staff and employees can register their own accounts.')
                    ->schema([
                        Toggle::make('allow_registration')
                            ->label('Enable Staff Self-Registration')
                            ->helperText('When enabled, the Register button is shown on the login page and staff can create accounts.')
                            ->default(true),
                    ]),

                Section::make('Authorized Institutional Email Domains')
                    ->description('Specify which email domains are permitted to register. By default, users with @uhs.edu.pk can register.')
                    ->schema([
                        TagsInput::make('allowed_domains')
                            ->label('Allowed Email Domains')
                            ->placeholder('Type domain and press Enter (e.g. uhs.edu.pk)')
                            ->helperText('Only users whose email ends with one of these domains will be permitted to register. Default domain is uhs.edu.pk.')
                            ->default(['uhs.edu.pk'])
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        SystemSetting::setValue('allow_registration', (bool) ($state['allow_registration'] ?? true), false, 'registration', 'Enable staff self-registration');

        $domains = $state['allowed_domains'] ?? ['uhs.edu.pk'];
        if (empty($domains)) {
            $domains = ['uhs.edu.pk'];
        }
        SystemSetting::setValue('allowed_registration_domains', json_encode(array_values($domains)), false, 'registration', 'Authorized email domains for registration');

        app(AuditLogService::class)->log(
            action: 'updated_registration_settings',
            description: 'Updated staff self-registration allowed domains and policies'
        );

        Notification::make()
            ->title('Registration Settings Saved')
            ->body('Allowed registration domains have been successfully updated.')
            ->success()
            ->send();
    }

    /**
     * Retrieve list of authorized registration domains.
     * Default is ['uhs.edu.pk'].
     *
     * @return array<string>
     */
    public static function getAllowedDomains(): array
    {
        $val = SystemSetting::getValue('allowed_registration_domains');

        if (empty($val)) {
            return ['uhs.edu.pk'];
        }

        if (is_array($val)) {
            return array_values(array_filter(array_map('strtolower', array_map('trim', $val))));
        }

        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map('strtolower', array_map('trim', $decoded))));
        }

        return array_values(array_filter(array_map('strtolower', array_map('trim', explode(',', $val)))));
    }

    /**
     * Check if a given email belongs to an authorized domain.
     */
    public static function isEmailDomainAllowed(string $email): bool
    {
        $email = strtolower(trim($email));
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }

        $domain = strtolower(trim($parts[1]));
        $allowed = static::getAllowedDomains();

        if (empty($allowed)) {
            $allowed = ['uhs.edu.pk'];
        }

        foreach ($allowed as $item) {
            $cleanItem = ltrim(strtolower(trim($item)), '@');
            if ($domain === $cleanItem || str_ends_with($domain, '.' . $cleanItem)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if registration is globally enabled.
     */
    public static function isRegistrationEnabled(): bool
    {
        return (bool) SystemSetting::getValue('allow_registration', true);
    }
}

