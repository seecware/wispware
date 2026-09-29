<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumptionLogResource\Pages;
use App\Filament\Resources\ConsumptionLogResource\RelationManagers;
use App\Models\ConsumptionLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsumptionLogResource extends Resource
{
    protected static ?string $model = ConsumptionLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('queue_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('consumption_gb')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('recorded_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('queue_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('consumption_gb')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recorded_at')
                    ->dateTime()
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
            'index' => Pages\ListConsumptionLogs::route('/'),
            'create' => Pages\CreateConsumptionLog::route('/create'),
            'edit' => Pages\EditConsumptionLog::route('/{record}/edit'),
        ];
    }
}
