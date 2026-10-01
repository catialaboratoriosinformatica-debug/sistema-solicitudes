<?php

namespace App\Filament\Resources\Profesores\Pages;

use App\Filament\Resources\Profesores\ProfesoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProfesores extends ManageRecords
{
    protected static string $resource = ProfesoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Agregar Profesor'),
        ];
    }
}
