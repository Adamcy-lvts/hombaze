<?php

namespace App\Filament\Agent\Resources\ReviewResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Agent\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Respond to Review'),
        ];
    }
}
