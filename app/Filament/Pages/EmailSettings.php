<?php

namespace App\Filament\Pages;

use App\Services\AuditLogService;
use App\Services\EmailSettingsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class EmailSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static UnitEnum|string|null $navigationGroup = 'Administration & Organization';

    protected static ?string $navigationLabel = 'SMTP & Email Settings';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.email-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public function mount(): void
    {
        $settings = app(EmailSettingsService::class)->getSettings();
        // Mask the password for security
        $maskedPassword = !empty($settings['password']) ? '••••••••••••' : '';

        $this->form->fill([
            'is_enabled' => $settings['is_enabled'],
            'host' => $settings['host'],
            'port' => $settings['port'],
            'encryption' => $settings['encryption'],
            'username' => $settings['username'],
            'password' => $maskedPassword,
            'from_address' => $settings['from_address'],
            'from_name' => $settings['from_name'],
            'reply_to' => $settings['reply_to'],
            'timeout' => $settings['timeout'],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SMTP Server Configuration')
                    ->description('Configure institutional mail server or Google Workspace SMTP relay. Secrets are securely encrypted at rest.')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Enable Outbound Email Notifications')
                            ->helperText('When enabled, automatic notifications will be delivered via SMTP.')
                            ->default(false),

                        Grid::make(2)->schema([
                            TextInput::make('host')
                                ->label('SMTP Host')
                                ->placeholder('smtp.gmail.com or mail.org.local')
                                ->required(),
                            TextInput::make('port')
                                ->label('SMTP Port')
                                ->numeric()
                                ->default(587)
                                ->required(),
                            Select::make('encryption')
                                ->label('Encryption Protocol')
                                ->options([
                                    'tls' => 'TLS (Recommended)',
                                    'ssl' => 'SSL',
                                    'none' => 'None (Insecure / Internal Relay)',
                                ])
                                ->default('tls')
                                ->required(),
                            TextInput::make('timeout')
                                ->label('Connection Timeout (Seconds)')
                                ->numeric()
                                ->default(30),
                        ]),
                    ]),

                Section::make('Authentication Credentials & Sender Identity')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('username')
                                ->label('SMTP Username / Email')
                                ->placeholder('notifications@org.com')
                                ->autocomplete('off'),
                            TextInput::make('password')
                                ->label('SMTP Password / App Password')
                                ->password()
                                ->revealable()
                                ->placeholder('Enter new password to update')
                                ->helperText('Passwords are encrypted at rest using Laravel Crypt. Plaintext is never revealed.')
                                ->autocomplete('new-password'),
                            TextInput::make('from_address')
                                ->label('Sender Email Address (From Address)')
                                ->email()
                                ->required()
                                ->placeholder('it.support@org.com'),
                            TextInput::make('from_name')
                                ->label('Sender Display Name')
                                ->required()
                                ->placeholder('IT Helpdesk & Service Desk Portal'),
                            TextInput::make('reply_to')
                                ->label('Reply-To Address (Optional)')
                                ->email()
                                ->placeholder('it.helpdesk@org.com'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // Do not overwrite existing password with mask
        if (($state['password'] ?? '') === '••••••••••••') {
            unset($state['password']);
        }

        app(EmailSettingsService::class)->saveSettings($state);

        app(AuditLogService::class)->log(
            action: 'updated_smtp_settings',
            description: 'Updated SMTP and email notification configuration'
        );

        Notification::make()
            ->title('Settings Saved')
            ->body('SMTP email configuration successfully updated.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_test_email')
                ->label('Send Test Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->form([
                    TextInput::make('test_recipient')
                        ->label('Recipient Email Address')
                        ->email()
                        ->required()
                        ->default(auth()->user()?->email)
                        ->placeholder('recipient@org.com'),
                ])
                ->action(function (array $data) {
                    $result = app(EmailSettingsService::class)->sendTestEmail($data['test_recipient']);

                    if ($result['success']) {
                        Notification::make()
                            ->title('SMTP Test Succeeded')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('SMTP Test Failed')
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
