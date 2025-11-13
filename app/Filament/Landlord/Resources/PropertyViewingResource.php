<?php

namespace App\Filament\Landlord\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Landlord\Resources\PropertyViewingResource\Pages\ListPropertyViewings;
use App\Filament\Landlord\Resources\PropertyViewingResource\Pages\CreatePropertyViewing;
use App\Filament\Landlord\Resources\PropertyViewingResource\Pages\EditPropertyViewing;
use App\Filament\Landlord\Resources\PropertyViewingResource\Pages;
use App\Filament\Landlord\Resources\PropertyViewingResource\RelationManagers;
use App\Models\PropertyViewing;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyViewingResource extends Resource
{
    protected static ?string $model = PropertyViewing::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListPropertyViewings::route('/'),
            'create' => CreatePropertyViewing::route('/create'),
            'edit' => EditPropertyViewing::route('/{record}/edit'),
        ];
    }
}
