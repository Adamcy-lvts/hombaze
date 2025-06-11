<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyInquiry;

class PlatformOverviewWidget extends ChartWidget
{
    protected static ?string $heading = 'Platform Activity Overview';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get data for the last 12 months
        $months = collect(range(1, 12))->map(function ($month) {
            return now()->subMonths(12 - $month)->format('M Y');
        });

        $userData = collect(range(1, 12))->map(function ($month) {
            return User::whereMonth('created_at', now()->subMonths(12 - $month)->month)
                      ->whereYear('created_at', now()->subMonths(12 - $month)->year)
                      ->count();
        });

        $propertyData = collect(range(1, 12))->map(function ($month) {
            return Property::whereMonth('created_at', now()->subMonths(12 - $month)->month)
                          ->whereYear('created_at', now()->subMonths(12 - $month)->year)
                          ->count();
        });

        $inquiryData = collect(range(1, 12))->map(function ($month) {
            return PropertyInquiry::whereMonth('created_at', now()->subMonths(12 - $month)->month)
                                 ->whereYear('created_at', now()->subMonths(12 - $month)->year)
                                 ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $userData->toArray(),
                    'backgroundColor' => '#3B82F6',
                ],
                [
                    'label' => 'New Properties',
                    'data' => $propertyData->toArray(),
                    'backgroundColor' => '#10B981',
                ],
                [
                    'label' => 'Inquiries',
                    'data' => $inquiryData->toArray(),
                    'backgroundColor' => '#F59E0B',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
