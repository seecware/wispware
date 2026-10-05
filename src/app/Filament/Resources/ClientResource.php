<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('f_lastname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_ammount')
                    ->label('Payment ammount')
                    ->prefix('$ ')
                    ->placeholder('400.00')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('m_lastname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unified_sys_id')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('assigned_speed_kb')
                    ->required()
                    ->maxLength(4),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(10),
                Forms\Components\FileUpload::make('photo_path')
                    ->label('Client Photo')
                    ->image()
                    ->disk('public')
                    ->imageEditor('false')
                    ->directory('clients-photo')
                    ->visibility('public')
                    ->maxSize(10240)
                    ->nullable(),
                Forms\Components\TextInput::make('notes')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('signup_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Client')
                    ->circular()
                    ->size(50)
                    ->checkFileExistence(false)
                    ->defaultImageUrl(url('/images/placeholder-client.png'))
                    ->extraImgAttributes([
        'class' => 'transition-transform duration-300 ease-in-out hover:scale-150 hover:z-10 relative cursor-pointer',
    ]),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('f_lastname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('m_lastname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('signup_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
