<?php

namespace App\Filament\Resources\ConsumptionLogResource\Pages;

use App\Filament\Resources\ConsumptionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsumptionLogs extends ListRecords
{
    protected static string $resource = ConsumptionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
