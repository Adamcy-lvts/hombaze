<?php

namespace App\Filament\Agency\Widgets;

use Filament\Widgets\AccountWidget;

class AgencyAccountWidget extends AccountWidget
{
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
