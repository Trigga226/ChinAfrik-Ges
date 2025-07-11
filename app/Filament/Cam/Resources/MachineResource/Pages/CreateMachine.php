<?php

namespace App\Filament\Cam\Resources\MachineResource\Pages;

use App\Filament\Cam\Resources\MachineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMachine extends CreateRecord
{
    protected static string $resource = MachineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
