<?php

namespace App\Filament\Resources\PropertyModerationResource\Pages;

use App\Filament\Resources\PropertyModerationResource;
use Filament\Resources\Pages\ListRecords;

class ListPropertyModerations extends ListRecords
{
    protected static string $resource = PropertyModerationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Property Moderation Queue';
    }

    public function getSubheading(): ?string
    {
        $count = $this->getTableRecords()->count();
        
        if ($count === 0) {
            return 'All properties have been reviewed';
        }
        
        return "{$count} " . ($count === 1 ? 'property' : 'properties') . " pending review";
    }
}
