<?php

namespace App\Filament\Resources\InfrastructureDeviceResource\Pages;

use App\Filament\Resources\InfrastructureDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfrastructureDevice extends EditRecord
{
    protected static string $resource = InfrastructureDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
