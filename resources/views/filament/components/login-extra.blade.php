<div class="login-footer-nav" style="margin-top: 0.75rem; padding-top: 0.65rem; border-top: 1px solid #e2e8f0; text-align: center;">
    <style>
        .portal-register-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 7px 12px;
            border-radius: 6px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            text-decoration: none;
            color: #166534;
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.15s ease;
            box-sizing: border-box;
            margin-bottom: 6px;
        }
        .portal-register-btn:hover {
            background: #dcfce7;
            border-color: #4ade80;
            color: #14532d;
            box-shadow: 0 1px 4px rgba(5, 150, 105, 0.12);
        }
        .portal-return-card {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            padding: 6px 10px;
            border-radius: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }
        .portal-return-card:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .portal-return-icon-wrapper {
            width: 24px;
            height: 24px;
            border-radius: 5px;
            background: #e2e8f0;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }
        .portal-return-card:hover .portal-return-icon-wrapper {
            background: #059669;
            color: #ffffff;
        }
        .portal-return-arrow {
            color: #94a3b8;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .portal-return-card:hover .portal-return-arrow {
            color: #0f172a;
            transform: translateX(2px);
        }
    </style>

    @if (\App\Filament\Pages\RegistrationSettings::isRegistrationEnabled())
        <a href="{{ filament()->getRegistrationUrl() ?? url('/admin/register') }}" class="portal-register-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <span>Register as Staff / Employee</span>
        </a>
    @endif

    <a href="{{ url('/') }}" class="portal-return-card">
        <div style="display: flex; align-items: center; gap: 8px; text-align: left; min-width: 0; flex: 1;">
            <div class="portal-return-icon-wrapper">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div style="min-width: 0;">
                <div style="font-size: 0.75rem; font-weight: 600; line-height: 1.2; color: #1e293b;">
                    Return to Public Portal
                </div>
                <div style="font-size: 0.625rem; color: #64748b; margin-top: 1px;">
                    Lodge quick complaints &bull; Live ticket tracker
                </div>
            </div>
        </div>
        <div class="portal-return-arrow">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </div>
    </a>

    <!-- Institutional Hotline & Support Footer Note -->
    <div style="margin-top: 6px; font-size: 0.625rem; color: #64748b; display: flex; align-items: center; justify-content: center; gap: 5px;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
        </svg>
        <span>Directorate of IT &bull; Support Ext. <strong style="color: #334155;">112 / 104</strong></span>
    </div>
</div>
