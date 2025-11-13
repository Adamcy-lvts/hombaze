<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Carbon\Carbon;

class UserActivityChartWidget extends ChartWidget
{
    protected ?string $heading = 'User Registrations (Last 30 Days)';
    
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        // Get data for the last 30 days
        $days = collect(range(1, 30))->map(function ($day) {
            return Carbon::now()->subDays(30 - $day);
        });

        $labels = $days->map(function ($date) {
            return $date->format('M j');
        })->toArray();

        $userData = $days->map(function ($date) {
            return User::whereDate('created_at', $date->toDateString())->count();
        })->toArray();

        $loginData = $days->map(function ($date) {
            return User::whereDate('last_login_at', $date->toDateString())->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'New Registrations',
                    'data' => $userData,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Daily Active Users',
                    'data' => $loginData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }
}
