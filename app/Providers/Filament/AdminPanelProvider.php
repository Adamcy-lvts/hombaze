<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use App\Filament\Widgets\AdminStatsWidget;
use App\Filament\Widgets\RevenueStatsWidget;
use App\Filament\Widgets\PlatformOverviewWidget;
use App\Filament\Widgets\PropertyTypesChartWidget;
use App\Filament\Widgets\UserActivityChartWidget;
use App\Filament\Widgets\GeographicDistributionWidget;
use App\Filament\Widgets\InquiryTrendsWidget;
use App\Filament\Widgets\RecentUsersWidget;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(asset('images/app-logo.svg'))
            ->darkModeBrandLogo(asset('images/app-logo.svg'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AdminStatsWidget::class,
                RevenueStatsWidget::class,
                PlatformOverviewWidget::class,
                PropertyTypesChartWidget::class,
                UserActivityChartWidget::class,
                GeographicDistributionWidget::class,
                InquiryTrendsWidget::class,
                RecentUsersWidget::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
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
            ])
            ->renderHook('panels::head.end', fn () => view('filament.pwa.head-meta'))
            ->renderHook('panels::body.end', fn () => view('filament.custom.property-validation-script'))
            ->renderHook('panels::body.end', fn () => view('filament.pwa.body-scripts'));
    }
}
