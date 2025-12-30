<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use App\Http\Middleware\RequireLandlordAssociation;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Tenant\Widgets\TenantOverview;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Tenant\Widgets\LeaseStatusWidget;
use App\Filament\Tenant\Widgets\LeaseRenewalWidget;
use App\Filament\Tenant\Widgets\LeaseProgressWidget;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Tenant\Widgets\TenantStatusInfoWidget;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('/tenant')
            ->authGuard('web')
            ->viteTheme('resources/css/filament/tenant/theme.css')
            ->colors([
                'primary' => Color::Blue,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'danger' => Color::Red,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->discoverResources(in: app_path('Filament/Tenant/Resources'), for: 'App\\Filament\\Tenant\\Resources')
            ->discoverPages(in: app_path('Filament/Tenant/Pages'), for: 'App\\Filament\\Tenant\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Tenant/Widgets'), for: 'App\\Filament\\Tenant\\Widgets')
            ->widgets([
                TenantStatusInfoWidget::class,
                TenantOverview::class,
                LeaseProgressWidget::class,
                AccountWidget::class,

            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RequireLandlordAssociation::class,
            ])
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->spa()
            ->brandName('HomeBaze - Tenant Portal')
            ->brandLogo(asset('images/app-logo.svg'))
            ->darkModeBrandLogo(asset('images/app-logo.svg'))
            ->favicon(asset('favicon.ico'))
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->navigationGroups([
                'My Tenancy',
                'Requests & Support',
                'Property Search'
            ])
            ->renderHook('panels::head.end', fn () => view('filament.pwa.head-meta'))
            ->renderHook('panels::body.end', fn () => view('filament.pwa.body-scripts'));
    }
}
