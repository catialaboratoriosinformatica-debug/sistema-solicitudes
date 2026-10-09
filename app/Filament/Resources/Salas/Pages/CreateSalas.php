<?php

namespace App\Filament\Resources\Salas\Pages;

use App\Filament\Resources\Salas\SalasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalas extends CreateRecord
{
    protected static string $resource = SalasResource::class;

    protected function getRedirectUrl(): string
    {
        
        return SalasResource::getUrl('index');
    }
}
