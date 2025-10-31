<?php

namespace App\Filament\Resources\PlotSizeResource\Pages;

use App\Filament\Resources\PlotSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlotSizes extends ListRecords
{
    protected static string $resource = PlotSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
