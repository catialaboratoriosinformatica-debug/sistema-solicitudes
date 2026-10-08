<?php

namespace App\Filament\Resources\Devoluciones\Pages;

use App\Filament\Resources\Devoluciones\DevolucionesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDevoluciones extends CreateRecord
{
    protected static string $resource = DevolucionesResource::class; 

    protected function getRedirectUrl(): string
    {
        
        return DevolucionesResource::getUrl('index');
    }
}
