<?php

namespace App\Filament\Resources\Devoluciones\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Carbon\Carbon;
use App\Models\Profesore;
use App\Models\Computadora;
use App\Models\Equipo;
use App\Models\Sala;
use App\Models\Devolucione;
use App\Models\Solicitude;
use App\Models\SolicitudFecha;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection; // Renombrado para evitar conflicto
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Icon;


class DevolucionesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            Section::make()
                    // La función anónima nos permite acceder al $record y a los parámetros de la URL
            ->schema(function (?Devolucione $record, Get $get) {
                        
                        // 🛑 CLAVE A: Obtener el ID de la Solicitud (padre) y el ID de la Fecha (fila)
                $solicitudId = $record?->solicitudes_id ?? request()->query('solicitudes_id');
                        // Usamos 'solicitud_fechas_id' ya que viene de la URL (botón "Devolver")
                $solicitudFechaId = $record?->solicitud_fechas_id ?? request()->query('solicitud_fechas_id'); 
                        
                $solicitud = null;
                $solicitudFecha = null;
                        
                        // 1. Buscar la SolicitudFecha específica
                if ($solicitudFechaId) {
                    $solicitudFecha = \App\Models\SolicitudFecha::find($solicitudFechaId);
                            
                            // Si encontramos la fecha, aseguramos el $solicitudId usando su relación.
                    if ($solicitudFecha) {
                        $solicitudId = $solicitudFecha->solicitudes_id;
                    }
                }

                        // 2. Buscar la Solicitud padre
                if ($solicitudId) {
                    $solicitud = Solicitude::find($solicitudId); // Usar Solicitudes si ese es tu modelo
                }

                        // Define los campos del formulario
                $fields = [
                    Hidden::make('solicitudes_id')
                    ->default($solicitudId)
                    ->required()
                    ->rules(['exists:solicitudes,id']),

                    Hidden::make('solicitud_fechas_id')
                    ->default($solicitudFechaId)
                    ->required()
                    ->rules(['exists:solicitud_fechas,id']),
                            // SECCIÓN 2: Datos de la Solicitud de Préstamo (Datos de Contexto - Solo Lectura)
                            // Usamos una función separada que devuelve un Section con TextInputs deshabilitados.
                    static::getSolicitudInfoSection($solicitud, $solicitudFecha),
                ];
                        
                return $fields;
            }),

            Section::make('Devolucion de Equipos')
            ->schema([
                Hidden::make('fecha_devolucion')
                ->label('Fecha de Devolución')
                ->default(now()->toDateString()),

                TextInput::make('fecha_devolucion_display')
                ->label('Fecha devolución')
                ->formatStateUsing(fn (?string $state, ?Devolucione $record) => $record?->fecha_devolucion ? Carbon::parse($record->fecha_devolucion)->format('M. d, Y') : Carbon::now()->format('M. d, Y'))
                ->disabled()
                ->dehydrated(false)
                ->inlineLabel(),
                                        
                ToggleButtons::make('status')
                ->label('Estado del equipo devuelto')
                ->options([
                    1 => 'ÓPTIMO',
                    0 => 'DETERIORADO',
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
                                        
                RichEditor::make('observacion')
                ->maxLength(255)
                ->columnSpanFull()
                ->default(null)
                               
            ]),
        ]);
    }

    protected static function getSolicitudInfoSection(?Solicitude $solicitud, ?SolicitudFecha $solicitudFecha): Section
    {
        return Section::make('Datos de la Solicitud')
        ->schema([
                // 🛑 CLAVE C: Mostrar la fecha ESPECÍFICA que se está devolviendo
            TextEntry::make('solicitudes_fecha')
            ->label('Fecha de la Solicitud')
            ->default(function () use ($solicitudFecha) {
                if ($solicitudFecha) {
                            // Muestra la fecha de la fila específica de SolicitudFecha
                    return Carbon::parse($solicitudFecha->fecha)->locale('es')->isoFormat('D MMM, YYYY');
                }
                return 'Fecha no encontrada';
            })
            ->beforeLabel(Icon::make('heroicon-m-calendar-days'))
            ->inlineLabel(),
                    
                // Información de la Solicitud Padre
            TextEntry::make('solicitudes_nombre')
            ->label('Profesor')
                    // 🛑 Uso del operador Nullsafe (?->) para proteger la lectura
            ->default(fn () => $solicitud ? 
                $solicitud->profesore?->nombre . ' ' . $solicitud->profesore?->apellido 
                : 'N/A'
            )
            ->beforeLabel(Icon::make('heroicon-m-user-circle'))
            ->inlineLabel(),
                    
            TextEntry::make('solicitudes_cargo')
            ->label('Cargo')
            ->default(fn () => $solicitud ? $solicitud->profesore?->cargo : 'N/A')
            ->beforeLabel(Icon::make('heroicon-m-briefcase'))
            ->inlineLabel(),
                    
            TextEntry::make('solicitudes_carrera')
            ->label('Carrera')
                    // 🛑 Uso del operador Nullsafe (?->) en cadena
            ->default(fn () => $solicitud ? $solicitud->profesore?->carrera?->carrera : 'N/A')
            ->beforeLabel(Icon::make('heroicon-m-academic-cap'))
            ->inlineLabel(),
                    
            TextEntry::make('solicitudes_equipo')
            ->label('Equipo Solicitado')
                    // 🛑 Uso del operador Nullsafe (?->)
            ->default(fn () => $solicitud ? ($solicitud->equipo ? $solicitud->equipo->equipo . ' ' . $solicitud->equipo->complemento : 'No fue Solicitado') : 'No fue Solicituda')
            ->beforeLabel(Icon::make('heroicon-m-video-camera'))
            ->inlineLabel(),
                    
            TextEntry::make('solicitudes_computadora')
            ->label('Computadora Solicitada')
                    // 🛑 Uso del operador Nullsafe (?->)
            ->default(fn () => $solicitud ? ($solicitud->computadora ? $solicitud->computadora->computadora . ' ' . $solicitud->computadora->complemento : 'No fue Solicitado') : 'No fue Solicitado')
            ->beforeLabel(Icon::make('heroicon-m-computer-desktop'))
            ->inlineLabel(),

            TextEntry::make('solicitudes_cantidad')
            ->label('Cantidad Solicitada')
            ->default(fn () => $solicitud ? ($solicitud->cantidad ? : 'N/A') : 'N/A')
            ->numeric(0)
            ->color('info')
            ->beforeLabel(Icon::make('heroicon-m-square-3-stack-3d'))
            ->inlineLabel(),

            TextEntry::make('solicitudes_observacion')  
                ->label('Requisitos')
                ->html() 
                ->inlineLabel()
                ->default(fn () => $solicitud ? ($solicitud->observacion ? : '') : '')
                ->placeholder('Sin descripción de uso proporcionada.')
                ->beforeLabel(Icon::make('heroicon-m-newspaper'))
                            //->size(TextEntry\TextEntrySize::Small)
                ->columnSpanFull()
        ])->columns(1);
    }
}
