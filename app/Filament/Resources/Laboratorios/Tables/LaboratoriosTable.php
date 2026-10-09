<?php

namespace App\Filament\Resources\Laboratorios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Profesore;
use App\Models\Sala;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class LaboratoriosTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('profesore.nombre') 
                    ->label('Profesor') 
                    ->formatStateUsing(function ($state, $record) { 
                        $apellido = $record->profesore->apellido ?? ''; 
                        return "{$state} {$apellido}"; 
                        })
                    ->sortable() 
                    ->searchable(['profesores.nombre', 'profesores.apellido']),
                TextColumn::make('sala.sala')
                    ->label('Sala Solicitada')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->date()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hora_inicio')
                ->label('Inicio')
                ->time('h:i a'),
                TextColumn::make('hora_final')
                ->label('Culminación')
                ->time('h:i a'),
                TextColumn::make('disponibilidad')
                    ->label('Estado')
                    ->badge()
                     ->formatStateUsing(function (string $state, $record): string {
        
                        $fechaSolicitud = Carbon::parse($record->fecha)->startOfDay();
                         $hoy = Carbon::now()->startOfDay();

                        if ($state === '0' && $hoy->isAfter($fechaSolicitud)) { 
                            return 'CUMPLIDO'; 
                        } 
                        elseif ($state === '0') {
                            return 'OCUPADO';
                        } 
                        elseif ($state === '1') {
                            return 'DISPONIBLE';
                        }
                        return $state;
                    })
                    ->color(function (string $state, $record): string {

                        $fechaSolicitud = Carbon::parse($record->fecha)->startOfDay();
                        $hoy = Carbon::now()->startOfDay();

                        if ($state === '0' && $hoy->isAfter($fechaSolicitud)) {
                            return 'success'; 
                        } elseif ($state === '0') {
                            return 'danger';
                        }  elseif ($state === '1') {
                            return 'success';
                        }
                        return 'gray'; 
                    }),
                //TextColumn::make('observacion')
                //    ->searchable(),
            ])

            ->modifyQueryUsing(function (Builder $query) {
                $hoy = Carbon::now()->format('Y-m-d');

                return $query
                    ->orderByRaw("
                        CASE 
                            -- OCUPADO: disponibilidad '0' y la fecha es hoy o futura
                            WHEN disponibilidad = '0' AND fecha >= '{$hoy}' THEN 1
                            -- CUMPLIDO: disponibilidad '0' y la fecha ya pasó
                            WHEN disponibilidad = '0' AND fecha < '{$hoy}' THEN 2
                            -- DISPONIBLE: disponibilidad '1'
                            WHEN disponibilidad = '1' THEN 3
                            ELSE 4 
                        END ASC
                    ")
                    // Segundo criterio: Ordenar por fecha dentro de cada grupo
                    ->orderBy('fecha', 'asc'); 
            })
            
            ->filters([
                Filter::make('fecha_personalizada')
                    ->schema([
                        DatePicker::make('fecha_desde')
                            ->label('Fecha Desde'),
                        DatePicker::make('fecha_hasta') 
                            ->label('Fecha Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn (Builder $query, $date) => $query->whereDate('fecha', '>=', $date)
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date) => $query->whereDate('fecha', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->tooltip('Ver Solicitud de Sala'),
                    EditAction::make()
                        ->tooltip('Editar Datos de la Solicitud'),
                    DeleteAction::make()
                        ->tooltip('Eliminar Solicitud'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

}
