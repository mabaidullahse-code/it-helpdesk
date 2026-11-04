<x-filament-panels::page.simple>
    @if (!\App\Filament\Pages\RegistrationSettings::isRegistrationEnabled())
        <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 14px; text-align: center; color: #991b1b; font-size: 0.8125rem;">
            <strong>Registration Currently Closed</strong>
            <p style="margin-top: 4px; font-size: 0.75rem; color: #b91c1c;">
                Self-registration is currently restricted by the administrator. Please contact the Directorate of IT for account provisioning.
            </p>
            <div style="margin-top: 10px;">
                <a href="{{ filament()->getLoginUrl() }}" style="display: inline-block; padding: 5px 12px; background: #ffffff; border: 1px solid #fca5a5; border-radius: 6px; color: #991b1b; font-weight: 600; text-decoration: none; font-size: 0.75rem;">
                    &larr; Return to Sign In
                </a>
            </div>
        </div>
    @elseif ($step === 1)
        {{-- Step 1: All User Fields Registration Form --}}
        <form wire:submit.prevent="sendRegistrationOtp" style="display: flex; flex-direction: column; gap: 8px;">
            {{-- Institutional Domain Notice --}}
            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 6px 10px; font-size: 0.71875rem; color: #166534; display: flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>
                    Requires institutional email (e.g. <strong>{{ implode(', ', \App\Filament\Pages\RegistrationSettings::getAllowedDomains()) }}</strong>).
                </span>
            </div>

            {{-- Full Name --}}
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                    Full Name <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" wire:model.defer="name" placeholder="e.g. Dr. Muhammad Usman" required
                    style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                @error('name') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
            </div>

            {{-- Username & Staff ID (2 columns) --}}
            <div class="uhs-register-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                        Username <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" wire:model.defer="username" placeholder="e.g. musman" required
                        style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                    @error('username') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                        Staff / Employee ID
                    </label>
                    <input type="text" wire:model.defer="employee_id" placeholder="e.g. EMP-1042"
                        style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                    @error('employee_id') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Email Address --}}
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                    Institutional Email Address <span style="color: #ef4444;">*</span>
                </label>
                <input type="email" wire:model.defer="email" placeholder="name@uhs.edu.pk" required
                    style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                @error('email') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
            </div>

            {{-- Department Selection --}}
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                    Department <span style="color: #ef4444;">*</span>
                </label>
                <select wire:model.defer="department_id" required
                    style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none; background: #ffffff;">
                    <option value="">-- Select Your Department --</option>
                    @foreach (\App\Models\Department::where('is_active', true)->orderBy('name')->get() as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
                @error('department_id') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
            </div>

            {{-- Designation & Phone (2 columns) --}}
            <div class="uhs-register-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                        Designation / Job Title <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" wire:model.defer="designation" placeholder="e.g. Assistant Professor" required
                        style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                    @error('designation') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                        Phone / Ext.
                    </label>
                    <input type="text" wire:model.defer="phone" placeholder="0300-1234567 or 112"
                        style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                    @error('phone') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Password & Confirmation (2 columns) --}}
            <div class="uhs-register-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                        Password <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" wire:model.defer="password" placeholder="Min. 6 chars" required
                        style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                    @error('password') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                        Confirm Password <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" wire:model.defer="password_confirmation" placeholder="Repeat password" required
                        style="width: 100%; padding: 6px 10px; font-size: 0.8125rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; outline: none;" />
                    @error('password_confirmation') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 1px; display: block;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Submit Button --}}
            <div style="margin-top: 4px;">
                <button type="submit" wire:loading.attr="disabled"
                    style="width: 100%; padding: 8px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; transition: background 0.15s ease;">
                    <span wire:loading.remove>Continue &bull; Send 4-Digit Code &rarr;</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>

            {{-- Back to Login Link --}}
            <div style="text-align: center; margin-top: 4px; font-size: 0.75rem; color: #64748b;">
                Already registered? 
                <a href="{{ filament()->getLoginUrl() }}" style="color: #059669; font-weight: 600; text-decoration: none;">
                    Sign in here
                </a>
            </div>
        </form>
    @else
        {{-- Step 2: 4-digit OTP Verification Screen --}}
        <form wire:submit.prevent="verifyAndRegister" style="display: flex; flex-direction: column; gap: 12px; text-align: center;">
            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 10px; text-align: center;">
                <div style="font-size: 0.78125rem; color: #065f46; font-weight: 600;">
                    Verification Code Sent!
                </div>
                <div style="font-size: 0.75rem; color: #047857; margin-top: 2px;">
                    Sent to: <strong>{{ $email }}</strong>
                </div>
                <div style="font-size: 0.6875rem; color: #b45309; background: #fef3c7; border-radius: 4px; padding: 2px 6px; margin-top: 6px; font-weight: 600; display: inline-block;">
                    &#9201; Valid for 30 minutes
                </div>
            </div>

            {{-- 4-Digit Code Input --}}
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Enter 4-Digit Code
                </label>
                <input type="text" wire:model.defer="otp" maxlength="4" placeholder="••••" required autofocus
                    style="width: 160px; margin: 0 auto; padding: 8px; font-size: 24px; font-weight: 800; letter-spacing: 10px; text-align: center; border: 2px solid #059669; border-radius: 6px; box-sizing: border-box; outline: none; font-family: monospace; color: #047857;" />
                @error('otp') <span style="color: #dc2626; font-size: 0.6875rem; margin-top: 2px; display: block;">{{ $message }}</span> @enderror
            </div>

            {{-- Actions --}}
            <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 4px;">
                <button type="submit" wire:loading.attr="disabled"
                    style="width: 100%; padding: 8px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    <span wire:loading.remove>Verify &amp; Complete Registration &rarr;</span>
                    <span wire:loading>Verifying...</span>
                </button>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; font-size: 0.75rem;">
                    <button type="button" wire:click="backToStepOne" style="background: none; border: none; color: #64748b; cursor: pointer; text-decoration: underline;">
                        &larr; Edit Details
                    </button>
                    <button type="button" wire:click="resendOtp" style="background: none; border: none; color: #059669; font-weight: 600; cursor: pointer;">
                        Resend Code
                    </button>
                </div>
            </div>
        </form>
    @endif
</x-filament-panels::page.simple>
