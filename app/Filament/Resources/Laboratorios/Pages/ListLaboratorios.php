<?php

namespace App\Filament\Resources\Laboratorios\Pages;

use App\Filament\Resources\Laboratorios\LaboratoriosResource;
use App\Filament\Resources\Salas\SalasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;


class ListLaboratorios extends ListRecords
{
    protected static string $resource = LaboratoriosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->Label('Nueva Solicitud'),
            Action::make('ir_a_salas') // Dale un nombre único a la acción
                ->label('Gestión de Salas') // Etiqueta que verá el usuario en el botón
                ->url(
                    // Utilizamos el método getUrl() del SalasResource para obtener la URL de su página de índice ('index').
                    SalasResource::getUrl('index')
                )
                ->color('info') // Opcional: Dale un color al botón
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }
}
