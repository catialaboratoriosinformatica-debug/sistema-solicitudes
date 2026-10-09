<?php

namespace App\Filament\Resources\Salas\Pages;

use App\Filament\Resources\Salas\SalaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSala extends ViewRecord
{
    protected static string $resource = SalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
