<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudesResource;
use App\Filament\Resources\SolicitudFechas\SolicitudFechaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSolicitudes extends CreateRecord
{
    protected static string $resource = SolicitudesResource::class;

    protected function getRedirectUrl(): string
    {
        // 1. Obtener la URL del Resource de SolicitudFecha
        // Ahora sí utiliza la ruta de clase correcta (SolicitudFechaResource::class)
        return SolicitudFechaResource::getUrl('index');
    }
}
