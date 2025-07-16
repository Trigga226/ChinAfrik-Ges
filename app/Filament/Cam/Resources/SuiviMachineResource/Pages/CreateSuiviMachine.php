<?php

namespace App\Filament\Cam\Resources\SuiviMachineResource\Pages;

use App\Filament\Cam\Resources\SuiviMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSuiviMachine extends CreateRecord
{
    protected static string $resource = SuiviMachineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
