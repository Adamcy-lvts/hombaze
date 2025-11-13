<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Property;
use App\Models\PropertyType;

class PropertyTypesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Property Types Distribution';
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $propertyTypes = PropertyType::withCount('properties')->get();
        
        $labels = $propertyTypes->pluck('name')->toArray();
        $data = $propertyTypes->pluck('properties_count')->toArray();
        
        // Generate colors for each property type
        $colors = [
            '#3B82F6', // Blue
            '#EF4444', // Red
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#8B5CF6', // Purple
            '#F97316', // Orange
            '#06B6D4', // Cyan
            '#84CC16', // Lime
        ];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
