<?php

namespace App\Filament\Resources;

use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\PropertyModerationResource\Pages;

class PropertyModerationResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Property Moderation';

    protected static ?string $modelLabel = 'Pending Property';

    protected static ?string $pluralModelLabel = 'Pending Properties';

    protected static string | \UnitEnum | null $navigationGroup = 'Moderation';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'property-moderation';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getEloquentQuery()->count();
        return $count > 0 ? 'warning' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        // Show all properties with pending moderation status
        // Note: is_published may be false because the observer enforces draft status on creation
        // The moderation queue should show pending properties regardless of publish status
        return parent::getEloquentQuery()
            ->where('moderation_status', 'pending')
            ->with(['owner', 'agent', 'agency', 'propertyType', 'city', 'area']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Property Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->disabled(),
                        Forms\Components\TextInput::make('price')
                            ->prefix('₦')
                            ->disabled(),
                        Forms\Components\Select::make('listing_type')
                            ->options([
                                'sale' => 'For Sale',
                                'rent' => 'For Rent',
                                'lease' => 'For Lease',
                                'shortlet' => 'Shortlet',
                            ])
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Moderation')
                    ->schema([
                        Forms\Components\Select::make('moderation_status')
                            ->options([
                                'approved' => 'Approve',
                                'rejected' => 'Reject',
                                'pending' => 'Keep Pending',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('moderation_notes')
                            ->label('Notes')
                            ->placeholder('Add notes about your decision (optional)')
                            ->rows(3),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-property.png'))
                    ->getStateUsing(fn (Property $record) => $record->getFirstMediaUrl('featured', 'thumb') ?: null),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Property $record) => $record->title),

                Tables\Columns\TextColumn::make('price')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('listing_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'success',
                        'rent' => 'info',
                        'lease' => 'warning',
                        'shortlet' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('owner_type')
                    ->label('Listed By')
                    ->getStateUsing(function (Property $record): string {
                        if ($record->agency) {
                            return 'Agency: ' . $record->agency->name;
                        }
                        if ($record->agent) {
                            return 'Agent: ' . $record->agent->display_name;
                        }
                        if ($record->owner) {
                            return 'Owner: ' . $record->owner->full_name;
                        }
                        return 'Unknown';
                    })
                    ->wrap()
                    ->size('sm'),

                Tables\Columns\IconColumn::make('owner_verified')
                    ->label('Verified')
                    ->getStateUsing(function (Property $record): bool {
                        if ($record->agency) {
                            return $record->agency->is_verified;
                        }
                        if ($record->agent) {
                            return $record->agent->is_verified;
                        }
                        if ($record->owner) {
                            return $record->owner->is_verified;
                        }
                        return false;
                    })
                    ->boolean()
                    ->tooltip(fn (Property $record): string => $record->requiresModeration() 
                        ? 'Unverified - Requires Moderation' 
                        : 'Verified Account'),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Location')
                    ->description(fn (Property $record) => $record->area?->name),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Property $record): string => $record->is_published 
                        ? 'Published - Visible to users' 
                        : 'Draft - Not visible'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'rented' => 'warning',
                        'sold' => 'danger',
                        'off_market' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('listing_fee_status')
                    ->label('Fee Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Paid',
                        'waived' => 'Waived',
                        'unpaid' => 'Unpaid',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'waived' => 'info',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('listing_type')
                    ->options([
                        'sale' => 'For Sale',
                        'rent' => 'For Rent',
                        'lease' => 'For Lease',
                        'shortlet' => 'Shortlet',
                    ]),
                Tables\Filters\SelectFilter::make('owner_type')
                    ->label('Listed By')
                    ->options([
                        'owner' => 'Property Owner',
                        'agent' => 'Agent',
                        'agency' => 'Agency',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'], function (Builder $query, string $value) {
                            return match ($value) {
                                'owner' => $query->whereNotNull('owner_id')->whereNull('agent_id')->whereNull('agency_id'),
                                'agent' => $query->whereNotNull('agent_id')->whereNull('agency_id'),
                                'agency' => $query->whereNotNull('agency_id'),
                                default => $query,
                            };
                        });
                    }),
            ])
            ->actions([
                Action::make('approve')
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
                    ->action(function (Property $record, array $data): void {
                        $record->update([
                            'moderation_status' => 'approved',
                            'moderated_at' => now(),
                            'moderated_by' => auth()->id(),
                            'moderation_notes' => $data['moderation_notes'] ?? null,
                            'is_published' => true,
                            'published_at' => now(),
                        ]);
                        
                        Notification::make()
                            ->success()
                            ->title('Property Approved')
                            ->body("'{$record->title}' is now live.")
                            ->send();

                        // TODO: Send notification to property owner
                    }),

                Action::make('reject')
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
                    ->action(function (Property $record, array $data): void {
                        $record->update([
                            'moderation_status' => 'rejected',
                            'moderated_at' => now(),
                            'moderated_by' => auth()->id(),
                            'moderation_notes' => $data['moderation_notes'],
                        ]);
                        
                        Notification::make()
                            ->warning()
                            ->title('Property Rejected')
                            ->body("'{$record->title}' has been rejected.")
                            ->send();

                        // TODO: Send notification to property owner with reason
                    }),

                Action::make('toggle_publish')
                    ->label(fn (Property $record): string => $record->is_published ? 'Unpublish' : 'Publish')
                    ->icon(fn (Property $record): string => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Property $record): string => $record->is_published ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Property $record): string => $record->is_published ? 'Unpublish Property' : 'Publish Property')
                    ->modalDescription(fn (Property $record): string => $record->is_published 
                        ? 'This property will no longer be visible to users.'
                        : 'This property will become visible to users.')
                    ->action(function (Property $record): void {
                        $wasPublished = $record->is_published;
                        $record->update([
                            'is_published' => !$wasPublished,
                            'published_at' => !$wasPublished ? now() : $record->published_at,
                        ]);
                        
                        Notification::make()
                            ->success()
                            ->title($wasPublished ? 'Property Unpublished' : 'Property Published')
                            ->body("'{$record->title}' is now " . ($wasPublished ? 'hidden' : 'visible') . ".")
                            ->send();
                    }),

                Action::make('change_status')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Property Status')
                            ->options([
                                'available' => 'Available',
                                'rented' => 'Rented',
                                'sold' => 'Sold',
                                'under_offer' => 'Under Offer',
                                'off_market' => 'Off Market',
                                'withdrawn' => 'Withdrawn',
                            ])
                            ->required(),
                    ])
                    ->action(function (Property $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                        
                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body("'{$record->title}' status changed to {$data['status']}.")
                            ->send();
                    }),

                Action::make('change_moderation')
                    ->label('Moderation Status')
                    ->icon('heroicon-o-shield-check')
                    ->color('gray')
                    ->form([
                        Forms\Components\Select::make('moderation_status')
                            ->label('Moderation Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('moderation_notes')
                            ->label('Notes')
                            ->rows(2),
                    ])
                    ->action(function (Property $record, array $data): void {
                        $updates = [
                            'moderation_status' => $data['moderation_status'],
                            'moderated_at' => now(),
                            'moderated_by' => auth()->id(),
                            'moderation_notes' => $data['moderation_notes'] ?? null,
                        ];
                        
                        // Also publish if approving
                        if ($data['moderation_status'] === 'approved') {
                            $updates['is_published'] = true;
                            $updates['published_at'] = now();
                        }
                        
                        $record->update($updates);
                        
                        Notification::make()
                            ->success()
                            ->title('Moderation Status Updated')
                            ->body("'{$record->title}' moderation status changed to {$data['moderation_status']}.")
                            ->send();
                    }),

                Action::make('waive_listing_fee')
                    ->label(fn (Property $record): string => $record->listing_fee_status === 'waived' 
                        ? 'Fee Waived ✓' 
                        : 'Waive Listing Fee')
                    ->icon('heroicon-o-gift')
                    ->color(fn (Property $record): string => $record->listing_fee_status === 'waived' 
                        ? 'success' 
                        : 'warning')
                    ->requiresConfirmation()
                    ->modalHeading('Waive Listing Fee')
                    ->modalDescription('This will grant a free listing for this property. The owner will not be charged.')
                    ->hidden(fn (Property $record): bool => in_array($record->listing_fee_status, ['paid', 'waived']))
                    ->action(function (Property $record): void {
                        $record->update([
                            'listing_fee_status' => Property::LISTING_FEE_WAIVED,
                            'is_published' => true,
                            'published_at' => $record->published_at ?? now(),
                        ]);
                        
                        Notification::make()
                            ->success()
                            ->title('Listing Fee Waived')
                            ->body("'{$record->title}' has been granted a free listing.")
                            ->send();
                    }),

                ViewAction::make()
                    ->url(fn (Property $record): string => route('property.show', ['property' => $record->slug]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Selected Properties')
                    ->modalDescription('All selected properties will be made visible.')
                    ->action(function (Collection $records): void {
                        $count = $records->count();
                        
                        $records->each(function (Property $record) {
                            $record->update([
                                'moderation_status' => 'approved',
                                'moderated_at' => now(),
                                'moderated_by' => auth()->id(),
                                'is_published' => true,
                                'published_at' => now(),
                            ]);
                        });
                        
                        Notification::make()
                            ->success()
                            ->title('Properties Approved')
                            ->body("{$count} properties are now live.")
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('bulk_reject')
                    ->label('Reject Selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Selected Properties')
                    ->form([
                        Forms\Components\Textarea::make('moderation_notes')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $count = $records->count();
                        
                        $records->each(function (Property $record) use ($data) {
                            $record->update([
                                'moderation_status' => 'rejected',
                                'moderated_at' => now(),
                                'moderated_by' => auth()->id(),
                                'moderation_notes' => $data['moderation_notes'],
                            ]);
                        });
                        
                        Notification::make()
                            ->warning()
                            ->title('Properties Rejected')
                            ->body("{$count} properties have been rejected.")
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->emptyStateHeading('No pending properties')
            ->emptyStateDescription('All properties have been reviewed. Great job!')
            ->emptyStateIcon('heroicon-o-check-badge');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyModerations::route('/'),
            'view' => Pages\ViewPropertyModeration::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
