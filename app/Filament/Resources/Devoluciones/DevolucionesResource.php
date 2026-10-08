<?php

namespace App\Filament\Resources\Devoluciones;

use App\Filament\Resources\Devoluciones\Pages\CreateDevoluciones;
use App\Filament\Resources\Devoluciones\Pages\EditDevoluciones;
use App\Filament\Resources\Devoluciones\Pages\ListDevoluciones;
use App\Filament\Resources\Devoluciones\Schemas\DevolucionesForm;
use App\Filament\Resources\Devoluciones\Tables\DevolucionesTable;
use App\Models\Devolucione;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DevolucionesResource extends Resource
{
    protected static ?string $model = Devolucione::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $pluralModelLabel = 'Devolucion de Equipos';

    protected static ?string $recordTitleAttribute = 'Devoluciones';

    protected static string | UnitEnum | null $navigationGroup = 'Gestión de Solicitudes';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return DevolucionesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DevolucionesTable::configure($table);
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
            'index' => ListDevoluciones::route('/'),
            'create' => CreateDevoluciones::route('/create'),
            'edit' => EditDevoluciones::route('/{record}/edit'),
        ];
    }
}
