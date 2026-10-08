<?php

namespace App\Filament\Resources\SolicitudFechas\Pages;

use App\Filament\Resources\Solicitudes\SolicitudesResource;
use App\Filament\Resources\SolicitudFechas\SolicitudFechaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\DevolucionResource;
use App\Models\SolicitudFecha; 
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Components\Icon;

class ViewSolicitudFechas extends ViewRecord
{
    protected static string $resource = SolicitudFechaResource::class;

    protected function getHeaderActions(): array
    {  
        return [
            Action::make('volver')
            ->label('Volver')
            ->icon('heroicon-o-arrow-left')
            ->color('danger')
            ->url(fn (): string => SolicitudFechaResource::getUrl('index')),
        ];
            // Acción para volver al listado
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
        ->schema([
                // SECCIÓN 1: SOLICITANTE
            Section::make('Información del Solicitante')
            ->inlineLabel()
            ->schema([
                        // Profesor
                TextEntry::make('solicitude.profesore.nombre') 
                ->label('Profesor')
                ->getStateUsing(function (Model $record) {
                            // Acceso: SolicitudFecha -> Solicitud -> Profesore
                    $profesor = $record->solicitude?->profesore;
                    if ($profesor) {
                        // Devolvemos el nombre completo concatenado
                        return "{$profesor->nombre} {$profesor->apellido}";
                    }
                    return 'N/A';
                })
                ->beforeLabel(Icon::make('heroicon-m-user-circle')),
                            // 2. Cargo
                TextEntry::make('solicitude.profesore.cargo')
                ->label('Cargo')
                ->beforeLabel(Icon::make('heroicon-m-briefcase'))
                ->placeholder('No especificado'),
                            
                            // 3. Carrera (Relación Carreras)
                TextEntry::make('solicitude.profesore.carrera.carrera')
                ->label('Carrera')
                ->beforeLabel(Icon::make('heroicon-m-academic-cap'))
                ->placeholder('N/A'),
            ])->columns(1),

                // SECCIÓN 3: EQUIPOS Y COMPONENTES
            Section::make('Equipos y Componentes Solicitados')
            //->inlineLabel()
            ->schema([
                        // Equipo
                TextEntry::make('solicitude.equipo.equipo') // La ruta debe ser completa para el TextEntry
                ->label('Equipo Principal')
                ->getStateUsing(function (Model $record): string {
                            // *** CORRECCIÓN CRÍTICA: Acceso a través de $record->solicitude ***
                    $equipo = $record->solicitude?->equipo; 
                
                    if ($equipo) {
                        return "{$equipo->equipo} " . ($equipo->complemento ? "({$equipo->complemento})" : '');
                    }
                    return 'No se seleccionó equipo principal.';
                })
                ->beforeLabel(Icon::make('heroicon-m-video-camera'))
                ->color(fn (Model $record) => $record->solicitude?->equipo ? null : 'primary'),

                    // Computadora
                TextEntry::make('solicitude.computadora.computadora') // La ruta debe ser completa para el TextEntry
                ->label('Laptop')
                ->getStateUsing(function (Model $record): string {
                            // *** CORRECCIÓN CRÍTICA: Acceso a través de $record->solicitude ***
                    $comp = $record->solicitude?->computadora;
                
                    if ($comp) {
                    return "{$comp->computadora} " . ($comp->complemento ? "({$comp->complemento})" : '');
                    }
                    return 'No se seleccionó computadora.';
                })
                ->beforeLabel(Icon::make('heroicon-m-computer-desktop'))
                ->color(fn (Model $record) => $record->solicitude?->computadora ? null : 'primary'),

                TextEntry::make('solicitude.cantidad')
                ->label('Cantidad Solicitada')
                ->numeric(0)
                ->color('info'),
                            
                        // Estado de Entrega (tomado de la fecha específica)
                TextEntry::make('status')
                ->label('Estado de Entrega')
                ->formatStateUsing(fn (int $state): string => match ($state) {
                    1 => 'Entregado',
                    0 => 'Sin Entregar',
                    default => 'Desconocido', 
                })
                ->color(fn (int $state): string => match ($state) {
                    1 => 'success',
                    0 => 'danger',
                    default => 'gray',
                })
                ->icon(fn (int $state): string => match ($state) {
                    1 => 'heroicon-o-check-circle',
                    0 => 'heroicon-o-x-circle',
                    default => 'heroicon-o-question-mark-circle',
                })
                ->badge(),
                        
            ])->columns(2),

                    // SECCIÓN 2: DETALLES DE LA SOLICITUD Y ESTADO
            Section::make('Detalles de la Entrega')
            //->inlineLabel()
            ->schema([
                        // Fecha Específica
                TextEntry::make('fecha')
                ->label('Fecha del Préstamo')
                ->date('d/m/Y')
                ->color('info'),

                             // Campo para observaciones (no está en tu form, pero es útil para contexto)
                TextEntry::make('solicitude.observacion')
                ->label('Requisitos')
                ->html() 
                ->placeholder('Sin descripción de uso proporcionada.')
                ->beforeLabel(Icon::make('heroicon-m-newspaper'))
                            //->size(TextEntry\TextEntrySize::Small)
                ->columnSpanFull()            
            ])->columns(3),

        ]);
    }
}
