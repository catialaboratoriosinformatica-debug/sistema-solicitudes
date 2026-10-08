<?php

namespace App\Filament\Resources\SolicitudFechas;

use App\Filament\Resources\Solicitudes\Pages\CreateSolicitudes;
use App\Filament\Resources\Solicitudes\Pages\EditSolicitudes;
use App\Filament\Resources\SolicitudFechas\Pages\ListSolicitudFechas;
use App\Filament\Resources\SolicitudFechas\Pages\ViewSolicitudFechas;
use App\Filament\Resources\SolicitudFechas\Schemas\SolicitudFechaForm;
use App\Filament\Resources\SolicitudFechas\Tables\SolicitudFechasTable;
use App\Models\SolicitudFecha;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SolicitudFechaResource extends Resource
{
    protected static ?string $model = SolicitudFecha::class;

    protected static ?string $recordTitleAttribute = 'Fechas por Solicitud';

    protected static ?string $pluralModelLabel = 'Solicitud de Equipo';

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
        return SolicitudFechaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SolicitudFechasTable::configure($table);
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
            'index' => ListSolicitudFechas::route('/'),
            'create' => CreateSolicitudes::route('/create'), 
            'edit' => EditSolicitudes::route('/{record}/edit'),
            'view' => ViewSolicitudFechas::route('/{record}'),
        ];
    }
}
