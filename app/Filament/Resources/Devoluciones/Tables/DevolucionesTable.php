<?php

namespace App\Filament\Resources\Devoluciones\Tables;

use App\Filament\Resources\Solicitudes\SolicitudesResource;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action; // Importar la clase Action
use Filament\Notifications\Notification;
use App\Models\Devolucione;

class DevolucionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
                // 4. FECHA DE DEVOLUCIÓN (Campo propio del modelo Devolucion)
            TextColumn::make('fecha_devolucion')
            ->label('Fecha Devolucion')
            ->date('d/M/Y')
            ->sortable(),

            TextColumn::make('solicitudFecha.solicitude.profesore.nombre')
            ->label('Profesor')
            ->description(fn (Devolucione $record): string => 
                $record->solicitudFecha?->solicitude?->profesore?->apellido ?? 'N/A'
            )
            ->searchable(query: function (Builder $query, string $search): Builder {
                        // Búsqueda en la relación anidada para el nombre del profesor
                return $query->whereHas('solicitudFecha.solicitude.profesore', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%");
                });
            })
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: false),
                
            TextColumn::make('equipo_principal')
            ->label('Equipo')
            ->description(fn(Devolucione $record): string =>
                $record->solicitudFecha?->solicitude?->equipo?->complemento ?? 'No fue solicitado'
            )
            ->getStateUsing(function (Devolucione $record) {
                $solicitud = $record->solicitudFecha?->solicitude;
                        
                if ($solicitud?->equipos_id) {
                            // Muestra Equipo y Complemento
                    return $solicitud->equipo->equipo . '';
                }
                return 'No fue Solicitado';
            })
            ->color('info')
            ->wrap()
            ->sortable(false),
                    
                // 2B. COMPUTADORA SOLICITADA
            TextColumn::make('computadora_solicitada')
            ->label('Computadora')
            ->description(fn(Devolucione $record): string =>
                $record->solicitudFecha?->solicitude?->computadora?->complemento ?? 'No fue Solicitado'
            )
            ->getStateUsing(function (Devolucione $record) {
                $solicitud = $record->solicitudFecha?->solicitude;
                        
                if ($solicitud?->computadoras_id) {
                            // Muestra Computadora y Complemento
                    return $solicitud->computadora->computadora . '';
                }
                return 'No fue Solicitado';
            })
            ->color('warning')
            ->wrap()
            ->sortable(false),

                // 3. CANTIDAD
                //TextColumn::make('solicitudFecha.solicitude.cantidad')
                //    ->label('Cant.')
                //    ->numeric()
                //    ->sortable(),

                // 5. ESTADO DEL EQUIPO DEVUELTO (Usando IconColumn para el ToggleButtons)
            TextColumn::make('status')
            ->label('Estado')
            ->badge()
                    // Lógica para determinar el color (usa la misma lógica de colores que tu ToggleButtons)
            ->color(fn (int $state): string => match ($state) {
                1 => 'success', // ÓPTIMO
                0 => 'danger',  // DETERIORADO
                default => 'secondary',
            })
                    // Lógica para mostrar el texto del estado
            ->formatStateUsing(fn (int $state): string => match ($state) { 
                1 => 'ÓPTIMO',
                0 => 'DETERIORADO',
                default => 'N/A',
            })
            ->sortable(),
        ])
        ->filters([
                //
        ])
        ->recordActions([
            ActionGroup::make([
                ViewAction::make()
                ->tooltip('Ver Datos de la Devolucion'),
                //EditAction::make()
                //->tooltip('Editar Datos de la Solicitud'),
                DeleteAction::make()
                ->tooltip('Eliminar Devolucion'),
            ]),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(), 
            ]),
        ]);
    }
}
