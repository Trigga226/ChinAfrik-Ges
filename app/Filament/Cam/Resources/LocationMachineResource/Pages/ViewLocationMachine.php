<?php

namespace App\Filament\Cam\Resources\LocationMachineResource\Pages;

use App\Filament\Cam\Resources\LocationMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLocationMachine extends ViewRecord
{
    protected static string $resource = LocationMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
