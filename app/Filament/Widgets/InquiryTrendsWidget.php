<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PropertyInquiry;
use Carbon\Carbon;

class InquiryTrendsWidget extends ChartWidget
{
    protected static ?string $heading = 'Inquiry Trends (Last 7 Days)';
    
    protected static ?int $sort = 8;

    protected function getData(): array
    {
        // Get data for the last 7 days
        $days = collect(range(1, 7))->map(function ($day) {
            return Carbon::now()->subDays(7 - $day);
        });

        $labels = $days->map(function ($date) {
            return $date->format('D, M j');
        })->toArray();

        $newInquiries = $days->map(function ($date) {
            return PropertyInquiry::whereDate('created_at', $date->toDateString())
                ->where('status', 'new')
                ->count();
        })->toArray();

        $respondedInquiries = $days->map(function ($date) {
            return PropertyInquiry::whereDate('updated_at', $date->toDateString())
                ->where('status', 'responded')
                ->count();
        })->toArray();

        $closedInquiries = $days->map(function ($date) {
            return PropertyInquiry::whereDate('updated_at', $date->toDateString())
                ->where('status', 'closed')
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'New Inquiries',
                    'data' => $newInquiries,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => '#3B82F6',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Responded',
                    'data' => $respondedInquiries,
                    'borderColor' => '#10B981',
                    'backgroundColor' => '#10B981',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Closed',
                    'data' => $closedInquiries,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => '#EF4444',
                    'tension' => 0.4,
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
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }
}
