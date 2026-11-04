<div class="sla-guidelines-card" style="display: flex; flex-direction: column; gap: 0.75rem; font-family: inherit;">
    <style>
        .sla-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 0.875rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            position: relative;
        }
        .sla-item:hover {
            transform: translateY(-1px);
        }
        .sla-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-top: 5px;
            flex-shrink: 0;
        }
        .sla-critical {
            background: linear-gradient(135deg, rgba(254, 242, 242, 0.8) 0%, rgba(255, 241, 242, 0.5) 100%);
            border: 1px solid rgba(252, 165, 165, 0.5);
        }
        .dark .sla-critical {
            background: rgba(127, 29, 29, 0.2);
            border-color: rgba(185, 28, 28, 0.4);
        }
        .sla-high {
            background: linear-gradient(135deg, rgba(255, 251, 235, 0.8) 0%, rgba(254, 243, 199, 0.4) 100%);
            border: 1px solid rgba(252, 211, 77, 0.5);
        }
        .dark .sla-high {
            background: rgba(120, 53, 15, 0.2);
            border-color: rgba(180, 83, 9, 0.4);
        }
        .sla-routine {
            background: linear-gradient(135deg, rgba(240, 249, 255, 0.8) 0%, rgba(224, 242, 254, 0.4) 100%);
            border: 1px solid rgba(186, 230, 253, 0.6);
        }
        .dark .sla-routine {
            background: rgba(12, 74, 110, 0.2);
            border-color: rgba(3, 105, 161, 0.4);
        }
        .sla-badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 9999px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.15); }
        }
        .animate-sla-pulse {
            animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

    <!-- Critical -->
    <div class="sla-item sla-critical">
        <span class="sla-dot animate-sla-pulse" style="background: #ef4444; box-shadow: 0 0 8px rgba(239, 68, 68, 0.7);"></span>
        <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #991b1b;" class="dark:text-red-300">Critical Outage</span>
                <span class="sla-badge" style="background: rgba(239, 68, 68, 0.15); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.3);">
                    &lt; 1 Hour
                </span>
            </div>
            <p style="margin: 0; font-size: 0.71875rem; line-height: 1.4; color: #7f1d1d;" class="dark:text-red-200/80">
                Datacenter, core university network, or campus-wide portal unavailability.
            </p>
        </div>
    </div>

    <!-- High -->
    <div class="sla-item sla-high">
        <span class="sla-dot" style="background: #f59e0b; box-shadow: 0 0 6px rgba(245, 158, 11, 0.5);"></span>
        <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #92400e;" class="dark:text-amber-300">High Priority</span>
                <span class="sla-badge" style="background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);">
                    &lt; 4 Hours
                </span>
            </div>
            <p style="margin: 0; font-size: 0.71875rem; line-height: 1.4; color: #78350f;" class="dark:text-amber-200/80">
                Departmental network switch, shared printer failure, or team bottleneck.
            </p>
        </div>
    </div>

    <!-- Medium / Routine -->
    <div class="sla-item sla-routine">
        <span class="sla-dot" style="background: #0284c7; box-shadow: 0 0 6px rgba(2, 132, 199, 0.5);"></span>
        <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #075985;" class="dark:text-sky-300">Medium &amp; Routine</span>
                <span class="sla-badge" style="background: rgba(2, 132, 199, 0.15); color: #0369a1; border: 1px solid rgba(2, 132, 199, 0.3);">
                    8 – 24 Hours
                </span>
            </div>
            <p style="margin: 0; font-size: 0.71875rem; line-height: 1.4; color: #0c4a6e;" class="dark:text-sky-200/80">
                Single workstation issue, OS reinstallation, or routine software support.
            </p>
        </div>
    </div>

    <!-- Contact & Operating Hours Card -->
    <div style="margin-top: 0.25rem; padding: 0.75rem 0.875rem; border-radius: 10px; background: rgba(248, 250, 252, 0.9); border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 0.4rem;" class="dark:bg-slate-900/60 dark:border-slate-800">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.71875rem; color: #334155;" class="dark:text-slate-300">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
            <span><strong>IT Hotline:</strong> Ext. 112 / 104 (Direct: 042-99231304)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.71875rem; color: #334155;" class="dark:text-slate-300">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span><strong>Support Hours:</strong> Mon – Fri, 8:00 AM – 4:00 PM</span>
        </div>
    </div>
</div>
