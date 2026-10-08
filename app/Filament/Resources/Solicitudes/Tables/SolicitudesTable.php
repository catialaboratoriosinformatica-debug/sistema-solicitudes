<?php

namespace App\Filament\Resources\Solicitudes\Tables;

use App\Models\Solicitude;
use App\Models\SolicitudFecha;// Asegúrate de tener tu modelo Solicitud importado
use App\Models\Profesore;
use App\Models\Equipo;
use App\Models\Computadora;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class SolicitudesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

        ])
        
        ->filters([
            ])
        
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([ 
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
