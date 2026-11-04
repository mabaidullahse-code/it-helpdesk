<x-filament-panels::page.simple>
    @if ($step === 1)
        {{-- Step 1: Enter Username or Email --}}
        <form wire:submit.prevent="sendResetOtp" style="display: flex; flex-direction: column; gap: 10px;">
            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 6px 10px; font-size: 0.71875rem; color: #166534; display: flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>
                    Enter your username or registered email to receive a 4-digit code.
                </span>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                    Username or Email Address <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" wire:model.defer="login" placeholder="e.g. musman or name@uhs.edu.pk" required autofocus
                    style="width: 100%; padding: 7px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                @error('login') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 2px; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-top: 2px;">
                <button type="submit" wire:loading.attr="disabled"
                    style="width: 100%; padding: 8px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    <span wire:loading.remove>Send 4-Digit Code &rarr;</span>
                    <span wire:loading>Searching...</span>
                </button>
            </div>

            <div style="text-align: center; margin-top: 4px; font-size: 0.75rem;">
                <a href="{{ filament()->getLoginUrl() }}" style="color: #64748b; text-decoration: none; font-weight: 500;">
                    &larr; Return to Sign In
                </a>
            </div>
        </form>
    @else
        {{-- Step 2: 4-digit OTP & New Password --}}
        <form wire:submit.prevent="resetPasswordWithOtp" style="display: flex; flex-direction: column; gap: 10px;">
            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 8px 10px; text-align: center;">
                <div style="font-size: 0.78125rem; color: #065f46; font-weight: 600;">
                    4-Digit Reset Code Dispatched!
                </div>
                <div style="font-size: 0.71875rem; color: #047857; margin-top: 1px;">
                    Sent to: <strong>{{ $email }}</strong>
                </div>
                <div style="font-size: 0.6875rem; color: #b45309; background: #fef3c7; border-radius: 4px; padding: 2px 6px; margin-top: 4px; font-weight: 600; display: inline-block;">
                    &#9201; Valid for 30 minutes
                </div>
            </div>

            {{-- 4-Digit OTP --}}
            <div style="text-align: center;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                    4-Digit Verification Code
                </label>
                <input type="text" wire:model.defer="otp" maxlength="4" placeholder="••••" required autofocus
                    style="width: 150px; margin: 0 auto; padding: 6px; font-size: 22px; font-weight: 800; letter-spacing: 10px; text-align: center; border: 2px solid #059669; border-radius: 6px; box-sizing: border-box; outline: none; font-family: monospace; color: #047857;" />
                @error('otp') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 2px; display: block;">{{ $message }}</span> @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                    New Password <span style="color: #ef4444;">*</span>
                </label>
                <input type="password" wire:model.defer="password" placeholder="Min. 6 characters" required
                    style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                @error('password') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 2px; display: block;">{{ $message }}</span> @enderror
            </div>

            {{-- Confirm New Password --}}
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                    Confirm New Password <span style="color: #ef4444;">*</span>
                </label>
                <input type="password" wire:model.defer="password_confirmation" placeholder="Repeat new password" required
                    style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                @error('password_confirmation') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 2px; display: block;">{{ $message }}</span> @enderror
            </div>

            {{-- Actions --}}
            <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 2px;">
                <button type="submit" wire:loading.attr="disabled"
                    style="width: 100%; padding: 8px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    <span wire:loading.remove>Reset Password &amp; Continue &rarr;</span>
                    <span wire:loading>Updating...</span>
                </button>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; font-size: 0.75rem;">
                    <button type="button" wire:click="backToStepOne" style="background: none; border: none; color: #64748b; cursor: pointer; text-decoration: underline;">
                        &larr; Try different email
                    </button>
                    <button type="button" wire:click="resendOtp" style="background: none; border: none; color: #059669; font-weight: 600; cursor: pointer;">
                        Resend Code
                    </button>
                </div>
            </div>
        </form>
    @endif
</x-filament-panels::page.simple>
