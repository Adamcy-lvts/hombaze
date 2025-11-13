<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SavedPropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'savedProperties';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title . ' - ₦' . number_format($record->price))
                    ->label('Property'),
                Textarea::make('notes')
                    ->maxLength(65535)
                    ->placeholder('Add personal notes about this property...')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('property.title')
            ->columns([
                TextColumn::make('property.title')
                    ->label('Property')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => $record->property ? 
                        route('filament.admin.resources.properties.view', $record->property) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('property.listing_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rent' => 'success',
                        'sale' => 'warning',
                        'shortlet' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('property.price')
                    ->label('Price')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('property.city.name')
                    ->label('Location')
                    ->sortable(),
                TextColumn::make('property.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'rented' => 'warning',
                        'sold' => 'danger',
                        'under_offer' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('notes')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                TextColumn::make('created_at')
                    ->label('Saved On')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('property.listing_type')
                    ->relationship('property', 'listing_type')
                    ->options([
                        'rent' => 'Rent',
                        'sale' => 'Sale',
                        'shortlet' => 'Shortlet',
                    ])
                    ->label('Listing Type'),
                SelectFilter::make('property.status')
                    ->relationship('property', 'status')
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                        'under_offer' => 'Under Offer',
                    ])
                    ->label('Property Status'),
                Filter::make('available_only')
                    ->query(fn (Builder $query): Builder => $query->available())
                    ->label('Available Properties Only'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('view_property')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => $record->property ? 
                        route('filament.admin.resources.properties.view', $record->property) : null)
                    ->openUrlInNewTab()
                    ->label('View Property'),
                DeleteAction::make()
                    ->label('Remove from Saved'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Remove from Saved'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
