<?php

namespace App\Filament\Resources\Salas\Pages;

use App\Filament\Resources\Salas\SalasResource;
use App\Filament\Resources\Laboratorio\LaboratoriosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListSalas extends ListRecords
{
    protected static string $resource = SalasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Añadir Sala'),

            Action::make('regresar_a_laboratorios') // Un nombre único para esta acción
            ->label('Volver') // La etiqueta que verá el usuario
            ->url(LaboratoriosResource::getUrl('index')) // <--- Apunta a la ruta 'index' de LaboratoriosResource
            ->color('danger') // Un color neutral, por ejemplo
            ->icon('heroicon-o-arrow-left'),
        ];
    }
}
