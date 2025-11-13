<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Property;
use App\Models\State;

class GeographicDistributionWidget extends ChartWidget
{
    protected ?string $heading = 'Properties by State';
    
    protected static ?int $sort = 7;

    protected function getData(): array
    {
        $states = State::withCount('properties')
            ->orderBy('properties_count', 'desc')
            ->limit(10)
            ->get();
        
        $labels = $states->pluck('name')->toArray();
        $data = $states->pluck('properties_count')->toArray();
        
        // Generate gradient colors
        $colors = [
            '#1E40AF', '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD',
            '#C7D2FE', '#E0E7FF', '#EEF2FF', '#F8FAFC', '#F1F5F9'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Properties',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => '#1E3A8A',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                    ],
                ],
            ],
            'indexAxis' => 'x',
        ];
    }
}
