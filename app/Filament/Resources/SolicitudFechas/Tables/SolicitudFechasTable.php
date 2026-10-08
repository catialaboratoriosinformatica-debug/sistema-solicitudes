<?php

namespace App\Filament\Resources\SolicitudFechas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action; // Importar la clase Action
use Filament\Notifications\Notification;
use App\Models\SolicitudFecha;
use App\Filament\Resources\Devoluciones\DevolucionesResource;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

use Filament\Forms\Components\RichEditor;

use Filament\Forms\Components\ToggleButtons;
// Importa también tus modelos para que las relaciones funcionen
use App\Models\Equipo;
use App\Models\Computadora;
use App\Models\Profesore;

class SolicitudFechasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('solicitude.profesore.nombre')
                ->label('Profesor')
                ->formatStateUsing(fn ($state, $record) => 
                    "{$record->solicitude->profesore->nombre} {$record->solicitude->profesore->apellido}"
                )
                ->searchable(['solicitude.profesore.nombre', 'solicitude.profesore.apellido'])
                ->sortable(),

            // --- DATOS DEL EQUIPO (Relación: solicitud -> equipo) ---
            TextColumn::make('solicitude.equipo.equipo') 
                ->label('Equipo')
                ->searchable(['solicitude.equipo.equipo', 'solicitude.equipo.complemento'])
                ->sortable()
                ->description(fn ($record) => $record->solicitude->equipo?->complemento)
                ->placeholder('No solicitado'), // Muestra esto si el equipo es nulo

            // --- DATOS DE LA COMPUTADORA (Relación: solicitud -> computadora) ---
            TextColumn::make('solicitude.computadora.computadora')
                ->label('Computadora')
                ->searchable(['solicitude.computadora.computadora', 'solicitude.computadora.complemento'])
                ->sortable()
                ->description(fn ($record) => $record->solicitude->computadora?->complemento)
                ->placeholder('No solicitado'), // Muestra esto si la computadora es nula
            
            // --- FECHA Y STATUS (Campos directos del modelo SolicitudFecha) ---

            // 1. Fecha (Variable)
            TextColumn::make('fecha')
                ->label('Fecha Solicitada')
                ->date('d/m/Y') // Formato de fecha
                ->sortable()
                ->extraAttributes(['class' => 'font-bold']), // Para destacarla
                
            // 2. Estado (Status)
            TextColumn::make('status')
                ->label('Estado')
                ->badge()
                     // Lógica para determinar el color
                ->color(function (int $state, SolicitudFecha $record): string {
        
                    // 🛑 CLAVE CORREGIDA: Usamos la relación directa 'devolucion()'
                    $isReturned = $record->devolucion()->exists();

                     // Prioridad 1: DEVUELTO (warning/amarillo)
                    if ($isReturned) {
                        return 'warning';
                    } 
        
                        // Prioridad 2: ENTREGADO (success/verde) - (asumimos status = 1)
                    if ($state === 1) {
                        return 'success';
                    } 
        
                        // Prioridad 3: SIN ENTREGAR (danger/rojo) - (asumimos status = 0)
                    if ($state === 0) {
                        return 'danger';
                    }

                    return 'secondary'; // Estado desconocido
                })
                    // Lógica para mostrar el texto del estado
                ->formatStateUsing(function (int $state, SolicitudFecha $record): string { 
        
                     // 🛑 CLAVE CORREGIDA: Usamos la relación directa 'devolucion()'
                    $isReturned = $record->devolucion()->exists();

                        // El texto "DEVUELTO" tiene la máxima prioridad
                    if ($isReturned) {
                        return 'DEVUELTO';
                    } 
        
                        // El resto se basa en el campo 'status'
                    if ($state === 1) { 
                        return 'ENTREGADO';
                    } 
        
                    if ($state === 0) {
                        return 'SIN ENTREGAR';
                    }
        
                    return (string) $state; 
                }),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                ->leftJoin('devoluciones', 'solicitud_fechas.id', '=', 'devoluciones.solicitud_fechas_id')
                ->select('solicitud_fechas.*')
                    // 1. Primero mantenemos el orden de prioridad de los estados
                 ->orderByRaw("
                    CASE 
                        WHEN devoluciones.id IS NOT NULL THEN 3   -- DEVUELTOS (Al final)
                        WHEN solicitud_fechas.status = 1 THEN 1   -- ENTREGADOS (Primero)
                        WHEN solicitud_fechas.status = 0 THEN 2   -- SIN ENTREGAR (Segundo)
                        ELSE 4 
                    END ASC
                ")
                    // 2. Segundo criterio: Ordenar por fecha de forma ascendente (más antigua a más reciente)
                    // Esto organizará a los "SIN ENTREGAR" por su fecha de solicitud.
                ->orderBy('fecha', 'asc'); 
            })
            
            ->filters([

                SelectFilter::make('profesore_filter')
                    ->label('Filtrar por Profesor')
                    // La relación correcta: 'solicitude' (del hijo al padre), luego 'profesore' (del padre al profesor)
                    ->relationship('solicitude.profesore', 'nombre', fn (Builder $query) => $query->orderBy('nombre')->orderBy('apellido'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombre} {$record->apellido}")
                    ->searchable()
                    ->preload(),

                SelectFilter::make('estado_prestamo')
                    ->label('Filtrar por Estado')
                    ->options([
                        'ENTREGADO' => 'Entregado (Pendiente Devolver)',
                        'SIN_ENTREGAR' => 'Sin Entregar', 
                        'DEVUELTO' => 'Devuelto',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        if (!$value) {
                            return $query;
                        }

                        // El $query base ya está sobre 'solicitud_fechas'
                        switch ($value) {
                            case 'ENTREGADO':
                                // status = 1 (Entregado) Y la solicitud padre aún NO tiene devolución
                                return $query->where('status', 1)
                                             ->whereDoesntHave('solicitudes.devoluciones');

                            case 'SIN_ENTREGAR':
                                // status = 0 (Sin Entregar)
                                return $query->where('status', 0);

                            case 'DEVUELTO':
                                // status = 1 (Entregado) Y la solicitud padre TIENE una devolución
                                return $query->where('status', 1)
                                             ->whereHas('solicitudes.devoluciones');
                        }

                        return $query;
                    }),

                Filter::make('fecha')
                ->Form([
                    DatePicker::make('fecha_desde')
                        ->label('Desde la fecha')
                        ->placeholder(fn ($state): string => 'Escribe una fecha'), // Texto de ayuda
                    DatePicker::make('fecha_hasta')
                        ->label('Hasta la fecha')
                        ->placeholder(fn ($state): string => 'Escribe una fecha'), // Texto de ayuda
                ])
                ->query(function (Builder $query, array $data): Builder {
                    // La lógica de la consulta se ejecuta cuando el filtro se aplica
                    
                    return $query
                        // 1. Filtrar si se proporciona 'fecha_desde'
                        ->when(
                            $data['fecha_desde'],
                            fn (Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                        )
                        // 2. Filtrar si se proporciona 'fecha_hasta'
                        ->when(
                            $data['fecha_hasta'],
                            fn (Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                        );
                }),
            ])
            ->recordActions([

                Action::make('redirigirADevolucion')
                    ->label('Devolver')
                    ->tooltip('Devolver Equipos')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning')
                    
                    // 1. Redirección y Parámetros
                    // NOTA: Cambié "DevolucionesResource" a "DevolucionResource" (singular)
                    ->url(fn (SolicitudFecha $record): string => DevolucionesResource::getUrl('create', [
                        'solicitud_fechas_id' => $record->id, 
                    ]))
                    ->openUrlInNewTab(false)
                    
                    // 2. Lógica de Ocultamiento (CORREGIDA)
                    ->hidden(fn (SolicitudFecha $record): bool => 
                        !$record->status // Ocultar si NO es true
                        || $record->devolucion()->exists() 
                    ),

                ActionGroup::make([
                    ViewAction::make()
                        ->tooltip('Ver Solicitud de Equipo'),
                    Action::make('editarFila')
                        ->label('Editar')
                        ->icon('heroicon-o-pencil-square')
                        ->color('info')
                        ->modalHeading('Editar Solicitud Individual')
                        ->modalWidth('4xl') // Un poco más ancho para que quepa bien el formulario
                        ->fillForm(fn (SolicitudFecha $record): array => [
                                // Cargamos datos del hijo (SolicitudFecha)
                            'fecha' => $record->fecha,
                            'status' => $record->status,
                                // Cargamos datos del padre (Solicitude)
                'profesores_id' => $record->solicitude->profesores_id,
        'observacion' => $record->solicitude->observacion,
        'equipos_id' => $record->solicitude->equipos_id,
        'computadoras_id' => $record->solicitude->computadoras_id,
        'cantidad' => $record->solicitude->cantidad,
    ])
    ->form([
        Section::make('Información del Solicitante')
            ->schema([
                Select::make('profesores_id')
                    ->label('Profesor')
                    ->relationship('solicitude.profesore', 'nombre') // Ajustado a la relación del hijo
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombre} {$record->apellido}")
                    ->searchable()
                    ->preload()
                    ->required(),
                RichEditor::make('observacion')
                    ->label('Observación')
                    ->columnSpanFull(),
            ])->columns(2),

        Section::make('Equipos y Estado')
            ->schema([
                Grid::make(3)
                    ->schema([
                        Select::make('equipos_id')
                            ->label('Equipo')
                            ->relationship('solicitude.equipo', 'equipo')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->equipo} {$record->complemento}")
                            ->searchable()
                            ->preload(),
                        Select::make('computadoras_id')
                            ->label('Computadora')
                            ->relationship('solicitude.computadora', 'computadora')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->computadora} {$record->complemento}")
                            ->searchable()
                            ->preload(),
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),
                    ]),
                
                Grid::make(2)
                    ->schema([
                        DatePicker::make('fecha')
                            ->label('Fecha de esta Solicitud')
                            ->required(),
                        ToggleButtons::make('status')
                            ->label('Estado de Entrega')
                            ->options([
                                1 => 'Entregado',
                                0 => 'Sin Entregar',
                            ])
                            ->colors([1 => 'success', 0 => 'danger'])
                            ->icons([1 => 'heroicon-o-check-circle', 0 => 'heroicon-o-x-circle'])
                            ->inline(),
                    ]),
            ]),
    ])
    ->action(function (SolicitudFecha $record, array $data): void {
        // 1. Actualizamos los datos del registro individual (Hijo)
        $record->update([
            'fecha' => $data['fecha'],
            'status' => $data['status'],
        ]);

        // 2. Actualizamos los datos del registro principal (Padre)
        $record->solicitude->update([
            'profesores_id' => $data['profesores_id'],
            'observacion' => $data['observacion'],
            'equipos_id' => $data['equipos_id'],
            'computadoras_id' => $data['computadoras_id'],
            'cantidad' => $data['cantidad'],
        ]);

        Notification::make()
            ->title('Registro actualizado correctamente')
            ->success()
            ->send();
    })
    ->hidden(fn (SolicitudFecha $record): bool => $record->devolucion()->exists()),
                    DeleteAction::make()
                    ->tooltip('Eliminar Solicitud')
                    ->hidden(fn (SolicitudFecha $record): bool => $record->devolucion()->exists())
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar Solicitud')
                    ->modalDescription('¿Está seguro de que desea eliminar esta solicitud? Esta acción es irreversible.')
                    ->color('danger'),
                    
                    Action::make('marcarComoEntregado')
                    ->label('Entregar') 
                    ->icon('heroicon-o-check-badge') 
                    ->color('success') 
                    ->tooltip('Marcar como Entregado')
                    ->requiresConfirmation() 
                    
                    // Lógica: Cambia el status a 1 (Entregado)
                    ->action(function (SolicitudFecha $record) { 
                        // Usamos 1, el valor booleano (true) que Filament requiere
                        $record->update(['status' => 1]); 
                        
                        Notification::make()
                            ->title('Entrega Confirmada')
                            ->body("El día *{$record->fecha->format('d/m/Y')}* ha sido marcado como ENTREGADO.")
                            ->success()
                            ->send();
                    })
                    
                    // Visibilidad: Solo si el status es 0 (Sin Entregar)
                    ->visible(fn (SolicitudFecha $record): bool => $record->status == 0),
                // 
                //
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
