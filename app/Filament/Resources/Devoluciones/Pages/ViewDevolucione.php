<?php

namespace App\Filament\Resources\Devoluciones\Pages;

use App\Filament\Resources\Devoluciones\DevolucioneResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDevolucione extends ViewRecord
{
    protected static string $resource = DevolucioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
