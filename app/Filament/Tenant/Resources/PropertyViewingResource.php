<?php

namespace App\Filament\Tenant\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Tenant\Resources\PropertyViewingResource\Pages\ListPropertyViewings;
use App\Filament\Tenant\Resources\PropertyViewingResource\Pages\CreatePropertyViewing;
use App\Filament\Tenant\Resources\PropertyViewingResource\Pages\EditPropertyViewing;
use App\Filament\Tenant\Resources\PropertyViewingResource\Pages;
use App\Filament\Tenant\Resources\PropertyViewingResource\RelationManagers;
use App\Models\PropertyViewing;
use App\Models\Property;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PropertyViewingResource extends Resource
{
    protected static ?string $model = PropertyViewing::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Property Viewings';

    protected static ?string $modelLabel = 'Property Viewing';

    protected static ?string $pluralModelLabel = 'Property Viewings';

    protected static string | \UnitEnum | null $navigationGroup = 'Property Search';

    protected static ?int $navigationSort = 1;

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
