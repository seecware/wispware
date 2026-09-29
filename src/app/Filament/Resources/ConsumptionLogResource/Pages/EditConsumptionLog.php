<?php

namespace App\Filament\Resources\ConsumptionLogResource\Pages;

use App\Filament\Resources\ConsumptionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsumptionLog extends EditRecord
{
    protected static string $resource = ConsumptionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
