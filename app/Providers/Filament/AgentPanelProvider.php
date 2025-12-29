<?php

namespace App\Providers\Filament;

use App\Filament\Agent\Pages\Auth\Register;
use Filament\Pages\Dashboard;
use App\Filament\Agent\Pages\Auth\EditProfile;
use App\Http\Middleware\RequireProfileCompletion;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Gate;
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
use App\Filament\Agent\Widgets\AgentAccountWidget;
use App\Filament\Agent\Widgets\AgentCreditStatusWidget;
use App\Filament\Pages\Pricing;

class AgentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('agent')
            ->path('agent')
            ->login()
            ->registration(Register::class)
            ->brandName('')
            ->brandLogo(asset('images/app-logo.svg'))
            ->darkModeBrandLogo(asset('images/app-logo.svg'))
            ->colors([
                'primary' => Color::Orange,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->viteTheme('resources/css/filament/agent/theme.css')
            ->renderHook('panels::head.end', fn () => view('filament.pwa.head-meta'))
            ->renderHook('panels::body.end', fn () => view('filament.custom.property-validation-script'))
            ->renderHook('panels::body.end', fn () => view('filament.pwa.body-scripts'))
            ->renderHook('panels::global-search.after', fn () => view('filament.components.credit-summary'))
            ->discoverResources(in: app_path('Filament/Agent/Resources'), for: 'App\\Filament\\Agent\\Resources')
            ->discoverPages(in: app_path('Filament/Agent/Pages'), for: 'App\\Filament\\Agent\\Pages')
            ->pages([
                Dashboard::class,
                Pricing::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Agent/Widgets'), for: 'App\\Filament\\Agent\\Widgets')
            ->widgets([
                AgentAccountWidget::class,
                AgentCreditStatusWidget::class,
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
            ;
    }

    public function boot(): void
    {
        // Role-based permissions are now handled properly through Spatie Laravel Permission
        // Independent agents are assigned the 'independent_agent' role during registration
        // This provides proper access control without bypassing security
    }
}
