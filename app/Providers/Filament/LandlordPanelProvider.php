<?php

namespace App\Providers\Filament;

use App\Filament\Landlord\Pages\Auth\Register;
use App\Filament\Landlord\Pages\Dashboard;
use App\Filament\Landlord\Widgets\LandlordAccountWidget;
use App\Filament\Landlord\Widgets\PropertyOwnerProfileWidget;
use App\Filament\Landlord\Widgets\LandlordInfoWidget;
use App\Filament\Landlord\Pages\Auth\EditProfile;
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

class LandlordPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('landlord')
            ->path('landlord')
            ->login()
            ->registration(Register::class)
            ->brandLogo(asset('images/app-logo.svg'))
            ->darkModeBrandLogo(asset('images/app-logo.svg'))
            ->colors([
                'primary' => Color::Green,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->viteTheme('resources/css/filament/landlord/theme.css')
            ->discoverResources(in: app_path('Filament/Landlord/Resources'), for: 'App\\Filament\\Landlord\\Resources')
            ->discoverPages(in: app_path('Filament/Landlord/Pages'), for: 'App\\Filament\\Landlord\\Pages')
            ->pages([
                Dashboard::class,
                Pricing::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Landlord/Widgets'), for: 'App\\Filament\\Landlord\\Widgets')
            ->widgets([
                LandlordAccountWidget::class,
                PropertyOwnerProfileWidget::class,
                LandlordInfoWidget::class,
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
            ->renderHook('panels::global-search.after', fn () => view('filament.components.credit-summary'));
    }
}
