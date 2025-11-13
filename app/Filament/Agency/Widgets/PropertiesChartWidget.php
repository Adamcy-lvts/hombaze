<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Property;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

class PropertiesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Properties Overview';
    
    protected ?string $description = 'Property listings by type and status';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected ?string $maxHeight = '300px';

    /**
     * Only show this widget to agency owners and super admins
     */
    public static function canView(): bool
    {
        $user = auth()->user();
        
        // Allow agency owners
        if ($user->user_type === 'agency_owner') {
            return true;
        }
        
        // Allow users with super_admin role
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        return false;
    }

    protected function getData(): array
    {
        // Get current agency from Filament tenant
        $agency = Filament::getTenant();
        
        if (!$agency) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }
        
        // Get monthly property listings for the last 6 months
        $monthlyListings = Property::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('count(*) as count')
            )
            ->where('agency_id', $agency->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        $months = [];
        $counts = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $found = $monthlyListings->where('year', $date->year)
                                   ->where('month', $date->month)
                                   ->first();
            $counts[] = $found ? $found->count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Properties Listed',
                    'data' => $counts,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                    ],
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $months,
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
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
