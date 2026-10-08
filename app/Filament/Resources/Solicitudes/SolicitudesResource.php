<?php

namespace App\Filament\Resources\Solicitudes;

use App\Filament\Resources\Solicitudes\Pages\CreateSolicitudes;
use App\Filament\Resources\Solicitudes\Pages\EditSolicitudes;
use App\Filament\Resources\Solicitudes\Pages\ListSolicitudes;
use App\Filament\Resources\Solicitudes\Schemas\SolicitudesForm;
use App\Filament\Resources\Solicitudes\Tables\SolicitudesTable;
use App\Filament\Resources\SolicitudFechas\Pages\ListSolicitudFechas;
use App\Models\Solicitude;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SolicitudesResource extends Resource
{
    protected static ?string $model = Solicitude::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $pluralModelLabel = 'Solicitud de Equipos';

    protected static ?string $recordTitleAttribute = 'Solicitud de Equipos';

    protected static string | UnitEnum | null $navigationGroup = 'Gestión de Solicitudes';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SolicitudesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SolicitudesTable::configure($table);
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
            'index' =>ListSolicitudFechas::route('/'),
            //'index' => ListSolicitudes::route('/'),
            //'create' => CreateSolicitudes::route('/create'),
            'edit' => EditSolicitudes::route('/{record}/edit'),
        ];
    }
}
