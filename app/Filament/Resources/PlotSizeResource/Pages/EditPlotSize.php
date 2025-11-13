<?php

namespace App\Filament\Resources\PlotSizeResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\PlotSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlotSize extends EditRecord
{
    protected static string $resource = PlotSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
