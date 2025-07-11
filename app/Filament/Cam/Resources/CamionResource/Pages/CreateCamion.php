<?php

namespace App\Filament\Cam\Resources\CamionResource\Pages;

use App\Filament\Cam\Resources\CamionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCamion extends CreateRecord
{
    protected static string $resource = CamionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
