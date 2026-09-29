<?php

namespace App\Filament\Resources\ConsumtionLogResource\Pages;

use App\Filament\Resources\ConsumtionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsumtionLogs extends ListRecords
{
    protected static string $resource = ConsumtionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
