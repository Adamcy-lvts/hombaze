<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\PropertyViewingResource\Pages;
use App\Filament\Landlord\Resources\PropertyViewingResource\RelationManagers;
use App\Models\PropertyViewing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyViewingResource extends Resource
{
    protected static ?string $model = PropertyViewing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListPropertyViewings::route('/'),
            'create' => Pages\CreatePropertyViewing::route('/create'),
            'edit' => Pages\EditPropertyViewing::route('/{record}/edit'),
        ];
    }
}
