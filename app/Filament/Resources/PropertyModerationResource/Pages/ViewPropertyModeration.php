<?php

namespace App\Filament\Resources\PropertyModerationResource\Pages;

use App\Filament\Resources\PropertyModerationResource;
use App\Models\Property;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Split;
use Filament\Infolists\Components\TextEntry;

class ViewPropertyModeration extends ViewRecord
{
    protected static string $resource = PropertyModerationResource::class;

    public function getTitle(): string
    {
        return 'Review: ' . $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Property')
                ->modalDescription('This property will be visible to all users after approval.')
                ->form([
                    Forms\Components\Textarea::make('moderation_notes')
                        ->label('Notes (optional)')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'moderation_status' => 'approved',
                        'moderated_at' => now(),
                        'moderated_by' => auth()->id(),
                        'moderation_notes' => $data['moderation_notes'] ?? null,
                    ]);
                    
                    Notification::make()
                        ->success()
                        ->title('Property Approved')
                        ->body("'{$this->record->title}' is now live.")
                        ->send();

                    $this->redirect(PropertyModerationResource::getUrl('index'));
                }),

            Actions\Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Property')
                ->modalDescription('Please provide a reason for rejection.')
                ->form([
                    Forms\Components\Textarea::make('moderation_notes')
                        ->label('Reason for Rejection')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'moderation_status' => 'rejected',
                        'moderated_at' => now(),
                        'moderated_by' => auth()->id(),
                        'moderation_notes' => $data['moderation_notes'],
                    ]);
                    
                    Notification::make()
                        ->warning()
                        ->title('Property Rejected')
                        ->body("'{$this->record->title}' has been rejected.")
                        ->send();

                    $this->redirect(PropertyModerationResource::getUrl('index'));
                }),

            Actions\Action::make('view_public')
                ->label('View on Site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => route('property.show', ['property' => $this->record->slug]))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Split::make([
                    Section::make('Property Information')
                        ->schema([
                            TextEntry::make('title')
                                ->size('lg')
                                ->weight('bold'),
                            
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('listing_type')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'sale' => 'success',
                                            'rent' => 'info',
                                            'lease' => 'warning',
                                            'shortlet' => 'gray',
                                            default => 'gray',
                                        }),
                                    
                                    TextEntry::make('price')
                                        ->money('NGN'),
                                    
                                    TextEntry::make('status')
                                        ->badge(),
                                ]),
                            
                            TextEntry::make('description')
                                ->prose()
                                ->columnSpanFull(),
                            
                            Grid::make(4)
                                ->schema([
                                    TextEntry::make('bedrooms'),
                                    TextEntry::make('bathrooms'),
                                    TextEntry::make('plot_size')
                                        ->label('Size'),
                                    TextEntry::make('propertyType.name')
                                        ->label('Type'),
                                ]),
                        ])
                        ->grow(),

                    Section::make('Listing Details')
                        ->schema([
                            TextEntry::make('listed_by')
                                ->label('Listed By')
                                ->getStateUsing(function (Property $record): string {
                                    if ($record->agency) {
                                        return 'Agency';
                                    }
                                    if ($record->agent) {
                                        return 'Agent';
                                    }
                                    return 'Property Owner';
                                })
                                ->badge(),

                            TextEntry::make('lister_name')
                                ->label('Name')
                                ->getStateUsing(function (Property $record): string {
                                    if ($record->agency) {
                                        return $record->agency->name;
                                    }
                                    if ($record->agent) {
                                        return $record->agent->display_name;
                                    }
                                    if ($record->owner) {
                                        return $record->owner->full_name;
                                    }
                                    return 'Unknown';
                                }),

                            TextEntry::make('lister_verified')
                                ->label('Verification Status')
                                ->getStateUsing(function (Property $record): string {
                                    $isVerified = false;
                                    if ($record->agency) {
                                        $isVerified = $record->agency->is_verified;
                                    } elseif ($record->agent) {
                                        $isVerified = $record->agent->is_verified;
                                    } elseif ($record->owner) {
                                        $isVerified = $record->owner->is_verified;
                                    }
                                    return $isVerified ? 'Verified' : 'Unverified';
                                })
                                ->badge()
                                ->color(fn (string $state): string => $state === 'Verified' ? 'success' : 'warning'),

                            TextEntry::make('city.name')
                                ->label('City'),

                            TextEntry::make('area.name')
                                ->label('Area'),

                            TextEntry::make('address')
                                ->label('Address'),

                            TextEntry::make('created_at')
                                ->label('Submitted')
                                ->dateTime('M j, Y H:i'),
                        ])
                        ->grow(false),
                ])
                ->from('lg'),
            ]);
    }
}
