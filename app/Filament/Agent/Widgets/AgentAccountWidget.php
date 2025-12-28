<?php

namespace App\Filament\Agent\Widgets;

use Filament\Widgets\AccountWidget;

class AgentAccountWidget extends AccountWidget
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
