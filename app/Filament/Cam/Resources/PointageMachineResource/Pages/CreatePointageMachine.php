<?php

namespace App\Filament\Cam\Resources\PointageMachineResource\Pages;

use App\Filament\Cam\Resources\PointageMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePointageMachine extends CreateRecord
{
    protected static string $resource = PointageMachineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
