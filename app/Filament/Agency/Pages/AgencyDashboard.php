<?php

namespace App\Filament\Agency\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Agency\Widgets\AgencyStatsWidget;
use App\Filament\Agency\Widgets\AgentStatsWidget;
use App\Filament\Agency\Widgets\PropertiesChartWidget;

class AgencyDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?int $navigationSort = 0;
    
    protected static string $routePath = '/';

    public function getWidgets(): array
    {
        return [
            AgencyStatsWidget::class,
            AgentStatsWidget::class,
            PropertiesChartWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ];
    }
}
