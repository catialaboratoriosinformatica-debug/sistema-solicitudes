<?php

namespace App\Filament\Resources\Salas\Pages;

use App\Filament\Resources\Salas\SalasResource;
use App\Filament\Resources\LaboratoriosResource;
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

            Action::make('regresar_a_laboratorios')
                ->label('Regresar a Laboratorios')
                ->url(LaboratoriosResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'), 
        ];
    }
}
