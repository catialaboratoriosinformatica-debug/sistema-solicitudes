<?php

namespace App\Filament\Resources\Laboratorios\Pages;

use App\Filament\Resources\Laboratorios\LaboratoriosResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use App\Models\Profesore;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Route;

class ViewLaboratorios extends ViewRecord
{
    protected static string $resource = LaboratoriosResource::class;
    protected static ?string $title = 'Detalles de la Solicitud';

    public function infolist(Schema $schema): Schema
    {
    return $schema
        ->schema([
            Section::make('Solicitante')
            ->inlineLabel()
            ->schema([
                TextEntry::make('profesore.nombre') 
                ->label('Profesor')
                ->getStateUsing(function (Model $record) {
                    $profesor = $record->profesore;
                    if ($profesor) {
                        return "{$profesor->nombre} {$profesor->apellido}";
                    }
                    return 'N/A';
                }),
                TextEntry::make('observacion')
                ->label('Propósito del Uso')
                ->html() 
                ->columnSpanFull()
                ->placeholder('Sin descripción de uso proporcionada.'),
            ])->columns(2),
            
             Section::make('Solicitud de Laboratorio')
            ->inlineLabel()
            ->schema([
                TextEntry::make('sala.sala')
                ->label('Sala Solicitada'),
                TextEntry::make('fecha')
                ->label('Fecha de Solicitud')
                ->date('d/m/Y'),
                TextEntry::make('hora_inicio')
                ->label('Inicio')
                ->time('h:i A'),
                TextEntry::make('hora_final')
                ->label('Culminación')
                ->time('h:i A'),
                TextEntry::make('disponibilidad')
                ->label('Disponibilidad')
                ->formatStateUsing(fn (int $state): string => match ($state) {
                    1 => 'Disponible',
                    0 => 'Ocupado',
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
                }),
            ])->columns(1),
        ]);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('volver')
            ->label('Volver')
            ->icon('heroicon-o-arrow-left')
            ->color('danger')
            ->url(fn (): string => LaboratoriosResource::getUrl('index')),
        ];
    }
}
