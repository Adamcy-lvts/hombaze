<?php

namespace App\Filament\Landlord\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Landlord\Resources\PropertyInquiryResource\Pages\ListPropertyInquiries;
use App\Filament\Landlord\Resources\PropertyInquiryResource\Pages\CreatePropertyInquiry;
use App\Filament\Landlord\Resources\PropertyInquiryResource\Pages\EditPropertyInquiry;
use App\Filament\Landlord\Resources\PropertyInquiryResource\Pages;
use App\Filament\Landlord\Resources\PropertyInquiryResource\RelationManagers;
use App\Models\PropertyInquiry;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyInquiryResource extends Resource
{
    protected static ?string $model = PropertyInquiry::class;

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
            'index' => ListPropertyInquiries::route('/'),
            'create' => CreatePropertyInquiry::route('/create'),
            'edit' => EditPropertyInquiry::route('/{record}/edit'),
        ];
    }
}
