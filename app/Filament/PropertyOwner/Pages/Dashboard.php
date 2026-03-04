<?php

namespace App\Filament\PropertyOwner\Pages;

use App\Filament\PropertyOwner\Widgets\LandlordAccountWidget;
use App\Filament\PropertyOwner\Widgets\LandlordInfoWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\PropertyOwner\Widgets\LandlordStatsWidget;
use App\Filament\PropertyOwner\Widgets\LeaseProgressWidget;
use App\Filament\PropertyOwner\Widgets\RecentPaymentsWidget;

class Dashboard extends BaseDashboard
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Landlord Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            LandlordAccountWidget::class,
            LandlordInfoWidget::class,
            LandlordStatsWidget::class,
            LeaseProgressWidget::class,
            RecentPaymentsWidget::class,
        ];
    }

}
