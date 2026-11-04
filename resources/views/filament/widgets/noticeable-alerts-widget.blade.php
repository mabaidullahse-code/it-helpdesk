<div class="uhs-alerts-root" style="grid-column: 1 / -1 !important; width: 100% !important; margin-bottom: 0.5rem;">
<style>
    .uhs-alerts-container {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        width: 100%;
        margin-bottom: 0.5rem;
        font-family: inherit;
    }

    .uhs-metrics-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.85rem;
        width: 100%;
        box-sizing: border-box;
    }

    @media (min-width: 600px) {
        .uhs-metrics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 960px) {
        .uhs-metrics-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .uhs-metric-card {
        background: #ffffff;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        padding: 0.875rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .uhs-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .uhs-metric-content {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }

    .uhs-metric-title {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .uhs-metric-value-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .uhs-metric-number {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .uhs-metric-subtext {
        font-size: 0.6875rem;
        color: #94a3b8;
        margin-top: 0.2rem;
    }

    .uhs-metric-badge {
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        line-height: 1.2;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        white-space: nowrap;
    }

    .uhs-badge-success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .uhs-badge-warning {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .uhs-badge-info {
        background: #f0f9ff;
        color: #075985;
        border: 1px solid #bae6fd;
    }

    .uhs-badge-neutral {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .uhs-metric-icon-box {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .uhs-icon-emerald {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #d1fae5;
    }

    .uhs-icon-amber {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fef3c7;
    }

    .uhs-icon-sky {
        background: #f0f9ff;
        color: #0284c7;
        border: 1px solid #e0f2fe;
    }

    .uhs-icon-teal {
        background: #f0fdfa;
        color: #0d9488;
        border: 1px solid #ccfbf1;
    }

    /* Notice Banner */
    .uhs-notice-banner {
        border-radius: 0.75rem;
        padding: 0.875rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        flex-wrap: wrap;
    }

    .uhs-notice-critical {
        background: linear-gradient(90deg, #fff1f2 0%, #fffbeb 50%, #fef2f2 100%);
        border: 1px solid #fecdd3;
        border-left: 4px solid #e11d48;
    }

    .uhs-notice-healthy {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 4px solid #10b981;
    }

    .uhs-notice-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        min-width: 240px;
    }

    .uhs-notice-icon-box {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .uhs-notice-icon-rose {
        background: #ffe4e6;
        color: #e11d48;
    }

    .uhs-notice-icon-emerald {
        background: #dcfce7;
        color: #10b981;
    }

    .uhs-notice-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .uhs-notice-desc {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.15rem;
        line-height: 1.4;
    }

    .uhs-notice-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #e11d48;
        color: #ffffff !important;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.45rem 0.9rem;
        border-radius: 0.5rem;
        text-decoration: none !important;
        box-shadow: 0 2px 4px rgba(225, 29, 72, 0.2);
        transition: background-color 0.15s ease, transform 0.15s ease;
        white-space: nowrap;
    }

    .uhs-notice-btn:hover {
        background: #be123c;
        transform: translateY(-1px);
    }
</style>

<div class="uhs-alerts-container">
    {{-- Operational Metric Strips (4 Columns) --}}
    <div class="uhs-metrics-grid">
        {{-- Card 1: SLA Compliance Rate --}}
        <div class="uhs-metric-card">
            <div class="uhs-metric-content">
                <span class="uhs-metric-title">SLA Compliance</span>
                <div class="uhs-metric-value-row">
                    <span class="uhs-metric-number">{{ $slaRate }}%</span>
                    @if ($slaRate >= 90)
                        <span class="uhs-metric-badge uhs-badge-success">● Healthy</span>
                    @else
                        <span class="uhs-metric-badge uhs-badge-warning">● Attention</span>
                    @endif
                </div>
                <span class="uhs-metric-subtext">Institutional Target: &ge; 90%</span>
            </div>
            <div class="uhs-metric-icon-box uhs-icon-emerald">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <polyline points="9 12 11 14 15 10"></polyline>
                </svg>
            </div>
        </div>

        {{-- Card 2: Unassigned Queue --}}
        <div class="uhs-metric-card">
            <div class="uhs-metric-content">
                <span class="uhs-metric-title">Unassigned Queue</span>
                <div class="uhs-metric-value-row">
                    <span class="uhs-metric-number">{{ $unassignedCount }}</span>
                    @if ($unassignedCount > 0)
                        <span class="uhs-metric-badge uhs-badge-warning">Needs Dispatch</span>
                    @else
                        <span class="uhs-metric-badge uhs-badge-neutral">Clear</span>
                    @endif
                </div>
                <span class="uhs-metric-subtext">Awaiting technician assignment</span>
            </div>
            <div class="uhs-metric-icon-box uhs-icon-amber">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                    <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                </svg>
            </div>
        </div>

        {{-- Card 3: IT Fleet on Duty --}}
        <div class="uhs-metric-card">
            <div class="uhs-metric-content">
                <span class="uhs-metric-title">IT Fleet On Duty</span>
                <div class="uhs-metric-value-row">
                    <span class="uhs-metric-number">{{ $techniciansCount }}</span>
                    <span class="uhs-metric-badge uhs-badge-info">Active Staff</span>
                </div>
                <span class="uhs-metric-subtext">Hardware & Networks division</span>
            </div>
            <div class="uhs-metric-icon-box uhs-icon-sky">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
        </div>

        {{-- Card 4: Support Hotline --}}
        <div class="uhs-metric-card">
            <div class="uhs-metric-content">
                <span class="uhs-metric-title">Emergency IT Hotline</span>
                <div class="uhs-metric-value-row">
                    <span class="uhs-metric-number" style="color: #059669; font-size: 1.1rem;">Ext. 112 / 104</span>
                </div>
                <span class="uhs-metric-subtext">Directorate of IT Helpdesk</span>
            </div>
            <div class="uhs-metric-icon-box uhs-icon-teal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Notice Banner (Attention Required vs Everything Healthy) --}}
    @if ($overdueCount > 0 || $criticalCount > 0)
        <div class="uhs-notice-banner uhs-notice-critical">
            <div class="uhs-notice-left">
                <div class="uhs-notice-icon-box uhs-notice-icon-rose">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <div class="uhs-notice-title">
                        Attention Required: {{ $overdueCount }} Overdue &bull; {{ $criticalCount }} Critical Priority Complaints
                    </div>
                    <div class="uhs-notice-desc">
                        Certain institutional tickets have exceeded SLA targets or require immediate escalation by senior technical supervisors.
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ url('/admin/tickets?tableFilters[status][values][0]=in_progress') }}" 
                   class="uhs-notice-btn">
                    <span>View Escalated Queue</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    @else
        <div class="uhs-notice-banner uhs-notice-healthy">
            <div class="uhs-notice-left">
                <div class="uhs-notice-icon-box uhs-notice-icon-emerald">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="uhs-notice-title" style="color: #065f46;">
                        All Institutional SLA Targets Are Healthy
                    </div>
                    <div class="uhs-notice-desc" style="color: #047857;">
                        There are no overdue or unresolved critical complaints at this moment. The IT fleet is on schedule.
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
