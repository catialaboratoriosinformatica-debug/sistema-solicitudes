<?php

namespace App\Filament\Resources\Profesores;

use App\Filament\Resources\Profesores\Pages\ManageProfesores;
use App\Models\Profesore;
use App\Models\Carrera;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ActionGroup;
use UnitEnum;

class ProfesoreResource extends Resource
{
    protected static ?string $model = Profesore::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'Profesores';

    protected static string | UnitEnum | null $navigationGroup = 'Personal';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(45),
                TextInput::make('apellido')
                    ->maxLength(45)
                    ->default(null),
                TextInput::make('cargo')
                    ->required()
                    ->maxLength(225),
                Select::make('carrera_id')
                    ->label('Carrera')
                    ->searchable()
                    ->preload()
                    ->relationship('carrera','carrera')
                    ->required()
                    ->createOptionForm([
                        TextInput::make('carrera')
                            ->label('Carrera')
                            ->required()
                            ->maxLength(45)
                    ])->createOptionModalHeading('Añadir Carrera')->columns(2) 
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('apellido')
                    ->placeholder('-'),
                TextEntry::make('cargo'),
                TextEntry::make('carrera_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Profesores')
            ->columns([
                TextColumn::make('nombre_apellido')
                    ->label('Nombre y Apellido')
                    ->getStateUsing(fn (Profesore $record): string => $record->nombre . ' ' . $record->apellido)
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('nombre', $direction)->orderBy('apellido', $direction))
                    ->searchable(['nombre', 'apellido']),
                TextColumn::make('cargo')
                    ->searchable(),
                TextColumn::make('carrera.carrera')
                    ->label('Carrera')
                    ->sortable(),
            ])
            ->filters([
                //
                SelectFilter::make('carrera_id')
                    ->label('Carrera')
                    ->options(Carrera::all()->pluck('carrera', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver')
                        ->tooltip('Ver Datos del Profesor'),
                    EditAction::make()
                        ->label('Editar')
                        ->tooltip('Editar Datos del Profesor'),
                    DeleteAction::make()
                        ->label('Eliminar')
                        ->tooltip('Eliminar Profesor'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProfesores::route('/'),
        ];
    }
}
