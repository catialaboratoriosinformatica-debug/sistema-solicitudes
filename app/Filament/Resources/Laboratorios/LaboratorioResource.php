<?php

namespace App\Filament\Resources\Laboratorios;

use App\Filament\Resources\Laboratorios\Pages\CreateLaboratorio;
use App\Filament\Resources\Laboratorios\Pages\EditLaboratorio;
use App\Filament\Resources\Laboratorios\Pages\ListLaboratorios;
use App\Filament\Resources\Laboratorios\Pages\ViewLaboratorio;
use App\Filament\Resources\Laboratorios\Schemas\LaboratorioForm;
use App\Filament\Resources\Laboratorios\Schemas\LaboratorioInfolist;
use App\Filament\Resources\Laboratorios\Tables\LaboratoriosTable;
use App\Models\Laboratorio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LaboratorioResource extends Resource
{
    protected static ?string $model = Laboratorio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Solicitud de Laboratorios';

    public static function form(Schema $schema): Schema
    {
        return LaboratorioForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LaboratorioInfolist::configure($schema);
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
            'create' => CreateLaboratorio::route('/create'),
            'view' => ViewLaboratorio::route('/{record}'),
            'edit' => EditLaboratorio::route('/{record}/edit'),
        ];
    }
}
