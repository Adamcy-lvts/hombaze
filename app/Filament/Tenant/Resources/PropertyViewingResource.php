<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\PropertyViewingResource\Pages;
use App\Filament\Tenant\Resources\PropertyViewingResource\RelationManagers;
use App\Models\PropertyViewing;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Property Viewings';

    protected static ?string $modelLabel = 'Property Viewing';

    protected static ?string $pluralModelLabel = 'Property Viewings';

    protected static ?string $navigationGroup = 'Property Search';

    protected static ?int $navigationSort = 1;

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
