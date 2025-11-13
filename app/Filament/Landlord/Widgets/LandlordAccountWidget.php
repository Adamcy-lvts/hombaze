<?php

namespace App\Filament\Landlord\Widgets;

use Filament\Widgets\AccountWidget;

class LandlordAccountWidget extends AccountWidget
{
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

