<?php

namespace App\Providers\Filament;

use App\Filament\PropertyOwner\Pages\Auth\Register;
use App\Filament\PropertyOwner\Pages\Dashboard;
use App\Filament\PropertyOwner\Widgets\LandlordAccountWidget;
use App\Filament\PropertyOwner\Widgets\PropertyOwnerProfileWidget;
use App\Filament\PropertyOwner\Widgets\LandlordInfoWidget;
use App\Filament\PropertyOwner\Widgets\LeaseProgressWidget;
use App\Filament\PropertyOwner\Pages\Auth\EditProfile;
use App\Http\Middleware\RequireProfileCompletion;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\MenuItem;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Pages\Pricing;

class PropertyOwnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('property-owner')
            ->path('owner')
            ->login()
            ->registration(Register::class)
            ->brandLogo(asset('images/app-logo.svg'))
            ->darkModeBrandLogo(asset('images/app-logo.svg'))
            ->colors([
                'primary' => Color::Green,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->viteTheme('resources/css/filament/property-owner/theme.css')
            ->discoverResources(in: app_path('Filament/PropertyOwner/Resources'), for: 'App\\Filament\\PropertyOwner\\Resources')
            ->discoverPages(in: app_path('Filament/PropertyOwner/Pages'), for: 'App\\Filament\\PropertyOwner\\Pages')
            ->pages([
                Dashboard::class,
                Pricing::class,
            ])
            ->discoverWidgets(in: app_path('Filament/PropertyOwner/Widgets'), for: 'App\\Filament\\PropertyOwner\\Widgets')
            ->widgets([
                LandlordAccountWidget::class,
                PropertyOwnerProfileWidget::class,
                LandlordInfoWidget::class,
                LeaseProgressWidget::class,
            ])
            ->profile(EditProfile::class)
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
                RequireProfileCompletion::class,
            ])
            ->renderHook('panels::head.end', fn () => view('filament.pwa.head-meta'))
            ->renderHook('panels::body.end', fn () => view('filament.custom.property-validation-script'))
            ->renderHook('panels::body.end', fn () => view('filament.pwa.body-scripts'))
            ->renderHook('panels::global-search.after', fn () => view('filament.components.credit-summary'))
            ->renderHook('panels::body.end', fn () => view('filament.property-owner.components.mobile-bottom-nav'));
    }
}
