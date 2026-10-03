<?php

namespace App\Filament\Resources\Computadoras\Pages;

use App\Filament\Resources\Computadoras\ComputadorasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageComputadoras extends ManageRecords
{
    protected static string $resource = ComputadorasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Agregar Computadora'), 
        ];
    }
}
