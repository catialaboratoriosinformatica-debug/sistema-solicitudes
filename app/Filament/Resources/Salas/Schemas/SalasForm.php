<?php

namespace App\Filament\Resources\Salas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;

class SalasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            TextInput::make('sala')
            ->label('Sala')
            ->required()
            ->maxLength(155),

            ToggleButtons::make('disponibilidad')
            ->label('Disponibilidad')
            ->options([
                1 => 'Disponible', 
                0 => 'Ocupado',   
            ])
            ->colors([
                1 => 'success',
                0 => 'danger',
            ])
            ->icons([
                1 => 'heroicon-o-check-circle',
                0 => 'heroicon-o-x-circle',
            ])
            ->default(1)
            ->inline()
            ->required(),
        ]);
    }
}
