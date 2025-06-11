<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ViewsRelationManager extends RelationManager
{
    protected static string $relationship = 'views';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('ip_address')
                    ->required()
                    ->maxLength(45)
                    ->label('IP Address'),
                Forms\Components\Textarea::make('user_agent')
                    ->maxLength(65535)
                    ->label('User Agent'),
                Forms\Components\TextInput::make('session_id')
                    ->maxLength(255)
                    ->label('Session ID'),
                Forms\Components\TextInput::make('referrer')
                    ->maxLength(255),
                Forms\Components\Select::make('device_type')
                    ->options([
                        'desktop' => 'Desktop',
                        'mobile' => 'Mobile',
                        'tablet' => 'Tablet',
                    ])
                    ->default('desktop'),
                Forms\Components\TextInput::make('browser')
                    ->maxLength(255),
                Forms\Components\TextInput::make('platform')
                    ->maxLength(255),
                Forms\Components\TextInput::make('country')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('viewed_at')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ip_address')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->default('Anonymous')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_type')
                    ->label('Device')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'desktop' => 'success',
                        'mobile' => 'warning',
                        'tablet' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('browser')
                    ->sortable(),
                Tables\Columns\TextColumn::make('platform')
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->sortable(),
                Tables\Columns\TextColumn::make('referrer')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('viewed_at')
                    ->label('Viewed At')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('device_type')
                    ->options([
                        'desktop' => 'Desktop',
                        'mobile' => 'Mobile',
                        'tablet' => 'Tablet',
                    ]),
                Tables\Filters\Filter::make('authenticated')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_id'))
                    ->label('Authenticated Users Only'),
                Tables\Filters\Filter::make('anonymous')
                    ->query(fn (Builder $query): Builder => $query->whereNull('user_id'))
                    ->label('Anonymous Views Only'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('viewed_at', 'desc');
    }
}
