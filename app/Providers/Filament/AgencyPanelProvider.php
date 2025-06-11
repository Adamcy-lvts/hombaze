<?php

namespace App\Providers\Filament;

use App\Filament\Agency\Pages\AgencyDashboard;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use App\Filament\Agency\Pages\Tenancy\EditAgencyProfile;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\Agency;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Http\Middleware\ApplyAgencyScopes;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Http\Middleware\EnsureUserBelongsToAgency;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Navigation\MenuItem;

class AgencyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('agency')
            ->path('agency')
            ->login()
            ->tenant(Agency::class, slugAttribute: 'slug')
            ->registration(\App\Filament\Agency\Pages\Auth\Register::class)
            ->tenantProfile(EditAgencyProfile::class)
            ->tenantMenu(fn() => auth()->user()->can('view_tenant_menu'))
            ->tenantMenuItems([
                'profile' => MenuItem::make()
                    ->label('Agency Profile')
                    ->icon('heroicon-o-building-office')
                    ->url(fn (): string => EditAgencyProfile::getUrl())
                    ->visible(fn (): bool => auth()->user()->can('update_agency_profile')),
                
            ])
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
            ])
            ->discoverWidgets(in: app_path('Filament/Agency/Widgets'), for: 'App\\Filament\\Agency\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ]);
    }
}
