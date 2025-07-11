<?php

namespace App\Filament\Cam\Resources\MachineResource\Pages;

use App\Filament\Cam\Resources\MachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMachine extends ViewRecord
{
    protected static string $resource = MachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
