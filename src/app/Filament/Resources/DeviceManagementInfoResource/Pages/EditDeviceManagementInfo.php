<?php

namespace App\Filament\Resources\DeviceManagementInfoResource\Pages;

use App\Filament\Resources\DeviceManagementInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeviceManagementInfo extends EditRecord
{
    protected static string $resource = DeviceManagementInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
