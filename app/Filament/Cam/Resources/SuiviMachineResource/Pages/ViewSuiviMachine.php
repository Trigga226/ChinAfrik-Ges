<?php

namespace App\Filament\Cam\Resources\SuiviMachineResource\Pages;

use App\Filament\Cam\Resources\SuiviMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSuiviMachine extends ViewRecord
{
    protected static string $resource = SuiviMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
