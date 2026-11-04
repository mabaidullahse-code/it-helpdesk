<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Throwable;

class EmailSettingsService
{
    public function getSettings(): array
    {
        return [
            'is_enabled' => (bool) SystemSetting::getValue('smtp_enabled', false),
            'host' => SystemSetting::getValue('smtp_host', config('mail.mailers.smtp.host', '127.0.0.1')),
            'port' => (int) SystemSetting::getValue('smtp_port', config('mail.mailers.smtp.port', 587)),
            'encryption' => SystemSetting::getValue('smtp_encryption', config('mail.mailers.smtp.encryption', 'tls')),
            'username' => SystemSetting::getValue('smtp_username', config('mail.mailers.smtp.username', '')),
            'password' => SystemSetting::getValue('smtp_password', ''), // decrypted automatically by model
            'from_address' => SystemSetting::getValue('smtp_from_address', config('mail.from.address', 'support@org.com')),
            'from_name' => SystemSetting::getValue('smtp_from_name', config('mail.from.name', 'IT Helpdesk & Service Desk')),
            'reply_to' => SystemSetting::getValue('smtp_reply_to', ''),
            'timeout' => (int) SystemSetting::getValue('smtp_timeout', 30),
        ];
    }

    public function saveSettings(array $data): void
    {
        if (isset($data['is_enabled'])) {
            SystemSetting::setValue('smtp_enabled', (bool) $data['is_enabled'], false, 'email', 'Enable outbound email notifications');
        }

        if (isset($data['host'])) {
            SystemSetting::setValue('smtp_host', $data['host'], false, 'email', 'SMTP Server Host');
        }

        if (isset($data['port'])) {
            SystemSetting::setValue('smtp_port', (int) $data['port'], false, 'email', 'SMTP Port');
        }

        if (isset($data['encryption'])) {
            SystemSetting::setValue('smtp_encryption', $data['encryption'], false, 'email', 'SMTP Encryption (tls, ssl, null)');
        }

        if (isset($data['username'])) {
            SystemSetting::setValue('smtp_username', $data['username'], false, 'email', 'SMTP Username');
        }

        // Only update password if a new non-empty password is provided
        if (!empty($data['password'])) {
            SystemSetting::setValue('smtp_password', $data['password'], true, 'email', 'Encrypted SMTP Password');
        }

        if (isset($data['from_address'])) {
            SystemSetting::setValue('smtp_from_address', $data['from_address'], false, 'email', 'From Address');
        }

        if (isset($data['from_name'])) {
            SystemSetting::setValue('smtp_from_name', $data['from_name'], false, 'email', 'From Name');
        }

        if (isset($data['reply_to'])) {
            SystemSetting::setValue('smtp_reply_to', $data['reply_to'], false, 'email', 'Reply-To Address');
        }

        if (isset($data['timeout'])) {
            SystemSetting::setValue('smtp_timeout', (int) $data['timeout'], false, 'email', 'Connection Timeout');
        }

        $this->applyRuntimeConfiguration();
    }

    public function applyRuntimeConfiguration(): void
    {
        $settings = $this->getSettings();

        if (!$settings['is_enabled']) {
            Config::set('mail.default', 'log');
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $settings['host']);
        Config::set('mail.mailers.smtp.port', $settings['port']);
        Config::set('mail.mailers.smtp.encryption', $settings['encryption'] === 'none' ? null : $settings['encryption']);
        Config::set('mail.mailers.smtp.username', $settings['username']);
        Config::set('mail.mailers.smtp.password', $settings['password']);
        Config::set('mail.mailers.smtp.timeout', $settings['timeout']);
        Config::set('mail.from.address', $settings['from_address']);
        Config::set('mail.from.name', $settings['from_name']);
    }

    public function sendTestEmail(string $recipientEmail): array
    {
        $this->applyRuntimeConfiguration();

        try {
            Mail::raw('This is a test notification from the IT Helpdesk and E-Ticketing System. Your SMTP configuration is working properly.', function ($message) use ($recipientEmail) {
                $message->to($recipientEmail)
                    ->subject('IT Helpdesk - SMTP Test Successful');
            });

            return [
                'success' => true,
                'message' => "Test email sent successfully to {$recipientEmail}.",
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ];
        }
    }
}
