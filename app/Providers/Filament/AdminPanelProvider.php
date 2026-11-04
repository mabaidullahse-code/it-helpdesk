<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Pages\Auth\CustomPasswordReset;
use App\Filament\Pages\Auth\CustomRegister;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->registration(CustomRegister::class)
            ->passwordReset(CustomPasswordReset::class)
            ->brandName('UHS IT Helpdesk & Service Portal')
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn () => view('filament.components.custom-styles'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.components.login-extra'),
            )
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\NoticeableAlertsWidget::class,
                \App\Filament\Widgets\TicketStatsOverview::class,
                \App\Filament\Widgets\QuickLodgeComplaintWidget::class,
                \App\Filament\Widgets\ComplaintsTrendChart::class,
                \App\Filament\Widgets\ComplaintsCategoryChart::class,
                \App\Filament\Widgets\RecentComplaintsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
