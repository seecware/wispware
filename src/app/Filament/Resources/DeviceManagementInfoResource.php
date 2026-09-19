<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceManagementInfoResource\Pages;
use App\Filament\Resources\DeviceManagementInfoResource\RelationManagers;
use App\Models\DeviceManagementInfo;
use App\Models\InfrastructureDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceManagementInfoResource extends Resource
{
    protected static ?string $model = DeviceManagementInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dev_id')
                    ->relationship('infrastructureDevice', 'serial_number')
                    ->searchable(['serial_number', 'mac_address'])
                    ->getOptionLabelFromRecordUsing(
                        fn (
                            InfrastructureDevice $record)=>
                            "{$record->serial_number} - {$record->mac_address}"
                    )
                    ->preload(false)
                    ->required(),
                Forms\Components\TextInput::make('ip_management_address')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('default_management_page')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('default_username')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('default_password')
                    ->password()
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('default_ssid')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('default_ssid_pw')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('notes')
                    ->required()
                    ->maxLength(200),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dev_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_management_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_management_page')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_ssid')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_ssid_pw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListDeviceManagementInfos::route('/'),
            'create' => Pages\CreateDeviceManagementInfo::route('/create'),
            'edit' => Pages\EditDeviceManagementInfo::route('/{record}/edit'),
        ];
    }
}
