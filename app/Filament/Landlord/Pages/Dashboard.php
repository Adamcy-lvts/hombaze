<?php

namespace App\Filament\Landlord\Pages;

use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Landlord\Widgets\LandlordStatsWidget;
use App\Filament\Landlord\Widgets\RentCollectionWidget;
use App\Filament\Landlord\Widgets\PaymentStatsWidget;
use App\Filament\Landlord\Widgets\RecentPaymentsWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Landlord Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            LandlordStatsWidget::class,
            PaymentStatsWidget::class,
            RentCollectionWidget::class,
            RecentPaymentsWidget::class,
            FilamentInfoWidget::class,
        ];
    }

}
