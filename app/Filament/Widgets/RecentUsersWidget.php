<?php

namespace App\Filament\Widgets;

use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class RecentUsersWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user_type')
                    ->badge()
                    ->label('Type')
                    ->colors([
                        'danger' => 'super_admin',
                        'success' => 'agency_owner',
                        'warning' => 'agent',
                        'info' => 'property_owner',
                        'primary' => 'tenant',
                    ]),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn(User $record): string => route('filament.admin.resources.users.view', $record)),
            ]);
    }
}
