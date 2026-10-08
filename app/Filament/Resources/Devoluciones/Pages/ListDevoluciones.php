<?php

namespace App\Filament\Resources\Devoluciones\Pages;

use App\Filament\Resources\Devoluciones\DevolucionesResource;
use App\Filament\Resources\Solicitudes\SolicitudesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListDevoluciones extends ListRecords
{
    protected static string $resource = DevolucionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Action::make('regresar_a_solicitudes') // Un nombre único para esta acción
            ->label('Volver') // La etiqueta que verá el usuario
            ->url(SolicitudesResource::getUrl('index')) // <--- Apunta a la ruta 'index' de LaboratoriosResource
            ->color('danger') // Un color neutral, por ejemplo
            ->icon('heroicon-o-arrow-left'),
        ];
    }
}
