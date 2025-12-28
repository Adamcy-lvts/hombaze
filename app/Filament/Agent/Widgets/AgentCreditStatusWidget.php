<?php

namespace App\Filament\Agent\Widgets;

use Filament\Widgets\FilamentInfoWidget;

class AgentCreditStatusWidget extends FilamentInfoWidget
{
    protected string $view = 'filament.agent.widgets.agent-credit-status-widget';

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
