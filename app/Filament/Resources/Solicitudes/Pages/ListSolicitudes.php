<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Livewire\SolicitudesSinEntregar;

class ListSolicitudes extends ListRecords
{
    protected static string $resource = SolicitudesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Nueva Solicitud'),
        ];
    }
}
