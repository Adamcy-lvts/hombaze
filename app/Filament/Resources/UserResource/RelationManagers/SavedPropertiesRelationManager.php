<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SavedPropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'savedProperties';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title . ' - ₦' . number_format($record->price))
                    ->label('Property'),
                Forms\Components\Textarea::make('notes')
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
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => $record->property ? 
                        route('filament.admin.resources.properties.view', $record->property) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('property.listing_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rent' => 'success',
                        'sale' => 'warning',
                        'shortlet' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('property.price')
                    ->label('Price')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.city.name')
                    ->label('Location')
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'rented' => 'warning',
                        'sold' => 'danger',
                        'under_offer' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Saved On')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property.listing_type')
                    ->relationship('property', 'listing_type')
                    ->options([
                        'rent' => 'Rent',
                        'sale' => 'Sale',
                        'shortlet' => 'Shortlet',
                    ])
                    ->label('Listing Type'),
                Tables\Filters\SelectFilter::make('property.status')
                    ->relationship('property', 'status')
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                        'under_offer' => 'Under Offer',
                    ])
                    ->label('Property Status'),
                Tables\Filters\Filter::make('available_only')
                    ->query(fn (Builder $query): Builder => $query->available())
                    ->label('Available Properties Only'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_property')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => $record->property ? 
                        route('filament.admin.resources.properties.view', $record->property) : null)
                    ->openUrlInNewTab()
                    ->label('View Property'),
                Tables\Actions\DeleteAction::make()
                    ->label('Remove from Saved'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Remove from Saved'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
