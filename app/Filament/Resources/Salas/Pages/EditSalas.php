<?php

namespace App\Filament\Resources\Salas\Pages;

use App\Filament\Resources\Salas\SalasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalas extends EditRecord
{
    protected static string $resource = SalasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
