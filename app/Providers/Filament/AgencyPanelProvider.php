<?php

namespace App\Providers\Filament;

use App\Filament\Agency\Pages\Auth\Register;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\Agency;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Http\Middleware\ApplyAgencyScopes;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Agency\Pages\AgencyDashboard;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Http\Middleware\EnsureUserBelongsToAgency;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Agency\Pages\Tenancy\EditAgencyProfile;
use App\Filament\Agency\Pages\Auth\EditProfile;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Agency\Widgets\AgencyAccountWidget;
use App\Filament\Agency\Widgets\AgencyCreditStatusWidget;
use App\Filament\Pages\Pricing;

class AgencyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('agency')
            ->path('agency')
            ->login()
            ->tenant(Agency::class, slugAttribute: 'slug')
            ->registration(Register::class)
            ->tenantProfile(EditAgencyProfile::class)
            ->profile(EditProfile::class)
            ->tenantMenu(fn() => auth()->user()->can('view_tenant_menu'))
            ->tenantMenuItems([
                'profile' => MenuItem::make()
                    ->label('Agency Profile')
                    ->icon('heroicon-o-building-office')
                    ->url(fn(): string => EditAgencyProfile::getUrl())
                    ->visible(fn(): bool => auth()->user()->can('update_agency_profile')),

            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->brandLogo(asset('images/app-logo.svg'))
            ->darkModeBrandLogo(asset('images/app-logo.svg'))
            ->viteTheme('resources/css/filament/agency/theme.css')
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Agency/Resources'), for: 'App\\Filament\\Agency\\Resources')
            ->discoverPages(in: app_path('Filament/Agency/Pages'), for: 'App\\Filament\\Agency\\Pages')
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->tenantMiddleware([
                SyncShieldTenant::class,
            ], isPersistent: true)
            ->pages([
                AgencyDashboard::class,
                Pricing::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Agency/Widgets'), for: 'App\\Filament\\Agency\\Widgets')
            ->widgets([
                AgencyAccountWidget::class,
                AgencyCreditStatusWidget::class,
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
                ApplyAgencyScopes::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook('panels::body.end', fn () => view('filament.custom.property-validation-script'))
            ->renderHook('panels::global-search.after', fn () => view('filament.components.credit-summary'));
    }
}
