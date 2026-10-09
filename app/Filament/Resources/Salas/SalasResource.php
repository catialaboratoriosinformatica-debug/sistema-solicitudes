<?php

namespace App\Filament\Resources\Salas;

use App\Filament\Resources\Salas\Pages\CreateSalas;
use App\Filament\Resources\Salas\Pages\EditSalas;
use App\Filament\Resources\Salas\Pages\ListSalas;
use App\Filament\Resources\Salas\Schemas\SalasForm;
use App\Filament\Resources\Salas\Tables\SalasTable;
use App\Models\Sala;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SalasResource extends Resource
{
    protected static ?string $model = Sala::class;

    protected static ?string $pluralModelLabel = 'Gestión de Laboratorios';

    //protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Salas';

    public static function shouldRegisterNavigation(): bool
    {
    // Esto hace que NO aparezca en el menú lateral.
        return false;
    }

    public static function canViewAny(): bool
    {
    // Devuelve true para permitir el acceso (resolviendo el 403)
        return true;
    }      

    public static function getNavigationGroup(): ?string
    {
        return null; 
    }

    public static function form(Schema $schema): Schema
    {
        return SalasForm::configure($schema);
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
            'create' => CreateSalas::route('/create'),
            'edit' => EditSalas::route('/{record}/edit'),
        ];
    }
}
