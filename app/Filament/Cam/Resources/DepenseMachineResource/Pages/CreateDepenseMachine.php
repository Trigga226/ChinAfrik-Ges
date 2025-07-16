<?php

namespace App\Filament\Cam\Resources\DepenseMachineResource\Pages;

use App\Filament\Cam\Resources\DepenseMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepenseMachine extends CreateRecord
{
    protected static string $resource = DepenseMachineResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
