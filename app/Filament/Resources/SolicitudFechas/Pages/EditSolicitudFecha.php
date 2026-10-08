<?php

namespace App\Filament\Resources\SolicitudFechas\Pages;

use App\Filament\Resources\SolicitudFechas\SolicitudFechaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudFecha extends EditRecord
{
    protected static string $resource = SolicitudFechaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
