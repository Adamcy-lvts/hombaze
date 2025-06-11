<?php

namespace App\Filament\Agent\Resources;

use App\Filament\Agent\Resources\ReviewResource\Pages;
use App\Filament\Agent\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationLabel = 'Reviews';
    
    protected static ?string $modelLabel = 'Review';
    
    protected static ?string $pluralModelLabel = 'Reviews';
    
    protected static ?int $navigationSort = 4;

    /**
     * Scope queries to only show reviews for the current agent
     */
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        return parent::getEloquentQuery()
            ->where('reviewable_type', 'App\\Models\\User')
            ->where('reviewable_id', $user->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Review Details')
                    ->schema([
                        TextInput::make('reviewer_name')
                            ->label('Reviewer Name')
                            ->disabled()
                            ->maxLength(255),
                        
                        TextInput::make('reviewer_email')
                            ->label('Reviewer Email')
                            ->disabled()
                            ->email()
                            ->maxLength(255),
                        
                        Select::make('rating')
                            ->label('Rating')
                            ->options([
                                1 => '1 Star',
                                2 => '2 Stars',
                                3 => '3 Stars',
                                4 => '4 Stars',
                                5 => '5 Stars',
                            ])
                            ->disabled(),
                        
                        Textarea::make('comment')
                            ->label('Review Comment')
                            ->disabled()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        
                        Textarea::make('response')
                            ->label('Your Response')
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->helperText('Respond to this review to show professionalism'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reviewer_name')
                    ->label('Reviewer')
                    ->searchable()
                    ->sortable(),
                
                BadgeColumn::make('rating')
                    ->label('Rating')
                    ->colors([
                        'danger' => 1,
                        'warning' => 2,
                        'primary' => 3,
                        'info' => 4,
                        'success' => 5,
                    ])
                    ->formatStateUsing(fn (string $state): string => $state . ' ★'),
                
                TextColumn::make('comment')
                    ->label('Review')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                BadgeColumn::make('response')
                    ->label('Response Status')
                    ->getStateUsing(fn (Review $record): string => $record->response ? 'Responded' : 'Pending')
                    ->colors([
                        'success' => 'Responded',
                        'warning' => 'Pending',
                    ]),
                
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),
                
                SelectFilter::make('has_response')
                    ->label('Response Status')
                    ->options([
                        'responded' => 'Responded',
                        'pending' => 'Pending Response',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'responded',
                                fn (Builder $query): Builder => $query->whereNotNull('response'),
                            )
                            ->when(
                                $data['value'] === 'pending',
                                fn (Builder $query): Builder => $query->whereNull('response'),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Respond'),
            ])
            ->bulkActions([
                // No bulk actions for reviews
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'view' => Pages\ViewReview::route('/{record}'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
