<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ViewsRelationManager extends RelationManager
{
    protected static string $relationship = 'views';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                TextInput::make('ip_address')
                    ->required()
                    ->maxLength(45)
                    ->label('IP Address'),
                Textarea::make('user_agent')
                    ->maxLength(65535)
                    ->label('User Agent'),
                TextInput::make('session_id')
                    ->maxLength(255)
                    ->label('Session ID'),
                TextInput::make('referrer')
                    ->maxLength(255),
                Select::make('device_type')
                    ->options([
                        'desktop' => 'Desktop',
                        'mobile' => 'Mobile',
                        'tablet' => 'Tablet',
                    ])
                    ->default('desktop'),
                TextInput::make('browser')
                    ->maxLength(255),
                TextInput::make('platform')
                    ->maxLength(255),
                TextInput::make('country')
                    ->maxLength(255),
                TextInput::make('city')
                    ->maxLength(255),
                DateTimePicker::make('viewed_at')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ip_address')
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->default('Anonymous')
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->sortable(),
                TextColumn::make('device_type')
                    ->label('Device')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'desktop' => 'success',
                        'mobile' => 'warning',
                        'tablet' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('browser')
                    ->sortable(),
                TextColumn::make('platform')
                    ->sortable(),
                TextColumn::make('country')
                    ->sortable(),
                TextColumn::make('referrer')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                TextColumn::make('viewed_at')
                    ->label('Viewed At')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('device_type')
                    ->options([
                        'desktop' => 'Desktop',
                        'mobile' => 'Mobile',
                        'tablet' => 'Tablet',
                    ]),
                Filter::make('authenticated')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_id'))
                    ->label('Authenticated Users Only'),
                Filter::make('anonymous')
                    ->query(fn (Builder $query): Builder => $query->whereNull('user_id'))
                    ->label('Anonymous Views Only'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('viewed_at', 'desc');
    }
}
