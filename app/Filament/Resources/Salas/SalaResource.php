<?php

namespace App\Filament\Resources\Salas;

use App\Filament\Resources\Salas\Pages\CreateSala;
use App\Filament\Resources\Salas\Pages\EditSala;
use App\Filament\Resources\Salas\Pages\ListSalas;
use App\Filament\Resources\Salas\Pages\ViewSala;
use App\Filament\Resources\Salas\Schemas\SalaForm;
use App\Filament\Resources\Salas\Schemas\SalaInfolist;
use App\Filament\Resources\Salas\Tables\SalasTable;
use App\Models\Sala;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalaResource extends Resource
{
    protected static ?string $model = Sala::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Salas';

    public static function form(Schema $schema): Schema
    {
        return SalaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalasTable::configure($table);
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
            'index' => ListSalas::route('/'),
            'create' => CreateSala::route('/create'),
            'view' => ViewSala::route('/{record}'),
            'edit' => EditSala::route('/{record}/edit'),
        ];
    }
}
