<?php

namespace App\Filament\Landlord\Pages;

use App\Filament\Landlord\Widgets\LandlordAccountWidget;
use App\Filament\Landlord\Widgets\LandlordInfoWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Landlord\Widgets\LandlordStatsWidget;
use App\Filament\Landlord\Widgets\RecentPaymentsWidget;

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
            RecentPaymentsWidget::class,
        ];
    }

}
