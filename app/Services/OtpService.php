<?php

namespace App\Services;

use App\Models\EmailOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OtpService
{
    /**
     * Generate a 4-digit OTP expiring in 30 minutes.
     */
    public function generate(string $email, string $purpose, ?array $payload = null): EmailOtp
    {
        $normalizedEmail = strtolower(trim($email));

        // Invalidate old active OTPs for this email and purpose
        EmailOtp::where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->delete();

        // 4 digits only (1000 - 9999)
        $code = (string) random_int(1000, 9999);

        $otp = EmailOtp::create([
            'email' => $normalizedEmail,
            'otp' => $code,
            'purpose' => $purpose,
            'payload' => $payload,
            'expires_at' => now()->addMinutes(30),
            'is_used' => false,
        ]);

        // Always log OTP for development / backup tracking
        Log::info("UHS IT Portal OTP generated for {$normalizedEmail} [{$purpose}]: {$code} (expires in 30 mins)");

        // Send via email
        $this->sendOtpMail($normalizedEmail, $code, $purpose, $payload['name'] ?? null);

        return $otp;
    }

    /**
     * Verify a 4-digit OTP.
     */
    public function verify(string $email, string $purpose, string $otp): ?EmailOtp
    {
        $normalizedEmail = strtolower(trim($email));
        $trimmedOtp = trim($otp);

        $record = EmailOtp::where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->where('otp', $trimmedOtp)
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if ($record) {
            $record->update(['is_used' => true]);
            return $record;
        }

        return null;
    }

    /**
     * Send email with the 4-digit OTP.
     */
    public function sendOtpMail(string $email, string $otp, string $purpose, ?string $recipientName = null): void
    {
        try {
            app(EmailSettingsService::class)->applyRuntimeConfiguration();

            $subject = match ($purpose) {
                'registration' => 'Account Registration Verification Code - UHS IT Helpdesk',
                'password_reset' => 'Password Reset Verification Code - UHS IT Helpdesk',
                default => 'Verification Code - UHS IT Helpdesk',
            };

            $headline = match ($purpose) {
                'registration' => 'Verify Your Staff Account Registration',
                'password_reset' => 'Reset Your Account Password',
                default => 'Verification Code Request',
            };

            $messageText = match ($purpose) {
                'registration' => 'Thank you for registering on the University of Health Sciences IT Helpdesk & Service Portal. Please enter the 4-digit verification code below to activate your account:',
                'password_reset' => 'A password reset request was received for your University of Health Sciences IT Helpdesk account. Enter the 4-digit verification code below to reset your password:',
                default => 'Your 4-digit verification code for the University of Health Sciences IT Helpdesk is:',
            };

            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b; }
                    .card { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
                    .header { background: #059669; color: #ffffff; padding: 24px; text-align: center; }
                    .header h1 { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: -0.5px; }
                    .header p { margin: 4px 0 0; font-size: 12px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
                    .content { padding: 32px 28px; }
                    .otp-box { background: #f0fdf4; border: 2px dashed #059669; border-radius: 10px; padding: 18px; text-align: center; margin: 24px 0; }
                    .otp-code { font-size: 38px; font-weight: 800; letter-spacing: 12px; color: #047857; font-family: ui-monospace, Menlo, Monaco, Consolas, monospace; }
                    .expiry-note { font-size: 13px; color: #b45309; background: #fef3c7; border-radius: 6px; padding: 8px 12px; text-align: center; margin-top: 14px; font-weight: 600; }
                    .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px; text-align: center; font-size: 11px; color: #64748b; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="header">
                        <h1>University of Health Sciences</h1>
                        <p>IT Helpdesk &bull; Hardware &amp; Networking</p>
                    </div>
                    <div class="content">
                        <h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">' . htmlspecialchars($headline) . '</h2>
                        ' . ($recipientName ? '<p>Dear <strong>' . htmlspecialchars($recipientName) . '</strong>,</p>' : '') . '
                        <p style="font-size: 14px; line-height: 1.6; color: #334155;">' . htmlspecialchars($messageText) . '</p>
                        <div class="otp-box">
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #059669; font-weight: 700; margin-bottom: 6px;">Your 4-Digit Code</div>
                            <div class="otp-code">' . htmlspecialchars($otp) . '</div>
                        </div>
                        <div class="expiry-note">
                            &#9201; This code is strictly valid for 30 minutes.
                        </div>
                        <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin-top: 24px;">
                            If you did not request this verification code, please ignore this email or contact the IT Support Helpdesk immediately at Ext. 112 / 104.
                        </p>
                    </div>
                    <div class="footer">
                        Directorate of IT &bull; University of Health Sciences Lahore<br>
                        This is an automated institutional service message.
                    </div>
                </div>
            </body>
            </html>
            ';

            Mail::html($html, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject);
            });
        } catch (Throwable $e) {
            Log::error("Failed to send OTP email to {$email}: " . $e->getMessage());
        }
    }
}

