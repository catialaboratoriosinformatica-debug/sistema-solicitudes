<?php

namespace App\Filament\Resources\Laboratorios\Pages;

use App\Filament\Resources\Laboratorios\LaboratoriosResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaboratorios extends CreateRecord
{
    protected static string $resource = LaboratoriosResource::class;

    protected function getRedirectUrl(): string
    {
        
        return LaboratoriosResource::getUrl('index');
    }
}
