<style>
    /* =========================================================
       UHS IT HELPDESK - GLOBAL DESIGN SYSTEM & STYLING OVERRIDES
       ========================================================= */

    /* 1. Universal SVG Containment (prevents runaway icon explosion) */
    svg:not([width]):not([height]),
    .fi-body svg,
    .fi-main svg {
        max-width: 100%;
    }

    .fi-btn svg,
    .fi-badge svg,
    .fi-icon svg,
    .fi-ta-cell svg,
    .fi-header-actions svg,
    .fi-breadcrumbs svg {
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
        flex-shrink: 0 !important;
    }

    .fi-logo svg {
        max-width: 24px !important;
        max-height: 24px !important;
    }

    /* 2. Topbar & Brand Header */
    .fi-topbar {
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03) !important;
    }

    /* 3. Sidebar Navigation */
    .fi-sidebar {
        border-right: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-btn {
        background-color: #ecfdf5 !important;
        color: #065f46 !important;
        font-weight: 700 !important;
        border-right: 3px solid #059669 !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: #059669 !important;
    }

    .fi-sidebar-group-label {
        font-size: 0.6875rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.075em !important;
        color: #94a3b8 !important;
    }

    /* 4. Page Header & Action Buttons */
    .fi-header-heading {
        font-weight: 800 !important;
        letter-spacing: -0.025em !important;
        color: #0f172a !important;
    }

    .fi-header-subheading {
        color: #64748b !important;
    }

    /* Primary Buttons across all resources */
    .fi-btn-color-primary,
    button.fi-btn.fi-color-primary {
        background-color: #059669 !important;
        color: #ffffff !important;
        border-radius: 0.5rem !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25) !important;
        transition: all 0.15s ease-in-out !important;
    }

    .fi-btn-color-primary:hover,
    button.fi-btn.fi-color-primary:hover {
        background-color: #047857 !important;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35) !important;
        transform: translateY(-1px);
    }

    /* 5. Resource Tables - Crisp, Readable Data Grids */
    .fi-ta-ctn {
        border-radius: 0.875rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.05) !important;
        background-color: #ffffff !important;
        overflow: hidden !important;
    }

    .fi-ta-header {
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        padding: 0.875rem 1.25rem !important;
    }

    .fi-ta-header-cell {
        font-size: 0.6875rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.06em !important;
        color: #475569 !important;
        background-color: #f8fafc !important;
        padding: 0.75rem 1rem !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    .fi-ta-record:hover {
        background-color: #f0fdf4 !important;
    }

    /* 6. Form Sections & Panels */
    .fi-section {
        border-radius: 0.875rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
        background-color: #ffffff !important;
        margin-bottom: 1.5rem !important;
    }

    .fi-section-header {
        padding: 1rem 1.25rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .fi-section-header-heading {
        font-size: 0.9375rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }

    .fi-section-header-description {
        font-size: 0.8125rem !important;
        color: #64748b !important;
    }

    .fi-section-content {
        padding: 1.25rem !important;
    }

    /* 7. Input Fields & Focus States */
    .fi-input-wrp {
        border-radius: 0.5rem !important;
        border-color: #cbd5e1 !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    }

    .fi-input-wrp:focus-within {
        border-color: #059669 !important;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15) !important;
    }

    /* 8. Badges across all tables and infolists (Anti-truncation) */
    .fi-badge-label {
        overflow: visible !important;
        text-overflow: clip !important;
        white-space: nowrap !important;
    }

    .fi-badge,
    .fi-in-text-item.fi-badge {
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.025em !important;
        padding: 0.25rem 0.65rem !important;
        border-radius: 9999px !important;
        max-width: fit-content !important;
        white-space: nowrap !important;
        display: inline-flex !important;
        align-items: center !important;
        min-width: min-content !important;
    }

    .fi-in-entry-wrp {
        min-width: 0 !important;
        overflow: visible !important;
    }

    .fi-in-text-item {
        overflow: visible !important;
    }

    /* Tracking ID Badge - Prominent Monospace Emerald Badge */
    .fi-badge.fi-color-primary {
        background-color: #ecfdf5 !important;
        color: #065f46 !important;
        border: 1px solid #a7f3d0 !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        font-weight: 800 !important;
        letter-spacing: 0.04em !important;
        box-shadow: 0 1px 2px rgba(5, 150, 105, 0.08) !important;
    }

    /* Gray Badges (Departments, etc.) */
    .fi-badge.fi-color-gray {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        border: 1px solid #e2e8f0 !important;
    }

    /* 9. KPI Stat Overview Widgets */
    .fi-wi-stats-overview-stat {
        border-radius: 0.875rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        background-color: #ffffff !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }

    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(15, 23, 42, 0.08) !important;
    }

    /* Force Full-Width Dashboard Widgets across all Grid Breakpoints */
    .uhs-hero-root,
    .uhs-alerts-root,
    .fi-grid > div:has(.uhs-hero-root),
    .fi-grid > div:has(.uhs-alerts-root),
    .fi-grid > div:has(.uhs-hero-dual-grid),
    .fi-grid > div:has(#admin-welcome-card),
    .fi-grid > div:has(.uhs-alerts-container),
    .fi-grid > div[wire\:name*="QuickLodgeComplaintWidget"],
    .fi-grid > div[wire\:name*="NoticeableAlertsWidget"],
    .fi-grid > div[wire\:name*="RecentComplaintsWidget"],
    div[wire\:name*="QuickLodgeComplaintWidget"],
    div[wire\:name*="NoticeableAlertsWidget"],
    div[wire\:name*="RecentComplaintsWidget"] {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* 10. Simple / Login / Auth Pages - Compact, Pixel-Perfect Viewport Fit & Mobile Responsive */
    .uhs-brand-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        height: 42px;
        max-height: 42px;
        user-select: none;
    }

    .uhs-brand-logo-img {
        width: 38px;
        height: 38px;
        object-fit: contain;
        flex-shrink: 0;
        image-rendering: -webkit-optimize-contrast;
    }

    .uhs-brand-text {
        display: flex;
        flex-direction: column;
        text-align: left;
        line-height: 1.2;
    }

    .uhs-brand-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.3px;
    }

    .uhs-brand-sub {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        color: #059669;
        letter-spacing: 0.5px;
    }

    html:has(body.fi-simple-page),
    body.fi-simple-page {
        background: #ffffff !important;
        background-color: #ffffff !important;
        height: 100vh !important;
        max-height: 100vh !important;
        position: relative !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }

    .fi-simple-layout {
        position: relative !important;
        z-index: 10 !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
        height: 100vh !important;
        max-height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 0.25rem 1rem !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    /* On multi-field registration page, allow graceful scroll if viewport is very short */
    .fi-simple-page:has(.uhs-register-grid) {
        overflow-y: auto !important;
    }
    .fi-simple-page:has(.uhs-register-grid) .fi-simple-layout {
        height: auto !important;
        min-height: 100vh !important;
        max-height: none !important;
        overflow-y: visible !important;
        padding: 1.5rem 1rem !important;
    }

    .fi-simple-main-ctn {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
        max-width: 24.5rem !important;
        margin: 0 auto !important;
        box-sizing: border-box !important;
    }

    /* Allow slightly wider container on multi-field registration */
    .fi-simple-page:has(.uhs-register-grid) .fi-simple-main-ctn {
        max-width: 28rem !important;
    }

    .fi-simple-main {
        background: #ffffff !important;
        background-color: #ffffff !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border-radius: 0.75rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 
                    0 2px 4px -2px rgba(0, 0, 0, 0.03) !important;
        padding: 0.85rem 1.15rem !important;
        position: relative !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        transition: none !important;
        width: 100% !important;
    }

    .dark .fi-simple-main {
        background: #ffffff !important;
        background-color: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03) !important;
    }

    /* Remove decorative gradient top line */
    .fi-simple-main::before {
        display: none !important;
        content: none !important;
    }

    /* Compact Auth Page Header & Prominent Clear UHS Logo */
    .fi-simple-page .fi-simple-header {
        margin-bottom: 0.25rem !important;
        text-align: center !important;
    }

    .fi-simple-page .fi-logo {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        height: auto !important;
        max-height: none !important;
        margin-bottom: 0.15rem !important;
    }

    .fi-simple-page .uhs-brand-wrapper {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        gap: 4px !important;
        height: auto !important;
        max-height: none !important;
        margin: 0 auto !important;
    }

    .fi-simple-page .uhs-brand-logo-img {
        width: 54px !important;
        height: 54px !important;
        max-width: 54px !important;
        max-height: 54px !important;
        object-fit: contain !important;
        image-rendering: -webkit-optimize-contrast !important;
        filter: drop-shadow(0 1px 3px rgba(0, 0, 0, 0.06)) !important;
    }

    .fi-simple-page .uhs-brand-text {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        text-align: center !important;
        gap: 1px !important;
    }

    .fi-simple-page .uhs-brand-title {
        font-size: 0.95rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        color: #0f172a !important;
        line-height: 1.2 !important;
    }

    .fi-simple-page .uhs-brand-sub {
        font-size: 0.6rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        color: #059669 !important;
        letter-spacing: 0.04em !important;
        line-height: 1.1 !important;
    }

    .fi-simple-page .fi-simple-header-heading {
        font-size: 1rem !important;
        font-weight: 700 !important;
        letter-spacing: -0.02em !important;
        color: #1e293b !important;
        margin-top: 0.2rem !important;
        margin-bottom: 0 !important;
        line-height: 1.2 !important;
        text-align: center !important;
    }

    .dark .fi-simple-page .fi-simple-header-heading {
        color: #1e293b !important;
    }

    .fi-simple-page .fi-simple-header-subheading {
        display: none !important;
    }

    /* Compact, Crisp Login Form Inputs */
    .fi-simple-page form {
        gap: 0.25rem !important;
    }

    .fi-simple-page .fi-fo-field-wrp {
        margin-bottom: 0.2rem !important;
    }

    .fi-simple-page .fi-fo-field-wrp-label,
    .fi-simple-page label {
        margin-bottom: 0.1rem !important;
        font-size: 0.73rem !important;
        font-weight: 600 !important;
        color: #334155 !important;
    }

    .fi-simple-page .fi-input-wrp {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    }

    .fi-simple-page .fi-input-wrp input {
        padding-top: 0.32rem !important;
        padding-bottom: 0.32rem !important;
        font-size: 0.8125rem !important;
        color: #0f172a !important;
    }

    .dark .fi-simple-page .fi-input-wrp {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
    }

    .fi-simple-page .fi-input-wrp:focus-within {
        border-color: #059669 !important;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.15) !important;
        background-color: #ffffff !important;
    }

    /* Compact Solid Sign In Button */
    .fi-simple-page button[type="submit"] {
        background: #059669 !important;
        background-color: #059669 !important;
        color: #ffffff !important;
        border-radius: 0.375rem !important;
        font-weight: 600 !important;
        font-size: 0.8125rem !important;
        padding: 0.4rem 0.8rem !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        letter-spacing: 0.01em !important;
        transition: background-color 0.15s ease !important;
        width: 100% !important;
        margin-top: 0.15rem !important;
        border: none !important;
        cursor: pointer !important;
    }

    .fi-simple-page button[type="submit"]:hover {
        background: #047857 !important;
        background-color: #047857 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06) !important;
        transform: none !important;
    }

    .fi-simple-page button[type="submit"]:active {
        background: #065f46 !important;
        transform: none !important;
    }

    /* Mobile Responsiveness Overrides */
    @media (max-width: 640px) {
        html:has(body.fi-simple-page),
        body.fi-simple-page {
            height: auto !important;
            min-height: 100vh !important;
        }

        .fi-simple-layout {
            height: auto !important;
            min-height: 100vh !important;
            max-height: none !important;
            justify-content: flex-start !important;
            padding: 1rem 0.75rem 2rem 0.75rem !important;
            overflow-y: visible !important;
        }

        .fi-simple-main-ctn {
            max-width: 100% !important;
            width: 100% !important;
        }

        .fi-simple-main {
            padding: 1rem 0.875rem !important;
            border-radius: 0.625rem !important;
        }

        .fi-simple-page .uhs-brand-logo-img {
            width: 52px !important;
            height: 52px !important;
            max-width: 52px !important;
            max-height: 52px !important;
        }

        .fi-simple-page .uhs-brand-title {
            font-size: 0.9375rem !important;
        }

        .fi-simple-page .uhs-brand-sub {
            font-size: 0.5625rem !important;
        }

        .fi-simple-page .fi-simple-header-heading {
            font-size: 1rem !important;
        }

        .uhs-register-grid {
            grid-template-columns: 1fr !important;
            gap: 6px !important;
        }
    }
</style>
