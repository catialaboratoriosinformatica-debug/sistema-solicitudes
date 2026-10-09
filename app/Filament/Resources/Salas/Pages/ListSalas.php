<?php

namespace App\Filament\Resources\Salas\Pages;

use App\Filament\Resources\Salas\SalasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalas extends ListRecords
{
    protected static string $resource = SalasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Añadir Sala'),
        ];
    }
}
