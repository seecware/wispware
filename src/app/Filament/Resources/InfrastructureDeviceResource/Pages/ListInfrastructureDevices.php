<?php

namespace App\Filament\Resources\InfrastructureDeviceResource\Pages;

use App\Filament\Resources\InfrastructureDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfrastructureDevices extends ListRecords
{
    protected static string $resource = InfrastructureDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
