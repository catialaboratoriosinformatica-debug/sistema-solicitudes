<?php

namespace App\Filament\Resources\Equipos;

use App\Filament\Resources\Equipos\Pages\ManageEquipos;
use App\Models\Equipo;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
//use Filament\Support\Icons\Heroicon;

class EquiposResource extends Resource
{
    protected static ?string $model = Equipo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $recordTitleAttribute = 'Equipos';

    protected static string | UnitEnum | null $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('equipo')
                    ->required()
                    ->maxLength(225),
                TextInput::make('complemento')
                    ->maxLength(225)
                    ->default(null),
                TextInput::make('cantidad')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('equipos'),
                TextEntry::make('complemento'),
                TextEntry::make('cantidad'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Equipos')
            ->columns([
                TextColumn::make('equipo')
                    ->searchable(),
                TextColumn::make('complemento')
                    ->searchable(),
                TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
               //
            ])
            ->recordActions([
                 ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver')
                        ->tooltip('Ver Datos del Equipo'),
                    EditAction::make()
                        ->label('Editar')
                        ->tooltip('Editar Datos del Equipo'),
                    DeleteAction::make()
                        ->label('Eliminar')
                        ->tooltip('Eliminar Equipo'),
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
            'index' => ManageEquipos::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
