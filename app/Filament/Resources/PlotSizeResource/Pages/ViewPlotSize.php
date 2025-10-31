<?php

namespace App\Filament\Resources\PlotSizeResource\Pages;

use App\Filament\Resources\PlotSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlotSize extends ViewRecord
{
    protected static string $resource = PlotSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
