<?php

namespace App\Filament\Cam\Resources\CategorieMachineResource\Pages;

use App\Filament\Cam\Resources\CategorieMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategorieMachine extends CreateRecord
{
    protected static string $resource = CategorieMachineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
