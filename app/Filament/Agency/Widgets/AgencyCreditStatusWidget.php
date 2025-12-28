<?php

namespace App\Filament\Agency\Widgets;

use Filament\Widgets\FilamentInfoWidget;

class AgencyCreditStatusWidget extends FilamentInfoWidget
{
    protected string $view = 'filament.agency.widgets.agency-credit-status-widget';

    /**
     * @var int | string | array<string, int | string | null>
     */
    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'lg' => 3,
        'xl' => 2,
    ];
}
