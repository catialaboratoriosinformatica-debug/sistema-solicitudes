<?php

namespace App\Filament\Resources\SolicitudFechas\Pages;

use App\Filament\Resources\SolicitudFechas\SolicitudFechaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;


class ListSolicitudFechas extends ListRecords
{
    protected static string $resource = SolicitudFechaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Nueva Solicitud'),
        ];
    }

}
