<?php

namespace App\Filament\Cam\Resources\LocationMachineResource\Pages;

use App\Filament\Cam\Resources\LocationMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocationMachines extends ListRecords
{
    protected static string $resource = LocationMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
