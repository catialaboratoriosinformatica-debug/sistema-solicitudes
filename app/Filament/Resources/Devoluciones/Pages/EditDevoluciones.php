<?php

namespace App\Filament\Resources\Devoluciones\Pages;

use App\Filament\Resources\Devoluciones\DevolucionesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDevoluciones extends EditRecord
{
    protected static string $resource = DevolucionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
