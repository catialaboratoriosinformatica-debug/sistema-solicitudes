<?php

namespace App\Filament\Resources\Laboratorios\Schemas;

use Filament\Schemas\Schema;
use App\Models\Profesore;
use App\Models\Sala;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

class LaboratoriosForm
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
                                        ->maxLength(45)
                                ])->columns(1)
                            ])->createOptionModalHeading('Añadir Carrera')->columns(2)
                        ])->columns(2),
                    ])->createOptionModalHeading('Añadir Nuevo Profesor'),
                ]),
                Wizard::make([
                    Step::make('Laboratorio')
                    ->schema([
                        Section::make()
                        ->contained(false) 
                        ->schema([
                            Select::make('salas_id')
                            ->label('Sala Solicitada')
                            ->searchable()
                            ->preload()
                            ->relationship('sala', 'sala') // Sin Closure si no filtra
                            ->required()
                            ->reactive() 
                            ->afterStateUpdated(function ($state) {
                                $sala = Sala::find($state);
                                if ($sala) {
                                    $sala->disponibilidad = 0; 
                                    $sala->save(); 
                                }
                            })
                            ->createOptionForm([
                            Section::make()
                                ->schema([
                                    TextInput::make('sala')->label('Sala')->required()->maxLength(45),
                                    ToggleButtons::make('disponibilidad')
                                        ->label('Disponibilidad')
                                        ->options([1 => 'Disponible', 0 => 'Ocupado'])
                                        ->colors([1 => 'success', 0 => 'danger'])
                                        ->icons([1 => 'heroicon-o-check-circle', 0 => 'heroicon-o-x-circle'])
                                        ->default(1)
                                        ->inline()
                                        ->required(),
                                ])->columns(2)
                            ])->createOptionModalHeading('Añadir Nueva Sala'),
                        ]),
                        DatePicker::make('fecha')
                        ->label('Fecha de Solicitud')
                        ->required()
                        ->minDate(now()),
                        TimePicker::make('hora_inicio')
                        ->label('Hora de Inicio')
                        ->required()
                        ->seconds(false)
                        ->displayFormat('h:i A'),
                        TimePicker::make('hora_final')
                        ->label('Hora de Culminación')
                        ->seconds(false)
                        ->displayFormat('h:i A')
                        ->required(), // Cierra el Section
                    ])->columns(2), // Cierra el Step::make('Laboratorio')
    
                    Step::make('Descripción') // 🌟 ESTE ES EL TERCER PASO
                    ->schema([
                        ToggleButtons::make('disponibilidad')
                        ->label('Disponibilidad')
                        ->disabled(fn (string $operation): bool => $operation === 'edit')
                        ->options([1 => 'Disponible', 0 => 'Ocupado'])
                        ->colors([1 => 'success', 0 => 'danger'])
                        ->icons([1 => 'heroicon-o-check-circle', 0 => 'heroicon-o-x-circle'])
                        ->default(0) // Usaste 0, lo mantengo.
                        ->inline()
                        ->required(),
                        RichEditor::make('observacion')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->default(null),
                    ]), // Cierra el Step::make('Descripción')
                ]),
                
            ]);
    }
}
