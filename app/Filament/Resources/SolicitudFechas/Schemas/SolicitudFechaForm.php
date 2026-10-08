<?php

namespace App\Filament\Resources\SolicitudFechas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SolicitudFechaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('solicitudes_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('fecha')
                    ->required(),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}
