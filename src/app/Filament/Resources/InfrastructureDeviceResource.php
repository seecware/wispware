<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfrastructureDeviceResource\Pages;
use App\Filament\Resources\InfrastructureDeviceResource\RelationManagers;
use App\Models\InfrastructureDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InfrastructureDeviceResource extends Resource
{
    protected static ?string $model = InfrastructureDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('brand')
                    ->options(
                        [
                            'TP-Link'=>'TP Link',
                            'Mercusys'=>'Mercusys',
                            'Ubiquiti'=>'Ubiquiti',
                            'Mikrotik'=>'Mikrotik',
                            'Generic FO'=>'Generic Fiber Media Converter',
                            'Starlink'=>'Starlink',
                            'Cisco'=>'Cisco',
                        ]
                    )
                    ->required(),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('serial_number')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('mac_address')
                    ->required()
                    ->rule('mac_address')
                    ->maxLength(17),
                Forms\Components\Select::make('status')
                    ->options(
                        [
                            'New'=>'Installed New',
                            'Used'=>'Used',
                            'Transfer'=>'Installed from other site.'
                        ]
                    )
                    ->required(),
                Forms\Components\TextInput::make('comment')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('photo_path')
                    ->label('Photo Evidence')
                    ->image()
                    ->disk('public')
                    ->imageEditor('false')
                    ->directory('devices-evidence')
                    ->visibility('public')
                    ->maxSize(10240)
                    ->nullable(),
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
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mac_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular()
                    ->size(50)
                    ->checkFileExistence(false)
                    ->defaultImageUrl(url('/images/placeholder-device.png')),
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
            'index' => Pages\ListInfrastructureDevices::route('/'),
            'create' => Pages\CreateInfrastructureDevice::route('/create'),
            'edit' => Pages\EditInfrastructureDevice::route('/{record}/edit'),
        ];
    }
}
