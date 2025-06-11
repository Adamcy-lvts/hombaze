<?php

namespace App\Filament\Agent\Resources\ReviewResource\Pages;

use App\Filament\Agent\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - agents can't create reviews
        ];
    }
}
