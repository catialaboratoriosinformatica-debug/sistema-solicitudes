<?php

namespace App\Filament\Resources\Laboratorios\Pages;

use App\Filament\Resources\Laboratorios\LaboratoriosResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLaboratorios extends EditRecord
{
    protected static string $resource = LaboratoriosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
