<?php

namespace App\Filament\Landlord\Widgets;

use Filament\Widgets\FilamentInfoWidget;

class LandlordInfoWidget extends FilamentInfoWidget
{
    protected string $view = 'filament.landlord.widgets.landlord-info-widget';

    /**
     * @var int | string | array<string, int | string | null>
     */
    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];
}
