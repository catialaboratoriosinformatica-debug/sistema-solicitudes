<?php

namespace App\Filament\Resources\Laboratorios;

use App\Filament\Resources\Laboratorios\Pages\CreateLaboratorios;
use App\Filament\Resources\Laboratorios\Pages\EditLaboratorios;
use App\Filament\Resources\Laboratorios\Pages\ListLaboratorios;
use App\Filament\Resources\Laboratorios\Schemas\LaboratoriosForm;
use App\Filament\Resources\Laboratorios\Tables\LaboratoriosTable;
use App\Filament\Resources\Laboratorios\Pages\ListSalas;
use App\Filament\Resources\Laboratorios\Pages\CreateSalas;
use App\Models\Laboratorio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon; 
use Filament\Tables\Table;
use Filament\Support\Enums\Width;
use UnitEnum;

class LaboratoriosResource extends Resource
{
    protected static ?string $model = Laboratorio::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $pluralModelLabel = 'Solicitud de Laboratorios';

    protected static string | UnitEnum | null $navigationGroup = 'Gestión de Solicitudes';

    protected static ?string $recordTitleAttribute = 'Laboratorios';

    protected static ?int $navigationSort = 3;

    

    public static function form(Schema $schema): Schema
    {
        return LaboratoriosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaboratoriosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaboratorios::route('/'),
            'create' => CreateLaboratorios::route('/create'),
            'edit' => EditLaboratorios::route('/{record}/edit'),
            'view' => Pages\ViewLaboratorios::route('/{record}'), 
            //'salas' => Pages\ListSalas::route('/salas'),
            //'salas_create' => Pages\CreateSalas::route('/salas/create'),
        ];
    }
}








/*public function table(Table $table): Table
    {
        return $table
        ->query(Sala::query())

        ->components([
            TextColumn::make('sala')
            ->Label('Espacio')
            ->sortable()
            ->searchable(),
            TextColumn::make('disponibilidad')
            ->Label('Estado')
            ->formatStateUsing(fn (string $state): string => match ($state) {
                '1' => 'DISPONIBLE',
                '0' => 'OCUPADO',
                default => $state,
            })
            ->colors(fn (string $state): string => match ($state) {
                '1' => 'success',
                '0' => 'danger',
                default => 'gray',
            }),
            TextColumn::make('proxima_fecha_activa')
            ->label('Fecha')
            ->date('M. d, Y')
        ])

        ->Filters([
            SelectFilter::make('disponibilidad')
            ->options([
                '1' => 'Disponible',
                '0' => 'Ocupado',
            ])
            ->label('Filtrar por Estado')
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

    protected function getHeaderActions(): array
    {
        return[
            Action::make('crear_sala_modal')
            ->label('Añadir Sala')
            ->color('success')
            ->icon('heroicon-o-plus')
            ->modalHeading('Nueva Sala')
            ->components([
                Grid::make(2)
                ->schema([
                    TextInput::make('sala')
                    ->label('Nombre de la Sala')
                    ->required()
                    ->maxlength(155),
                    ToggleButtons::make('disponibilidad')
                    ->label('Estado de la Sala')
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
                    ->defaullt(1)
                    ->inline()
                    ->required()
                ])
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
    }*/
