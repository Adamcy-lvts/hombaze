<?php

namespace App\Filament\Agency\Resources\PropertyInquiryResource\Pages;

use App\Filament\Agency\Resources\PropertyInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPropertyInquiry extends ViewRecord
{
    protected static string $resource = PropertyInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit action since inquiries are read-only
            Actions\Action::make('respond')
                ->label('Respond to Inquiry')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'pending')
                ->action(function ($record) {
                    // This will be handled by the table action
                    return redirect()->route('filament.agency.resources.property-inquiries.index');
                }),
        ];
    }
}
