<?php

namespace App\Filament\Resources\Solicitudes\Schemas;

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
use App\Models\Profesore;
use App\Models\Computadora;
use App\Models\Equipo;
use App\Models\Sala;

class SolicitudesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            Section::make('Solicitante')
            ->schema([
                Select::make('profesores_id')
                ->label('Profesor')
                ->searchable()
                ->preload()
                ->relationship('profesore', 'id', function ($query) { 
                    return $query->select('id', 'nombre', 'apellido')
                    ->orderBy('nombre')
                    ->orderBy('apellido');
                })
                ->getOptionLabelFromRecordUsing(fn (Profesore $record) => "{$record->nombre} {$record->apellido}") 
                ->required()
                ->createOptionForm([
                    Section::make('Información del Profesor')
                    ->schema([
                        TextInput::make('nombre')->required()->maxLength(45),
                        TextInput::make('apellido')->maxLength(45)->default(null),
                        TextInput::make('cargo')->required()->maxLength(225),
                        Select::make('carrera_id')
                        ->label('Carrera')
                        ->searchable()
                        ->preload()
                        ->relationship('carrera','carrera')
                        ->required()
                        ->createOptionForm([
                             Section::make()
                            ->schema([
                                TextInput::make('carrera')
                                ->label('Carrera')
                                ->required()
                                ->maxLength(45), 
                            ])->columns(1), 
                        ])
                        ->createOptionModalHeading('Añadir Carrera')
                        ->columns(2), 
                    ])->columns(2), 
                ])
                ->createOptionModalHeading('Añadir Nuevo Profesor'),
                RichEditor::make('observacion') 
                    ->label('Observacion') 
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->default(null), 
            ]), 
    
            Wizard::make([
                Step::make('Equipos Solicitados')
                ->schema([
                    Select::make('equipos_id')
                    ->label('Equipo')
                    //->disabled()
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->relationship('equipo', 'id', function ($query) { 
                        return $query->select('id', 'equipo', 'complemento')
                        ->orderBy('equipo')
                        ->orderBy('complemento');
                    })
                    ->getOptionLabelFromRecordUsing(fn (Equipo $record) => "{$record->equipo} {$record->complemento}"), 
                
                    Select::make('computadoras_id')
                    ->label('Computadora')
                    //->disabled()
                    ->searchable()
                    ->preload()
                    ->relationship('computadora', 'id', function ($query) { 
                        return $query->select('id', 'computadora', 'complemento')
                        ->orderBy('computadora')
                        ->orderBy('complemento');
                    })
                    ->getOptionLabelFromRecordUsing(fn (Computadora $record) => "{$record->computadora} {$record->complemento}"), 
                
                    Grid::make(4)
                    ->schema([
                        TextInput::make('cantidad')
                        ->label('Cantidad')
                        //->disabled()
                        ->numeric()
                        ->minValue(1)
                        ->rules([
                            function ($get, $set): \Closure { 
                                return function (string $attribute, $value, \Closure $fail) use ($get) { 
                                        
                                    $equipoId = $get('equipos_id');
                                    $computadoraId = $get('computadoras_id');
                                    $cantidadSolicitada = (int) $value;
                                        // 1. Verificar si se seleccionó AL MENOS un recurso
                                    if (is_null($equipoId) && is_null($computadoraId)) {
                                        $fail('Debes seleccionar al menos un Equipo o una Computadora para realizar la solicitud.');
                                        return;
                                    }
                                        // 2. Lógica de validación de Stock para EQUIPO
                                    if ($equipoId) {
                                        $equipo = Equipo::find($equipoId); // Usamos el modelo importado
                                        $nombreRecurso = "({$equipo->equipo} " . ($equipo->complemento ?? '') . ")";
                                            
                                        if (!$equipo || $equipo->cantidad <= 0) {
                                            $fail("🚫 Ya no hay existencias disponibles para {$nombreRecurso}.");
                                            return;
                                        }
                                            
                                        if ($cantidadSolicitada > $equipo->cantidad) {
                                            $fail("❌ No hay suficientes Equipos. Solo hay {$equipo->cantidad} Equipos disponibles de {$nombreRecurso}.");
                                            return;
                                        }
                                    }
                                        // 3. Lógica de validación de Stock para COMPUTADORA
                                    if ($computadoraId) {
                                        $computadora = Computadora::find($computadoraId); // Usamos el modelo importado
                                        $nombreRecurso = "({$computadora->computadora} " . ($computadora->complemento ?? '') . ")";
                                            
                                        if (!$computadora || $computadora->cantidad <= 0) {
                                            $fail("🚫 Ya no hay existencias disponibles para {$nombreRecurso}.");
                                            return;
                                        }

                                        if ($cantidadSolicitada > $computadora->cantidad) {
                                            $fail("❌ No hay suficientes Laptops. Solo hay {$computadora->cantidad} Laptop disponibles de {$nombreRecurso}.");
                                            return;
                                        }
                                    }
                                };
                            }
                        ]), 
                    ]),
                    Grid::make(1)
                    ->schema([
                        ToggleButtons::make('status')
                        ->label('Estado')
                        ->options([
                            1 => 'Entregado', 
                            0 => 'Sin Entregar', 
                        ])
                        ->colors([
                            1 => 'success', 
                            0 => 'danger', 
                        ])
                        ->icons([
                            1 => 'heroicon-o-check-circle', 
                            0 => 'heroicon-o-x-circle', 
                        ])
                        ->default(0) 
                        ->inline(), 
                    ]), 
                ])->columns(1), 
                Step::make('Fechas Solicitadas') 
                ->schema([
                    Repeater::make('fechasSolicitadas') 
                    //->label('Fecha')
                    ->relationship('fechasSolicitadas') 
                    ->schema([
                        
                        DatePicker::make('fecha')
                            //->label('Fecha')
                            ->required()
                            ->columnSpan(1)
                            ->minDate(now()) // No permite seleccionar fechas anteriores a la actual
                    ])
                    ->columns(1), 
                ])->columns(1),
            ]), 
    
        ]); 
    }
}
