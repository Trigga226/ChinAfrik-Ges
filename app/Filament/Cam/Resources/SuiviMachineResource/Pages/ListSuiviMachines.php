<?php

namespace App\Filament\Cam\Resources\SuiviMachineResource\Pages;

use App\Filament\Cam\Resources\SuiviMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuiviMachines extends ListRecords
{
    protected static string $resource = SuiviMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
