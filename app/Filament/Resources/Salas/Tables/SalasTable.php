<?php

namespace App\Filament\Resources\Salas\Tables;

use App\Filament\Resources\Laboratorios\LaboratoriosResource;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use App\Models\Profesore;
use App\Models\Sala;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;

class SalasTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->query(Sala::query())
        ->columns([
            TextColumn::make('sala')
                ->label('Espacio')
                ->sortable()
                ->searchable(),
            TextColumn::make('disponibilidad')
                ->label('Estado')
                ->formatStateUsing(function ($state, $record): string {
                    $isDisponible = (bool) $state;

                    // 1. DISPONIBLE (true / 1): La sala está libre
                    if ($isDisponible) {
                         return 'DISPONIBLE';
                    }

                    if (! $record->fecha) {
                        return 'OCUPADO';
                    }

                    // Parseamos el día de la solicitud
                    $fechaSolicitud = Carbon::parse($record->fecha)->startOfDay();
                    $hoy = Carbon::now()->startOfDay();

                    // Si la solicitud incluye hora de inicio, la combinamos; si no, comparamos por fecha
                    if (! empty($record->hora_inicio)) {
                        $inicio = Carbon::parse(Carbon::parse($record->fecha)->format('Y-m-d') . ' ' . $record->hora_inicio);
                        $ahora = Carbon::now();

                    if ($ahora->isBefore($inicio)) {
                        return 'APARTADO';
                    }
                    } else {
                        // Comparación estricta por fecha: si hoy es antes de la fecha de la solicitud
                        if ($hoy->isBefore($fechaSolicitud)) {
                            return 'APARTADO';
                        }
                    }

                    // 3. OCUPADO (false / 0): Ya es el día y la hora correspondiente
                    return 'OCUPADO';
                })
                ->color(function ($state, $record): string {
                    $isDisponible = (bool) $state;

                    if ($isDisponible) {
                        return 'success'; // Verde para DISPONIBLE
                    }

                    if (! $record->fecha) {
                        return 'danger';
                    }

                    $fechaSolicitud = Carbon::parse($record->fecha)->startOfDay();
                    $hoy = Carbon::now()->startOfDay();

                    if (! empty($record->hora_inicio)) {
                        $inicio = Carbon::parse(Carbon::parse($record->fecha)->format('Y-m-d') . ' ' . $record->hora_inicio);
                        $ahora = Carbon::now();

                    if ($ahora->isBefore($inicio)) {
                        return 'warning'; // Amarillo para APARTADO
                    }
                    } else {
                        if ($hoy->isBefore($fechaSolicitud)) {
                            return 'warning'; // Amarillo para APARTADO
                        }
                    }

                    return 'danger'; // Rojo para OCUPADO
                }),
            TextColumn::make('proxima_fecha_activa') // Usa el accessor del modelo Sala
                ->label('Fecha')
                ->date('M. d, Y'),
        ])
        ->filters([
            SelectFilter::make('disponibilidad')
                ->options([
                    '1' => 'Disponible',
                    '0' => 'Ocupado',
                ])
                ->label('Filtrar por Estado'),
        ])
        ->recordActions([
            ActionGroup::make([
                ViewAction::make()
                    ->tooltip('Ver Datos de la Sala'),
                EditAction::make()
                    ->tooltip('Editar Datos de la Sala'),
                DeleteAction::make()
                    ->tooltip('Eliminar Sala'),
            ])
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);

    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crear_sala_modal')
                ->label('Añadir Nueva Sala')
                ->color('success') // Opcional: un color que indique 'crear'
                ->icon('heroicon-o-plus') // Opcional: un icono de añadir
                ->modalHeading('Crear Nueva Sala') // Título del modal
                ->components([
                    // --- AQUÍ ESTÁ EL CAMBIO CLAVE: USAR Grid o Columns ---
                    Grid::make(2) // Crea una cuadrícula con 2 columnas
                    ->schema([
                        TextInput::make('sala')
                        ->label('Nombre de Sala')
                        ->required()
                        ->maxLength(155),

                        ToggleButtons::make('disponibilidad')
                        ->label('Estado de Disponibilidad')
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
                        ->inline() // Este es para los botones dentro del toggle, no para el layout general
                        ->required(),
                    ]),
                    // --- FIN DEL CAMBIO ---
                ])
                ->action(function (array $data) {
                    try {
                        DB::beginTransaction();
                        Sala::create($data);
                        DB::commit();

                        Notification::make()
                        ->title('Sala creada correctamente')
                        ->success()
                        ->send();

                    } catch (\Exception $e) {
                        DB::rollBack();
                        Notification::make()
                        ->title('Error al crear la sala')
                        ->body('Hubo un problema al intentar guardar la sala. ' . $e->getMessage())
                        ->danger()
                        ->send();
                    }
                }),
            Action::make('regresar_a_laboratorios') // Un nombre único para esta acción
            ->label('Volver') // La etiqueta que verá el usuario
            ->url(LaboratoriosResource::getUrl('index')) // <--- Apunta a la ruta 'index' de LaboratoriosResource
            ->color('gray') // Un color neutral, por ejemplo
            ->icon('heroicon-o-arrow-left'), 
        ];
    }
}
