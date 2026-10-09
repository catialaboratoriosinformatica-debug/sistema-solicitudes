<?php

namespace App\Filament\Resources\Laboratorios\Pages;

use App\Filament\Resources\Laboratorios\LaboratorioResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLaboratorio extends EditRecord
{
    protected static string $resource = LaboratorioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
