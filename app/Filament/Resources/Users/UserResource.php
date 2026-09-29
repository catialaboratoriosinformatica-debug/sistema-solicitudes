<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

     protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $recordTitleAttribute = 'Usuarios';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static string | UnitEnum | null $navigationGroup = 'Personal';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Usuario')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('surnames')
                        ->label('Apellido')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('cargo')
                        ->label('Cargo')
                        ->required()
                        ->maxLength(255),
                ])->columns(1),
                Section::make('Cuenta del Usuario')
                ->schema([
                    TextInput::make('email')
                        ->label('Correo Electrónico')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // ENCRIPTA LA CLAVE
                        ->dehydrated(fn ($state) => filled($state)) // No sobrescribe si queda vacío al editar
                        ->required(fn (string $context): bool => $context === 'create'),
                 ])->columns(1)
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
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
            ->recordTitleAttribute('Usuarios')
            ->columns([
                TextColumn::make('name_surnames')
                    ->label('Nombre y Apellido')
                    ->getStateUsing(fn (User $record): string => $record->name . ' ' . $record->surnames)
                    ->searchable(['name', 'surnames']),
                TextColumn::make('cargo')
                    ->label('Cargo')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo Electronico')
                    ->searchable(),
                //TextColumn::make('email_verified_at')
                //    ->dateTime()
                //    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ManageUsers::route('/'),
        ];
    }
}
